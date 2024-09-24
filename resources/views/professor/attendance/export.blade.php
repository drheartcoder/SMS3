<html>
<head>
    <link rel="stylesheet" href="{{url('/')}}/css/export.css">
</head>
<body>
    <header>
        {{config('app.project.header')}} 
    </header> 
    
     <div align="center">
      {{$sheetTitle or ''}}
      </div>
      <br>
    <footer>
        {{config('app.project.footer')}} 
    </footer>
    <main>
        <table align="center">
       <?php echo $arr_data; ?>
       </table>
    </main>
</body>
</html>
