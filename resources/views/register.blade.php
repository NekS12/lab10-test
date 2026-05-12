@extends('layouts.app')

@section('title', 'Регистрация - ОчУмелые ручки')

@section('content')
<div class="row row--nogutter top-line">
    <div class="line"></div>
</div>
<div class="main">
    <div class="row">
        <div class="row--small">
            <form action="{{ route('register') }}" method="POST">
                @csrf
                <h2>Форма регистрации</h2>
                
                
                <div class="form-group">
                    <label>ФИО <span style="color:#f44336;">*</span></label>
                    <input type="text" name="full_name"  value="{{ old('full_name') }}" 
                           placeholder="Иванов Иван Иванович"
                           style="border-color: {{ $errors->has('full_name') ? '#f44336' : '#20416c' }};">
                    @error('full_name')
                        <div style="color:#f44336; font-size:12px; margin-top:5px;">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label>Email <span style="color:#f44336;">*</span></label>
                    <input type="email" name="email"  value="{{ old('email') }}" 
                           placeholder="example@mail.ru"
                           style="border-color: {{ $errors->has('email') ? '#f44336' : '#20416c' }};">
                    @error('email')
                        <div style="color:#f44336; font-size:12px; margin-top:5px;">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label>Пароль <span style="color:#f44336;">*</span></label>
                    <input type="password" name="password"  
                           style="border-color: {{ $errors->has('password') ? '#f44336' : '#20416c' }};">
                    @error('password')
                        <div style="color:#f44336; font-size:12px; margin-top:5px;">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label>Подтверждение пароля <span style="color:#f44336;">*</span></label>
                    <input type="password" name="password_confirmation"  
                           style="border-color: {{ $errors->has('password') ? '#f44336' : '#20416c' }};">
                    @error('password')
                        <div style="color:#f44336; font-size:12px; margin-top:5px;">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label>Номер телефона <span style="color:#f44336;">*</span></label>
                    <input type="tel" name="phone"  value="{{ old('phone') }}" 
                           placeholder="+7 999 123-45-67"
                           style="border-color: {{ $errors->has('phone') ? '#f44336' : '#20416c' }};">
                    @error('phone')
                        <div style="color:#f44336; font-size:12px; margin-top:5px;">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <button class="btn">Зарегистрироваться</button>
                </div>
                
                <p style="margin-top: 20px; text-align: center;">
                    <a href="{{ route('login') }}">Уже есть аккаунт? Войти</a>
                </p>
            </form>
        </div>
    </div>
</div>
@endsection