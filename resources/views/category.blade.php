@extends('layouts.app')

@section('title', $type->name . ' - ОчУмелые ручки')

@section('content')
<div class="row">
    <div class="hover"></div>
    <div class="title">{{ $type->name }}</div>
    <div class="row--small grid between">
        <div class="content">
            <img src="{{ asset('img/elifant.png') }}" alt="{{ $type->name }}">
            {!! nl2br(e($type->description)) !!}
        </div>
        <ul class="menu">
            @foreach(\App\Models\CreativityType::all() as $t)
                <li><a href="{{ route('category.show', $t->id) }}">{{ $t->name }}</a></li>
            @endforeach
        </ul>
    </div>
    <div class="row shedule">
        <div class="row--small">
            <h2>Расписание мастер-классов</h2>
            <div class="drivers">
                @forelse($classes as $mc)
                <div class="driver grid">
                    <div class="driver-left grid">
                        <div class="driver-photo">
                            <img src="{{ asset('img/driver1.png') }}" alt="Мастер">
                        </div>
                        <div class="driver-text">
                            <div class="driver-name">{{ $mc->instructor->full_name }}</div>
                            <div class="driver-desc">
                                <strong>{{ $mc->title }}</strong><br>
                                {{ $mc->description }}<br>
                                <strong>Стоимость:</strong> {{ number_format($mc->price, 0, ',', ' ') }} ₽<br>
                                <strong>Свободных мест:</strong> {{ $mc->free_seats }} из {{ $mc->max_participants }}
                            </div>
                        </div>
                    </div>
                    <div class="driver-right">
                        @auth
                            @if($mc->free_seats > 0)
                                <a href="{{ route('booking.confirm', $mc->id) }}" class="driver-btn">записаться</a>
                            @else
                                <button class="driver-btn" disabled style="opacity:0.5;">мест нет</button>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="driver-btn">войти</a>
                        @endauth
                        <div class="driver-time">
                            {{ $mc->date->format('d.m.Y') }}<br>
                            {{ $mc->start_time }} - {{ \Carbon\Carbon::parse($mc->start_time)->addHours(2)->format('H:i') }}
                        </div>
                    </div>
                </div>
                @empty
                    <p>Нет запланированных мастер-классов по этому направлению.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection