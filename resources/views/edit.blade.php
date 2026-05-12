@extends('layouts.app')

@section('title', 'Редактировать мастер-класс')

@section('content')
<div class="row row--nogutter top-line">
    <div class="line"></div>
</div>
<div class="main">
    <div class="row">
        <div class="row--small">
            <form action="{{ route('cabinet.update', $masterClass->id) }}" method="POST">
                @csrf
                @method('PUT')
                <h2>Редактирование мастер-класса</h2>
                <div class="form-group">
                    <label>Описание мастер-класса</label>
                    <textarea name="description">{{ $masterClass->description }}</textarea>
                </div>
                <div class="form-group">
                    <label>Стоимость (руб.)</label>
                    <input type="number" name="price" value="{{ $masterClass->price }}"  step="0.01">
                </div>
                <div class="form-group">
                    <button class="btn">Сохранить</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection