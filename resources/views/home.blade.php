@extends('layouts.app')

@section('title', 'Главная - ОчУмелые ручки')

@section('content')
<div class="row">
    <div class="hover"></div>
    <div class="title">Добро пожаловать!</div>
    <div class="row--small grid between">
        <div class="content">
            <img src="{{ asset('img/elifant.png') }}" alt="Творчество">
            <p>Клуб «ОчУмелые ручки» предлагает уникальные мастер-классы по архитектурному моделированию, кулинарии и резьбе по дереву. Развивайте творческие навыки вместе с нами!</p>
            <p><span>Отличительной особенностью</span> клуба является индивидуальный подход и возможность попробовать себя в разных направлениях. Мы ждём как новичков, так и опытных мастеров.</p>
            <p><span>Актуальность</span> программы подтверждена многолетним опытом: более 500 проведённых мастер-классов и тысячи довольных участников.</p>
        </div>
        <ul class="menu">
            @foreach(\App\Models\CreativityType::all() as $type)
                <li><a href="{{ route('category.show', $type->id) }}">{{ $type->name }}</a></li>
            @endforeach
        </ul>
    </div>
    
    @auth
        @if($myBookings->count() > 0)
        <div class="row shedule" style="background: #2b2586; margin-top: 20px;">
            <div class="row--small">
                <h2>Мои записи на мастер-классы</h2>
                <div class="drivers">
                    @foreach($myBookings as $booking)
                    <div class="driver grid" style="margin-bottom: 20px;">
                        <div class="driver-left grid">
                            <div class="driver-text">
                                <div class="driver-name">{{ $booking->masterClass->title }}</div>
                                <div class="driver-desc">
                                    <strong>Вид творчества:</strong> {{ $booking->masterClass->type->name }}<br>
                                    <strong>Мастер:</strong> {{ $booking->masterClass->instructor->full_name }}<br>
                                    <strong>Дата:</strong> {{ $booking->masterClass->date->format('d.m.Y') }}<br>
                                    <strong>Время:</strong> {{ $booking->masterClass->start_time }} - 
                                    {{ \Carbon\Carbon::parse($booking->masterClass->start_time)->addHours(2)->format('H:i') }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    @endauth
    
    <div class="row shedule">
        <div class="row--small">
            <h2>Расписание мастер-классов</h2>
            <div class="drivers">
                @forelse($allClasses as $mc)
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
                    <p>Нет запланированных мастер-классов.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection