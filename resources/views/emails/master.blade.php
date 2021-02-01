<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Email</title>
</head>
<body style="margin: 0; padding: 0; box-sizing: border-box; background-color: #eeeeee">
    <div
        style="
            display:block;
            max-width: 728px;
            width: 60%;
            margin: 0px auto;
            background-color:#4985d3;
        ">
        <img src="{{url('/static/images/logo-jr.svg')}}"
            style="
                display:block;
                width: 30%;
                opacity: 30%;
                margin: 0 auto;
                margin-bottom: -2rem;
        "/>
        <div style="
            background-color: white;
            padding: 2rem;
        ">
            @yield('content')
        </div>
    </div>
</body>
</html>
