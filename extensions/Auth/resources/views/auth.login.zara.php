@extends('auth.common')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <h1 class="mt-5">Авторизация</h1>
        @if(Request::exists('error'))
        <div class="alert alert-danger"><b>Ошибка!</b> Email или пароль введены неверно.</div>
        @endif
    </div>
    <form class="col-lg-5" method="post" action="{{ url('auth.login') }}">
        <div class="form-group">
            <label for="inputEmail1">E-mail адрес</label>
            <input type="email" name="email" class="form-control" id="inputEmail1" placeholder="Введите email">
        </div>
        <div class="form-group">
            <label for="inputPassword1">Пароль</label>
            <input type="password" name="password" class="form-control" id="inputPassword1" placeholder="Пароль">
        </div>
        @csrf_field()
        <button type="submit" class="btn btn-primary">Войти</button>
        <a href="/register" class="btn">Регистрация</a>
    </form>
</div>
@stop