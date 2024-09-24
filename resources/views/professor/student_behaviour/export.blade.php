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
                <th>{{translation('national_id')}}</th>
                <th>{{translation('average_notation')}}</th>
                <th>{{translation('comment')}}</th>
            </tr>
            <?php echo $arr_data['data']; ?>
        </table>
    </main>
</body>
</html>
