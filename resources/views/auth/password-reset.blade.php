<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Interpretasi ID</title>
    </head>
    <body>
        <form action="{{ route('reset', $token) }}" method="post">
            {{ csrf_field() }}
            <input type="text" name="password">
            <input type="text" name="password_confirmation">
            <button type="submit">Reset</button>
        </form>
    </body>
</html>