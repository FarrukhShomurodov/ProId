<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }} - Authorization</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <link rel="shortcut icon" href="{{ asset('images/logo/ProIDLogo.svg') }}"/>
    <link href="{{ asset('/style/authFromAnotherApp.css') }}" rel="stylesheet">
</head>
<body class="passport-authorize">
@php
    $user = \Illuminate\Support\Facades\Auth::user()
@endphp

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
                <p class="oauth_entrance"><span>{{ $user->name }} {{ $user->surname }}</span><br> Вы успешно вошли в
                    систему.</p>

                <!-- Select dropdown for selecting a user -->
                <select name="selected_user" id="selectedUser">
                    <option value="" selected>Please select a user</option>
                </select>

                <p class="redirect_timer">Через <span id="second">{{ 5 }}</span> секунд вы вернетесь на сайт
                    {{  $client->name  }} </p>
                <form method="post" action="{{ route('passport.authorizations.approve') }}" id="approvalForm">
                    @csrf
                    <input type="hidden" name="state" value="{{ $request->state }}">
                    <input type="hidden" name="client_id" value="{{ $client->getKey() }}">
                    <input type="hidden" name="auth_token" value="{{ $authToken }}">

                    <button type="submit" class="reirect_link approveUser" style="cursor:pointer;">Перейти
                        на {{  $client->name  }}</button>
                </form>

            </div>

            <div class="text">
                <p>Продолжая использовать PRO ID,<br>я принимаю <span>условия оферты.</span></p>
                <p>PRO ID - ключ от всех сервисов</p>
            </div>
        </div>
    </div>
</div>

{{-- Footer --}}
<div class="bottom-part">
    <p>Используйте режим инкогнито на чужом компьютере</p>
    <p>Справка и поддержка <span>©2021-2023 PRO GROUP</span></p>
</div>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    // Populate options when the page loads
    window.addEventListener('load', function () {
        // Fetch user data from localStorage
        let users = JSON.parse(localStorage.getItem('users_id'));

        // If there are users in localStorage, populate the dropdown
        if (users) {
            // Create an option for each user
            users.forEach(userId => {
                axios.get(`/api/get-another-user/${userId}`).then(res => {
                    const userData = res.data;
                    const option = new Option(`${userData.name} ${userData.surname}`, userData.id);
                    document.getElementById('selectedUser').appendChild(option);
                }).catch(error => {
                    console.error('Error fetching user:', error);
                });
            });

            // Get the ID of the previously selected user from localStorage
            let selectedUserId = localStorage.getItem('selected_user_id');

            // Set the selected user in the dropdown
            if (selectedUserId) {
                document.getElementById('selectedUser').value = selectedUserId;
            }
        }
    });

    // Event listener for select element change
    document.getElementById('selectedUser').addEventListener('change', function () {
        // Get the selected user ID
        const selectedUserId = this.value;
        // Save the selected user ID to localStorage
        localStorage.setItem('selected_user_id', selectedUserId);
        // Call switchUser function with the selected user ID
        switchUser(selectedUserId);
    });


    // Function to switch the user
    function switchUser(id) {
        axios.post('/api/switch-user', {user_id: id})
            .then((res) => {
                let d = new Date();
                d.setTime(d.getTime() + 24 * 60 * 60 * 1000);
                let expires = "expires=" + d.toUTCString();

                // Update user data
                // Assuming res.data contains the updated user information
                // Modify this part according to your API response
                this.id = res.data.user.id;

                // Update access token in cookie
                document.cookie = "accessToken=" + res.data.access_token + ";expires=" + expires + ";path=/;SameSite=None;Secure";

                // Update Authorization header for axios
                axios.defaults.headers.common['Authorization'] = `Bearer ${res.data.access_token}`;

                // Reload the page
                window.location.reload();
            })
            .catch(error => {
                console.error('Switch user error:', error);
            });
    }

    document.addEventListener("DOMContentLoaded", function () {
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
            }
        }

        let timerInterval = setInterval(timer, 1000)

        // Clear interval if time out
        if (second <= 0) {
            clearInterval(timerInterval);
        }
    });
</script>

</body>
</html>
