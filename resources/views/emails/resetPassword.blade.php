@extends('emails.master')

@section('content')
    <p>
        Hi {{$name}},
        <br/><br/>
        Your password was re-established successfully
        <br/><br/>
        To enter your user account please use the following password: <strong style="font-size: 1.2rem">{{$new_password}}</strong>

        <div style="display: flex; flex-flow: row wrap; width:100%; justify-content:center">
            <div style="
                background-color: #4985d3;
                display: inline-block;
                padding: 16px 32px;
                border-radius: 5px;
                border: 2px solid #396aaa;
                cursor: pointer;
            ">
                <a href="localhost:3000/login'"
                style="text-decoration: none; color: #f5f5f5;">
                    <strong>Ir a mi cuenta</strong>
                </a>
            </div>
        </div>
    </p>
@stop
