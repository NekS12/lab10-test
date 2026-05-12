<?php

namespace App\Http\Controllers;

use App\Models\MasterClass;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function confirmPage($id)
    {
        $masterClass = MasterClass::findOrFail($id);

        if ($masterClass->free_seats <= 0) {
            return redirect()->back()->with('error', 'К сожалению, свободных мест больше нет.');
        }

        if (Booking::isAlreadyBooked(Auth::id(), $id)) {
            return redirect()->back()->with('error', 'Вы уже записаны на этот мастер-класс.');
        }

        return view('confirm', compact('masterClass'));
    }

    public function process(Request $request, $id)
    {
        $masterClass = MasterClass::findOrFail($id);

        if ($request->action === 'cancel') {
            return redirect()->route('category.show', $masterClass->type_id)
                ->with('message', 'Запись была отменена.');
        }

        if ($masterClass->free_seats <= 0) {
            return redirect()->back()->with('error', 'Мест больше нет. Попробуйте другой мастер-класс.');
        }

        if (Booking::isAlreadyBooked(Auth::id(), $id)) {
            return redirect()->back()->with('error', 'Вы уже записаны на этот мастер-класс.');
        }


        // Создаем запись
        Booking::create([
            'user_id' => Auth::id(),
            'master_class_id' => $id,
        ]);

        return redirect()->route('category.show', $masterClass->type_id)
            ->with('message', 'Вы успешно записаны на мастер-класс!');
    }
}