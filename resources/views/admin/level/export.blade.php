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
                <th>{{translation('level_name')}}</th>
            </tr>
            <?php $count=1; ?>
            @foreach($arr_data as $value)
            <tr>
                <td>{{$count++}}</td>
                <td>{{$value->level_name}}</td>
            </tr>
            @endforeach
        </table>
    </main>  

</body>
</html>
