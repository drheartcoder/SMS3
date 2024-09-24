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
                <th>{{translation('subject')}}</th>
                <th>{{translation('suggestion_date')}}</th>
                <th>{{translation('from_school_number')}}</th>
                <th>{{translation('from_school_name')}}</th>
                <th>{{translation('role')}}</th>
            </tr>
            <?php $count=1; ?>
            @foreach($arr_data as $value)
            <tr>
                <td>{{$count++}}</td>
                <td>{{$value->subject}}</td>
                <td>{{$value->suggestion_date}}</td>
                <td>{{$value->school_no}}</td>
                <td>{{$value->school_name}}</td>
                <td>{{$value->user_role}}</td>
            </tr>
            @endforeach
        </table>
    </main>
</body>
</html>
