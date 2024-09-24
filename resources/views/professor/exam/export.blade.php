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
                <th>{{translation('exam_number')}}</th>
                <th>{{translation('level')}}</th>
                <th>{{translation('class')}}</th>
                <th>{{translation('exam_type')}}</th>
                <th>{{translation('exam_name')}}</th>
                <th>{{translation('course')}}</th>
                <th>{{translation('status')}}</th>
                <th>{{translation('exam_period')}}</th>
            </tr>
            <?php $count=1; ?>
            @foreach($arr_data as $value)
            <tr>
                <td>{{ $count++ }}</td>
                <td>{{ $value->exam_no }}</td>
                <td>{{ $value->level_name }}</td>
                <td>{{ $value->class_name }}</td>
                <td>{{ $value->exam_type }}</td>
                <td>{{ $value->exam_name }}</td>
                <td>{{ $value->course_name }}</td>
                <td>{{ $value->status }}</td>
                <td>{{ $value->exam_start_time.'-'.$value->exam_end_time }}</td>
            </tr>
            @endforeach
        </table>
    </main>
</body>
</html>
