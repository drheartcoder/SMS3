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
        
        <script type="text/javascript">
            var locations_url_path = '{{$locations_url_path}}';
        </script>
        
        
    </body>
</html>