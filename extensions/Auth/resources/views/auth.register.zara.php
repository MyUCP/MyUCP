@extends('auth.common')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <h1 class="mt-5">Регистрация</h1>
        @if(!empty(flash('error')))
        <div class="alert alert-danger"><b>Ошибка!</b> {{ flash('error') }}</div>
        @endif
    </div>
    <form class="col-lg-5" method="post" action="{{ url('auth.register') }}">
        <div class="form-group">
            <label for="inputEmail1">E-mail адрес</label>
            <input type="email" name="email" class="form-control" id="inputEmail1" placeholder="Введите email">
        </div>
        <div class="form-group">
            <label for="inputPassword1">Пароль</label>
            <input type="password" name="password" class="form-control" id="inputPassword1" placeholder="Пароль">
        </div>
        <div class="form-group">
            <label for="inputPassword2">Повторите пароль</label>
            <input type="password" name="password_repeat" class="form-control" id="inputPassword2" placeholder="Повторите пароль">
        </div>
        @csrf_field()
        <button type="submit" class="btn btn-primary">Зарегистрироватся</button>
        <a href="/login" class="btn">Авторизация</a>
    </form>
</div>
@stop