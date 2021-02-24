<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="/api/signup/setLoginKey" method="POST">
        <select name="email" placeholder="Email">
            @foreach ($emails as $email)
                <option value="{{$email->email}}">{{$email->email}}</option>
            @endforeach
        </select>
        <br>
        <input type="number" name="time_value" placeholder="Time value">
        <select name="time_type" placeholder="Time type">
            <option value="SECOND">Seconds</option>
            <option value="MINUTE">Minutes</option>
            <option value="HOUR">Hours</option>
        </select>
        <br>
        <input type="submit" value="Generate key">
    </form>    
    @if(isset($key))
        <a href="/api/signup/key/{{$key}}">Test link</a>
    @endif
    
</body>
</html>