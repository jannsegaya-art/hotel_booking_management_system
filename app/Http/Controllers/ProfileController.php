<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        return view('profile.show', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'photo'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ], [
            'photo.image' => 'Please select a valid image file.',
            'photo.mimes' => 'Only JPG, PNG, or GIF images are allowed.',
            'photo.max'   => 'Image must be smaller than 5MB.',
        ]);

        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            $file = $request->file('photo');

            // Create the upload directory if it does not exist
            $uploadDir = public_path('uploads/profiles');
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0775, true);
            }

            // Delete the old photo from public/uploads/profiles/
            if ($user->profile_photo && file_exists(public_path($user->profile_photo))) {
                @unlink(public_path($user->profile_photo));
            }

            // Build a unique filename
            $extension = $file->getClientOriginalExtension();
            $filename  = 'user_' . $user->id . '_' . time() . '.' . $extension;

            // Move directly to public/uploads/profiles/
            $file->move($uploadDir, $filename);

            // Save relative path (relative to public/)
            $user->profile_photo = 'uploads/profiles/' . $filename;
        }

        $user->name    = $request->name;
        $user->phone   = $request->phone;
        $user->address = $request->address;
        $user->save();

        ActivityLog::create([
            'user_id'     => $user->id,
            'action'      => 'profile_update',
            'description' => $user->name . ' updated their profile.',
            'ip_address'  => $request->ip(),
        ]);

        return back()->with('success', 'Profile updated successfully!');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ], [
            'password.confirmed' => 'The new passwords do not match.',
            'password.min'       => 'New password must be at least 8 characters.',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        ActivityLog::create([
            'user_id'     => $user->id,
            'action'      => 'password_change',
            'description' => $user->name . ' changed their password.',
            'ip_address'  => $request->ip(),
        ]);

        return back()->with('success', 'Password changed successfully!');
    }
}
