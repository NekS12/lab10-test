<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\CreativityType;
use App\Models\MasterClass;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $types = CreativityType::all();

        $allClasses = MasterClass::with(['instructor', 'type'])
            ->where('date', '>=', now()->startOfDay())
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        $myBookings = collect();
        if (Auth::check()) {
            $myBookings = Booking::where('user_id', Auth::id())
                ->with(['masterClass.type', 'masterClass.instructor'])
                ->get();
        }

        return view('home', compact('types', 'allClasses', 'myBookings'));
    }
}
