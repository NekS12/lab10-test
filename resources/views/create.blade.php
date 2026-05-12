@extends('layouts.app')

@section('title', 'Добавить мастер-класс')

@section('content')
<div class="row row--nogutter top-line">
    <div class="line"></div>
</div>
<div class="main">
    <div class="row">
        <div class="row--small">
            <form action="{{ route('cabinet.store') }}" method="POST">
                @csrf
                <h2>Форма добавления мастер-класса</h2>
                
                @if($errors->any())
                    <div style="background:#f44336; color:#fff; padding:10px; margin-bottom:20px; border-radius:5px;">
                        <ul style="margin:0; padding-left:20px;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <div class="form-group">
                    <label>Вид творчества</label>
                    <select name="type_id" required>
                        <option value="">Выберите вид творчества</option>
                        @foreach($types as $type)
                            <option value="{{ $type->id }}" {{ old('type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Название мастер-класса</label>
                    <input type="text" name="title" required value="{{ old('title') }}" placeholder="Например: Моделирование транспорта">
                </div>
                
                <div class="form-group">
                    <label>Описание мастер-класса</label>
                    <textarea name="description" required placeholder="Подробно опишите, чему научатся участники...">{{ old('description') }}</textarea>
                </div>
                
                <div class="form-group">
                    <label>Дата</label>
                    <select name="date" required id="dateSelect">
                        <option value="">Выберите дату</option>
                        @foreach($availableDates as $date)
                            <option value="{{ $date }}" {{ old('date') == $date ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}
                            </option>
                        @endforeach
                    </select>
                    <small style="color:#666; display:block; margin-top:5px;">Занятые даты не отображаются (выходные также исключены)</small>
                </div>
                
                <div class="form-group">
                    <label>Время (2 часа)</label>
                    <select name="start_time" required id="timeSelect">
                        <option value="">Выберите время</option>
                        @foreach($timeSlots as $slot)
                            @php
                                $slotEnd = \Carbon\Carbon::parse($slot)->addHours(2)->format('H:i');
                                $isBusy = in_array(old('date', '') . '|' . $slot, $busySlots);
                            @endphp
                            @if($isBusy)
                                <option value="{{ $slot }}" disabled style="color:gray; background:#f0f0f0;">
                                    {{ $slot }} - {{ $slotEnd }} (ЗАНЯТО)
                                </option>
                            @else
                                <option value="{{ $slot }}" {{ old('start_time') == $slot ? 'selected' : '' }}>
                                    {{ $slot }} - {{ $slotEnd }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                    <small style="color:#666; display:block; margin-top:5px;">
                        Сетка занятий: 9:00-11:00, 11:00-13:00, 13:00-15:00, 15:00-17:00
                    </small>
                </div>
                
                <div class="form-group">
                    <label>Количество человек в группе</label>
                    <input type="number" name="max_participants" required min="1" max="30" value="{{ old('max_participants', 10) }}">
                </div>
                
                <div class="form-group">
                    <label>Стоимость (руб.)</label>
                    <input type="number" name="price" required min="0" step="100" value="{{ old('price', 1000) }}">
                </div>
                
                <div class="form-group">
                    <button class="btn">Добавить мастер-класс</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection