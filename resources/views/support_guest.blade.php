<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <table align=center border=1 width=50%>
    <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Country</th>
        <th>Message</th>
    </tr>
        <tr>
            <td>{!! $data['Name'] !!}</td>
            <td>{!! $data['Email'] !!}</td>
            <td>{!! $data['Phone'] !!}</td>
            <td>{!! $data['Country'] !!}</td>
            <td>{!! $data['Message'] !!}</td>

        </tr>

</body>
</html>
