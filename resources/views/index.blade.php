<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>
</head>
<body>

{{-- todo replay DB stats on main page --}}

<form action="/api/v1/replays" method="post" enctype="multipart/form-data">
    <input type="file" name="file" accept=".StormReplay">
    <input type="submit" value="Submit">
</form>

</body>
</html>
