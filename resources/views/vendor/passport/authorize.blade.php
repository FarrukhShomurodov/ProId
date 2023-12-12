<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name') }} - Authorization</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <!-- Styles -->

    <link href="{{ asset('/style/authFromAnotherApp.css') }}" rel="stylesheet">
</head>
<body class="passport-authorize">
    <div id="app">
        <router-view 
        :request-state="{{ json_encode($request->state) }}"
        :client-id="{{ json_encode($client->getKey()) }}"
        :auth-token="{{ json_encode($authToken) }}"
        :client-name="{{ json_encode($client->name) }}"
    ></router-view>
    </div>
</body>
</html>


{{-- auth --}}

{{-- <form method="post" action="{{ route('passport.authorizations.approve') }}">
    @csrf

    <input type="hidden" name="state" value="{{ $request->state }}">
    <input type="hidden" name="client_id" value="{{ $client->getKey() }}">
    <input type="hidden" name="auth_token" value="{{ $authToken }}">
    <button type="submit" class="btn btn-success btn-approve">Перейти на сайт {{ $client->name }}</button>
</form> --}}