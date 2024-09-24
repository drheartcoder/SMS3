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
                <th>{{translation('level')}}</th>
                <th>{{translation('class')}}</th>
                <th>{{translation('course')}}</th>
                <th>{{translation('homework_details')}}</th>
                <th>{{translation('added_date')}}</th>
                <th>{{translation('due_date')}}</th>
                <th>{{translation('status')}}</th>
            </tr>
            <?php $count=1; ?>
            @foreach($arr_data as $value)
            <tr>
                <td>{{$count++}}</td>
                <td>{{$value->level_name}}</td>
                <td>{{$value->class_name}}</td>
                <td>{{$value->course_name}}</td>
                <td>{{$value->description}}</td>
                <td>{{$value->added_date}}</td>
                <td>{{$value->due_date}}</td>
                <td>{{$value->status}}</td>
            </tr>
            @endforeach
        </table>
    </main>
</body>
</html>
