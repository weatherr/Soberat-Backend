@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('You are logged in!') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<div>
<?php if($userInfo == false && $userNotLogged == false): ?>
<form action="/save_user_info" method="post">
    @csrf
    <label for="weight">Weight:</label>
    <input type="text" placeholder="" name="weight">
    <br>
    <label for="height">Height:</label>
    <input type="text" name="height"></input>
    <br>
    <label for="age">Age:</label>
    <input type="text" name="age"></input>
    <br>
    <button type="submit">Submit</button>
</form>
<?php endif;?>
</div>
