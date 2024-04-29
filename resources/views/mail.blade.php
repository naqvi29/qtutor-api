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
        <th>Requester info</th>
        <th>Cell Phone</th>
        <th>Country</th>
        <th>Email</th>
    </tr>
        <tr>
            <td>Name: {!! $data['Full_Name'] !!}<br>DOB: {!! $data['Dob'] !!}</td>
            <td>{!! $data['Cell_Phone'] !!}</td>
            <td>{!! $data['Country'] !!}</td>
            <td>{!! $data['Email'] !!}</td>

        </tr>

</body>
</html>
