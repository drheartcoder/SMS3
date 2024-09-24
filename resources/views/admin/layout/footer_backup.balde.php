                <?php  
                        $admin_type = ": Admin";
                        $user = Sentinel::check();
                        if($user)
                        {
                            if($user->inRole(config('app.project.role_slug.admin_role_slug')))
                            {
                                $admin_type = ": Admin";
                            }
                            else if($user->inRole(config('app.project.role_slug.subadmin_role_slug')))
                            {
                                $admin_type = ": Sub-Admin";
                            }
                        }

                        $locations_url_path = url('/common');

                        
                    ?>   
                <footer>
                   <div class="col-sm-12 col-md-12 col-lg-12"><p>{{date('Y')}} © Master Setup {{ $admin_type or '' }}.</p> </div> 
                   <div class="clearfix"></div>
                </footer>

                <a id="btn-scrollup" class="btn btn-circle btn-lg" href="#"><i class="fa fa-chevron-up"></i></a>
        </div>

    <script type="text/javascript">
      function addLoader(){   
                $('#validation-form1').submit(function(event) {
                    if($('.has-error').length > 0){
                       event.preventDefault();
                    }else{
                        $("#submit_button").html("<b><i class='fa fa-spinner fa-spin'></i></b> Processing...");
                        $("#submit_button").attr('disabled', true);
                    }
                });
            }
    </script>      
              
            <!-- END Content -->
        </div>
        {{--    --}}
        <script type="text/javascript">
            var locations_url_path = '{{$locations_url_path}}';
        </script>
        {{-- <script src="{{ url('/') }}/assets/jquery/jquery-2.1.4.min.js"></script> --}}
<!--    <script src="{{ url('/') }}/js/admin/parsley.extend.js"></script>-->
    
        
        {{-- <script src="{{ url('/') }}/js/admin/location.js"></script> --}}
       {{--  <script type="text/javascript" src="{{ url('/') }}/assets/bootstrap/js/bootstrap.js"></script> --}}
        <script type="text/javascript" src="{{ url('/') }}/assets/jquery-validation/dist/jquery.validate.min.js"></script>
        <script type="text/javascript" src="{{ url('/') }}/assets/jquery-validation/dist/additional-methods.js"></script>
        <script src="{{ url('/') }}/assets/bootstrap/js/bootstrap.min.js"></script>
        <script src="{{ url('/') }}/assets/jquery-slimscroll/jquery.slimscroll.min.js"></script>
        <script src="{{ url('/') }}/assets/jquery-cookie/jquery.cookie.js"></script>

        <!--page specific plugin scripts-->
        <script src="{{ url('/') }}/assets/flot/jquery.flot.js"></script>
        <script src="{{ url('/') }}/assets/flot/jquery.flot.resize.js"></script>
        <script src="{{ url('/') }}/assets/flot/jquery.flot.pie.js"></script>
        <script src="{{ url('/') }}/assets/flot/jquery.flot.stack.js"></script>
        <script src="{{ url('/') }}/assets/flot/jquery.flot.crosshair.js"></script>
        {{--<script src="{{ url('/') }}/assets/flot/jquery.flot.tooltip.min.js"></script>--}}
        <script src="{{ url('/') }}/assets/sparkline/jquery.sparkline.min.js"></script>

        <script type="text/javascript" src="{{ url('/') }}/assets/bootstrap-switch/static/js/bootstrap-switch.js"></script>
        <script type="text/javascript" src="{{ url('/') }}/assets/bootstrap-wysihtml5/wysihtml5-0.3.0.js"></script> 
        <script type="text/javascript" src="{{ url('/') }}/assets/bootstrap-wysihtml5/bootstrap-wysihtml5.js"></script>
        <script type="text/javascript" src="{{ url('/') }}/assets/ckeditor/ckeditor.js"></script> 
        <script type="text/javascript" src="{{ url('/') }}/assets/bootstrap-fileupload/bootstrap-fileupload.min.js"></script>

        <script src="{{ url('/') }}/assets/bootstrap-wizard/jquery.bootstrap.wizard.js"></script>
        <script type="text/javascript" src="{{ url('/') }}/assets/chosen-bootstrap/chosen.jquery.min.js"></script>
         <script src="//cdn.tinymce.com/4/tinymce.min.js"></script>
        <!--flaty scripts-->
        <script src="{{ url('/') }}/js/admin/flaty.js"></script>
        <script src="{{ url('/') }}/js/admin/flaty-demo-codes.js"></script>
        <script src="{{ url('/') }}/js/admin/validation.js"></script>
        <script src="{{ url('/') }}/js/admin/base64.js"></script>

        <script type="text/javascript" src="{{url('/')}}/assets/jquery-validation/dist/additional-methods.min.js"></script>

        <script src="{{url('/')}}/assets/data-tables/latest/jquery.dataTables.min.js"></script>
        <script src="{{url('/')}}/assets/data-tables/latest/dataTables.bootstrap.min.js"></script>
        
         <!-- date picker js -->
        <script type="text/javascript" language="javascript" src="js/bootstrap-datepicker.min.js"></script>
        
        
    </body>
</html>