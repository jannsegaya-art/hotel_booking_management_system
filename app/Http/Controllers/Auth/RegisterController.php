<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role'     => 'required|in:staff,customer',
            'phone'    => 'nullable|string|max:20',
        ], [
            'email.unique'       => 'This email is already registered. Please login instead.',
            'password.min'       => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Passwords do not match.',
        ]);

        // Staff accounts need admin approval; customers are immediately active
        $status = $request->role === 'staff' ? 'pending' : 'active';

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
            'phone'    => $request->phone,
            'status'   => $status,
        ]);

        ActivityLog::create([
            'user_id'     => $user->id,
            'action'      => 'register',
            'description' => $user->name . ' registered as ' . $user->role,
            'ip_address'  => $request->ip(),
        ]);

        if ($request->role === 'staff') {
            // Notify all admins about new staff registration
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'title'   => 'New Staff Registration',
                    'message' => $user->name . ' has registered as staff and is awaiting your approval.',
                    'type'    => 'info',
                ]);
            }

            // Staff → redirect to login with pending message
            return redirect()->route('login')
                ->with('success', 'Registration successful! Your staff account is pending admin approval. Please wait for admin to approve your account before logging in.');
        }

        // Customer → DO NOT auto-login. Redirect to login page with success message.
        // This way customers must log in manually, which is the expected flow.
        Notification::create([
            'user_id' => $user->id,
            'title'   => 'Welcome to Grand Royal Hotel!',
            'message' => 'Thank you for registering, ' . $user->name . '. You can now log in and book our rooms.',
            'type'    => 'success',
        ]);

        return redirect()->route('login')
            ->with('success', 'Account created successfully! Welcome, ' . $user->name . '. Please log in with your email and password.');
    }
}