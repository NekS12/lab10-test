@extends('layouts.app')

@section('title', 'Личный кабинет - ОчУмелые ручки')
@section('body_class', 'dp')

@section('content')
<div class="row">
    <div class="hover"></div>
    <div class="title"></div>
    <div class="row--small grid between">
        <div class="content driver-page">
            <div class="driver-page-photo">
                <img src="{{ asset('img/driver-page.png') }}">
            </div>
            <div class="driver-page-name">{{ auth()->user()->full_name }}</div>
            <div class="driver-page-text">
                <div class="driver-page-my">Мои мастер-классы</div>
                <table class="driver-page-table">
                    <tbody>
                        @forelse($classes as $mc)
                        <tr>
                            <td style="vertical-align: top; white-space: nowrap;">
                                {{ $mc->date->format('d.m.Y') }}<br>
                                {{ $mc->start_time }} - {{ \Carbon\Carbon::parse($mc->start_time)->addHours(2)->format('H:i') }}
                            </td>
                            <td>
                                <b>{{ $mc->title }}</b><br>
                                <small style="color: #666;">{{ $mc->type->name }}</small><br>
                                <small>Стоимость: {{ number_format($mc->price, 0, ',', ' ') }} ₽</small><br>
                                <small>Мест: {{ $mc->bookings->count() }}/{{ $mc->max_participants }}</small>
                                
                                <div style="margin-top: 15px;">
                                    <strong>Участники:</strong>
                                    @forelse($mc->bookings as $booking)
                                        <p style="margin: 5px 0;">
                                            {{ $loop->iteration }}. {{ $booking->user->full_name }}<br>
                                            email: {{ $booking->user->email }}<br>
                                            тел: {{ $booking->user->phone }}
                                        </p>
                                    @empty
                                        <p>Нет записей</p>
                                    @endforelse
                                </div>
                                
                                <div style="margin-top: 15px;">
                                    <a href="{{ route('cabinet.edit', $mc->id) }}" 
                                       style="display: inline-block; background: #20416c; color: #fff; padding: 5px 15px; 
                                              text-decoration: none; border-radius: 3px; font-size: 12px; transition: all 0.2s ease;"
                                       onmouseover="this.style.background='#2b5a8c'"
                                       onmouseout="this.style.background='#20416c'">
                                            Редактировать 
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" style="text-align: center; padding: 40px;">
                                Вы ещё не создали ни одного мастер-класса.
                                <div style="margin-top: 20px;">
                                    <a href="{{ route('cabinet.create') }}" class="driver-page-btn btn">Создать первый мастер-класс</a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="driver-page-btn-wrapper">
                <a href="{{ route('cabinet.create') }}" class="driver-page-btn btn">Добавить мастер-класс</a>
            </div>
        </div>
        <ul class="menu">
            @foreach(\App\Models\CreativityType::all() as $type)
                <li><a href="{{ route('category.show', $type->id) }}">{{ $type->name }}</a></li>
            @endforeach
        </ul>
    </div>
</div>
@endsection