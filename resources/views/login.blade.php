@extends('layouts.app')

@section('title', 'Вход - ОчУмелые ручки')

@section('content')
<div class="row row--nogutter top-line">
    <div class="line"></div>
</div>
<div class="main">
    <div class="row">
        <div class="row--small">
            <form action="{{ route('login') }}" method="POST">
                @csrf
                <h2>Вход</h2>
                
                @if(session('success'))
                    <div style="background:#4caf50; color:#fff; padding:12px; margin-bottom:20px; border-radius:5px;">
                        {{ session('success') }}
                    </div>
                @endif
                
                @if($errors->any())
                    <div style="background:#ffebee; border-left:4px solid #f44336; padding:12px; margin-bottom:20px; border-radius:4px;">
                        <strong style="color:#c62828;">Ошибка входа:</strong>
                        @foreach($errors->all() as $error)
                            <div style="color:#c62828; margin-top:5px;">{{ $error }}</div>
                        @endforeach
                    </div>
                @endif
                
                <div class="form-group">
                    <label>Email <span style="color:#f44336;">*</span></label>
                    <input type="email" name="email" required value="{{ old('email') }}" 
                           style="border-color: {{ $errors->has('email') ? '#f44336' : '#20416c' }};">
                </div>
                
                <div class="form-group">
                    <label>Пароль <span style="color:#f44336;">*</span></label>
                    <input type="password" name="password" required 
                           style="border-color: {{ $errors->has('password') ? '#f44336' : '#20416c' }};">
                </div>
                
                <div class="form-group">
                    <button class="btn">Войти</button>
                </div>
                
                <p style="margin-top: 20px; text-align: center;">
                    <a href="{{ route('register') }}">Нет аккаунта? Зарегистрироваться</a>
                </p>
            </form>
        </div>
    </div>
</div>
@endsection