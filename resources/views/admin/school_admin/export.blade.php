<html>
<head>
    <link rel="stylesheet" href="{{url('/')}}/css/export.css">
</head>
<body>
    <header>
        {{config('app.project.header')}} 
    </header> 
    <footer>
        {{config('app.project.footer')}} 
    </footer>
    <main>
        <table align="center">
            <tr>
                <th>{{translation('sr_no')}}</th>
                <th>{{translation('name')}}</th>
                <th>{{translation('email')}}</th>
                <th>{{translation('school_name')}}</th>
            </tr>
            <?php $count=1; ?>
            @foreach($arr_data as $value)
            <tr>
                <td>{{$count++}}</td>
                <td>{{$value->user_name}}</td>
                <td>{{$value->email}}</td>
                <td>{{$value->school_name}}</td>
            </tr>
            @endforeach
        </table>
    </main>
</body>
</html>
