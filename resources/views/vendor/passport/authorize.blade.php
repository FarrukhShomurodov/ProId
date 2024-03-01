<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }} - Authorization</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <link rel="shortcut icon" href="{{ asset('images/logo/ProIDLogo.svg') }}" />
    <link href="{{ asset('/style/authFromAnotherApp.css') }}" rel="stylesheet">
</head>
<body class="passport-authorize">

{{--Fetching User--}}
@php $user = \Illuminate\Support\Facades\Auth::user() @endphp

{{-- Authitificated User From --}}
<div class="login">
    <div class="form">
        <div class="top">
            <div class="image">
                <img src="{{asset('/images/logo/pro_id_logo.svg')}}" alt="">
            </div>
            <div>
                <a href="" class="link">Что такое PRO ID <img src="{{asset('/images/icons/question.svg')}}"></a>
            </div>
        </div>
        <div class="bottom top-aqua">
            <div class="enterence">
                <div class="text">
                    <p>{{  $client->name  }}</p>
                    <p>Вход c помощью PRO ID</p>
                </div>
                <div class="image">
                    <img src="{{asset('/images/logo/pro_id_logo_mini.svg')}}" alt="">
                </div>
            </div>

            <div class="oauth_redirect oauth_redirect_true">
                <img src="{{asset('/images/icons/oauthTrue.svg')}}">
                <p class="oauth_entrance"><span>{{ $user->name }} {{ $user->surname }}</span><br> Вы успешно вошли в систему.</p>
                <p class="redirect_timer">Через <span id="second">{{ 5 }}</span> секунд вы вернетесь на сайт
                    {{  $client->name  }} </p>
                <form method="post" action="{{ route('passport.authorizations.approve') }}" id="approvalForm">
                    @csrf

                    <input type="hidden" name="state" value="{{ $request->state }}">
                    <input type="hidden" name="client_id" value="{{ $client->getKey() }}">
                    <input type="hidden" name="auth_token" value="{{ $authToken }}">
                    <button type="submit" class="reirect_link">Перейти на сайт {{  $client->name  }}</button>
                </form>
            </div>

            <div class="text">
                <p>Продолжая использовать PRO ID,<br>я принимаю <span>условия оферты.</span></p>
                <p>PRO ID - ключ от всех сервисов</p>
            </div>
        </div>
    </div>
</div>

{{-- Fotter --}}
<div class="bottom-part">
    <p>Используйте режим инкогнито на чужом компьютере</p>
    <p>Справка и поддержка <span>©2021-2023 PRO GROUP</span></p>
</div>

<script>
  const redirect = document.querySelector('.reirect_link');
    redirect.addEventListener('click', function() {
        window.close();
    });

    
    // Set default value in second
    let second = 5;

    // Getting second element form html
    let secondEl = document.querySelector('#second');

    // Setting second value in element and decreasing value each second
    function timer() {
        if (second > 0) {
            second -= 1;
            secondEl.innerHTML = second;
        } else {
            clearInterval(timerInterval)
            document.getElementById('approvalForm').submit();
            window.close()
        }
    }
    let timerInterval = setInterval(timer, 1000)

    // Clear interval if time out
    if (second <= 0) {
        clearInterval(timerInterval);
        window.close()
    }
</script>
</body>
</html>
