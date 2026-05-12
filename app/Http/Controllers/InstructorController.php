<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterClass;
use App\Models\CreativityType;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class InstructorController extends Controller
{
    // Личный кабинет ведущего
    public function index()
    {
        $classes = MasterClass::where('instructor_id', Auth::id())
            ->with(['bookings.user', 'type'])
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        return view('cabinet', compact('classes'));
    }

    // Форма создания нового МК
    public function create()
    {
        $types = CreativityType::all();
        
        // Получаем занятые слоты для текущего ведущего
        $busySlots = MasterClass::where('instructor_id', Auth::id())
            ->where('date', '>=', Carbon::now()->startOfDay())
            ->get(['date', 'start_time'])
            ->map(function($item) {
                return $item->date->format('Y-m-d') . '|' . $item->start_time;
            })
            ->toArray();
        
        // Доступные даты: от сегодня до +30 дней
        $availableDates = [];
        for ($i = 0; $i <= 30; $i++) {
            $date = Carbon::now()->addDays($i);
            if (!$date->isWeekend()) {
                $availableDates[] = $date->format('Y-m-d');
            }
        }
        
        $timeSlots = ['09:00', '11:00', '13:00', '15:00'];
        
        return view('create', compact('types', 'busySlots', 'availableDates', 'timeSlots'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type_id' => ['required', 'exists:creativity_types,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:10'],
            'date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'in:09:00,11:00,13:00,15:00'],
            'max_participants' => ['required', 'integer', 'min:1', 'max:30'],
            'price' => ['required', 'numeric', 'min:0', 'max:100000'],
        ]);

        $isBusy = MasterClass::where('instructor_id', Auth::id())
            ->where('date', $validated['date'])
            ->where('start_time', $validated['start_time'])
            ->exists();

        if ($isBusy) {
            return back()
                ->withErrors([
                    'start_time' => 'У вас уже запланирован мастер-класс на эту дату и время! Выберите другую дату или время.'
                ])
                ->withInput();
        }

        $selectedDate = Carbon::parse($validated['date']);
        if ($selectedDate->isPast() && !$selectedDate->isToday()) {
            return back()
                ->withErrors([
                    'date' => 'Нельзя создать мастер-класс на прошедшую дату.'
                ])
                ->withInput();
        }

        $classesCountOnDate = MasterClass::where('instructor_id', Auth::id())
            ->where('date', $validated['date'])
            ->count();
        
        if ($classesCountOnDate >= 3) {
            return back()
                ->withErrors([
                    'date' => 'Вы не можете провести более 3 мастер-классов в один день.'
                ])
                ->withInput();
        }

        MasterClass::create([
            'instructor_id' => Auth::id(),
            ...$validated,
        ]);

        return redirect()->route('cabinet.index')
            ->with('message', 'Мастер-класс "' . $validated['title'] . '" успешно добавлен!');
    }

    public function edit($id)
    {
        $masterClass = MasterClass::where('instructor_id', Auth::id())->findOrFail($id);
        return view('edit', compact('masterClass'));
    }

    public function update(Request $request, $id)
    {
        $masterClass = MasterClass::where('instructor_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'description' => ['required', 'string', 'min:10'],
            'price' => ['required', 'numeric', 'min:0', 'max:100000'],
        ]);

        $masterClass->update($validated);

        return redirect()->route('cabinet.index')
            ->with('message', 'Мастер-класс успешно обновлен!');
    }
}