@extends('layouts.app')

@section('title', 'Подтверждение записи')

@section('content')
<div class="row">
    <div class="row--small">
        <h2>Подтверждение записи</h2>
        <div style="margin:20px 0; padding:15px; border:1px solid #20416c;">
            <p><strong>ФИО:</strong> {{ auth()->user()->full_name }}</p>
            <p><strong>Вид творчества:</strong> {{ $masterClass->type->name }}</p>
            <p><strong>Мастер:</strong> {{ $masterClass->instructor->full_name }}</p>
            <p><strong>Дата и время:</strong> {{ $masterClass->date->format('d.m.Y') }} {{ $masterClass->start_time }}</p>
        </div>
        <form action="{{ route('booking.process', $masterClass->id) }}" method="POST">
            @csrf
            <button type="submit" name="action" value="confirm" class="btn">Подтвердить</button>
            <button type="submit" name="action" value="cancel" class="btn" style="margin-left:15px;">Отмена</button>
        </form>
    </div>
</div>
@endsection