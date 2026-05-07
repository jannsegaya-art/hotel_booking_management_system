<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->role);
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->role === 'staff' && $user->status === 'pending') {
                Auth::logout();
                return back()->withErrors(['email' => 'Your staff account is pending admin approval.'])->withInput($request->except('password'));
            }

            if ($user->status === 'inactive') {
                Auth::logout();
                return back()->withErrors(['email' => 'Your account has been deactivated. Contact the administrator.'])->withInput($request->except('password'));
            }

            ActivityLog::create([
                'user_id'     => $user->id,
                'action'      => 'login',
                'description' => $user->name . ' logged in.',
                'ip_address'  => $request->ip(),
            ]);

            return $this->redirectByRole($user->role);
        }

        return back()
            ->withErrors(['email' => 'Invalid email or password. Please try again.'])
            ->withInput($request->except('password'));
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            ActivityLog::create([
                'user_id'     => Auth::id(),
                'action'      => 'logout',
                'description' => Auth::user()->name . ' logged out.',
                'ip_address'  => $request->ip(),
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }

    private function redirectByRole(string $role)
    {
        return match ($role) {
            'admin'    => redirect()->route('admin.dashboard'),
            'staff'    => redirect()->route('staff.dashboard'),
            'customer' => redirect()->route('customer.dashboard'),
            default    => redirect()->route('home'),
        };
    }
}
