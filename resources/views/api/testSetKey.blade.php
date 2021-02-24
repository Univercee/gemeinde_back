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
        <input type="text" name="key" placeholder="Key">
        <input type="submit">
    </form>
</body>
</html>