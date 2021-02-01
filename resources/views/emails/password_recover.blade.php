@extends('emails.master')

@section('content')
    <p>
        Hi {{$name}},
        <br/><br/>
        This email is goint to help you to recover your password. Whether you didn't request it, please ignore this email.
        <br/><br/>
        Click the bottom below and type the next code: <strong style="font-size: 1.2rem">{{$code}}</strong>

        <div style="display: flex; flex-flow: row wrap; width:100%; justify-content:center">
            <div style="
                background-color: #4985d3;
                display: inline-block;
                padding: 16px 32px;
                border-radius: 5px;
                border: 2px solid #396aaa;
                cursor: pointer;
            ">
                <a href="localhost:3000/reset/"{{$email}}"
                style="text-decoration: none; color: #f5f5f5;">
                    <strong>Reset password</strong>
                </a>
            </div>
        </div>

        <div>
            <p>If the bottom doesn't work, please copy and paste the next link in your browser:</p>
            <p>
                <a href="localhost:3000/reset/"{{$email}}>http://localhost:3000/{{$email}}</a>
            </p>
        </div>
    </p>
@stop
