                <?php  
                        $admin_type = ": Professor";
                        $user = Sentinel::check();
                        if($user)
                        {
                            if($user->inRole(config('app.project.role_slug.admin_role_slug')))
                            {
                                $admin_type = ": Professor";
                            }
                        }
                        $locations_url_path = url('/common');  
                    ?> 
                  
                <footer>
                   <div class="col-sm-12 col-md-12 col-lg-12"><p>{{date('Y')}} © {{$footer_name}} {{ $admin_type or '' }}.</p> </div> 
                   <div class="clearfix"></div>
                </footer>

                <a id="btn-scrollup" class="btn btn-circle btn-lg" href="#"><i class="fa fa-chevron-up"></i></a>
           
           
                 <!--main cntent--> 
    </div>
    </div>
    <!--school-admin-main-->
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
        <script type="text/javascript">
            var locations_url_path = '{{$locations_url_path}}';
        </script>
    
        <script src="{{ url('/') }}/assets/jquery-cookie/jquery.cookie.js"></script>

        <script type="text/javascript" src="{{ url('/') }}/assets/jquery-validation/dist/jquery.validate.min.js"></script>
        <script type="text/javascript" src="{{ url('/') }}/assets/jquery-validation/dist/additional-methods.js"></script>

        <script src="{{ url('/') }}/assets/jquery-slimscroll/jquery.slimscroll.min.js"></script>

        <script src="{{ url('/') }}/js/school_admin/flaty.js"></script>
       
         @if(Session::has('locale') && \Session::get('locale') == 'fr')
          <script src="{{ url('/') }}/assets/jquery-validation/localization/messages_fr.js"></script>
          <script src="{{ url('/') }}/js/admin/custom_validation_fr.js"></script>
        @else
              <script src="{{ url('/') }}/js/admin/custom_validation_en.js"></script>
        @endif
        <script src="{{ url('/') }}/js/admin/validation.js"></script>

        <script type="text/javascript" src="{{ url('/') }}/assets/bootstrap-fileupload/bootstrap-fileupload.min.js"></script>

        <script src="{{url('/')}}/assets/data-tables/latest/jquery.dataTables.min.js"></script>
        <script src="{{url('/')}}/assets/data-tables/latest/dataTables.bootstrap.min.js"></script>

       
    </body>
</html>