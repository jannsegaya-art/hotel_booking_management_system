<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\Room;

class HomeController extends Controller
{
    public function index()
    {
        $featured_rooms = Room::where('status', 'available')->take(6)->get();
        $ratings        = Rating::with(['user', 'room'])->latest()->take(6)->get();
        return view('public.home', compact('featured_rooms', 'ratings'));
    }

    public function rooms()
    {
        $rooms = Room::where('status', '!=', 'maintenance')->get();
        return view('public.rooms', compact('rooms'));
    }

    public function about()
    {
        return view('public.about');
    }

    public function contact()
    {
        return view('public.contact');
    }
}
