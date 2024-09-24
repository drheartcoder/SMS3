<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <meta http-equiv="X-UA-Compatible" content="IE=edge" />
      <meta name="description" content="" />
      <meta name="keywords" content="" />
      <meta name="author" content="" />
      <!-- ======================================================================== -->
      <title>500-page</title>
   
      <!-- Bootstrap Core CSS -->
      <link rel="stylesheet" href="{{url('/')}}/assets/bootstrap/css/bootstrap.min.css">
      <!-- main CSS -->
      <link href= "{{url('/')}}/css/404-page.css" rel="stylesheet" type="text/css" />
      <link rel="stylesheet" href="{{url('/')}}/assets/font-awesome/css/font-awesome.css">
      <link rel="stylesheet" href="{{url('/')}}/assets/font-awesome/css/font-awesome.min.css">
  
   

</head>

<body>
    <div class="banner-404">
        <div class="container">
            <div class="wrapper">
                <div class="500-error-img">
                    <img src="{{url('/')}}/images/500-img.jpg" alt="" />
                </div>
                <div class="oops-text-section">
                    <span>Oops!</span> something went wrong
                </div>
                <br>
              <a class="back-btn" href="{{url('/')}}/{{\Request::segment('1')}}"><span><i class="fa fa-long-arrow-left" aria-hidden="true"></i>
                </span>Go Back To Homepage</a>
            </div>

        </div>

    </div>
</body>

</html>