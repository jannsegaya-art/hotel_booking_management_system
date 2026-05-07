<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->latest()->paginate(20);
        return view('admin.notifications', compact('notifications'));
    }

    public function markRead(Notification $notification)
    {
        $notification->update(['is_read' => true]);
        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllRead()
    {
        Notification::where('user_id', auth()->id())->update(['is_read' => true]);
        return back()->with('success', 'All notifications marked as read.');
    }

    public function destroy(Notification $notification)
    {
        $notification->delete();
        return back()->with('success', 'Notification deleted.');
    }

    public function send(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'title'   => 'required|string|max:255',
            'message' => 'required|string',
            'type'    => 'required|in:info,success,warning,danger',
        ]);

        if ($request->user_id === 'all') {
            $users = User::all();
            foreach ($users as $user) {
                Notification::create([
                    'user_id' => $user->id,
                    'title'   => $request->title,
                    'message' => $request->message,
                    'type'    => $request->type,
                ]);
            }
        } else {
            Notification::create([
                'user_id' => $request->user_id,
                'title'   => $request->title,
                'message' => $request->message,
                'type'    => $request->type,
            ]);
        }

        return back()->with('success', 'Notification sent successfully!');
    }
}
