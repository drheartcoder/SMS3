          
            <?php   $locations_url_path = url('/common'); ?> 
                  
                <footer>
                   <div class="col-sm-12 col-md-12 col-lg-12"><p>{{date('Y')}} &copy; {{ config('app.project.name') }} : <?php echo  ucfirst($parent_panel_slug) ?></p> </div> 
                   <div class="clearfix"></div>
                </footer>
                <a id="btn-scrollup" class="btn btn-circle btn-lg" href="#"><i class="fa fa-chevron-up"></i></a>
                <!--main cntent--> 
    </div>
    <!-- </div> -->
    {{ csrf_field() }}
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
    

        /* change the users kids  */
        $(document).on("change", ".parent_kids_arr_cls", function(){
            var kidId = $(this).val();
            var level_class_id =  $('option:selected', this).attr('data-kid-level-class');  

            if(kidId == ''){
             swal('Please select Kid')
            }
            else{
                $('#loader').fadeIn('slow');
                $('body').addClass('loader-active');
                $.ajax({
                    headers:{'X-CSRF-Token': $('input[name="_token"]').val()},
                    url : '{{ url($parent_panel_slug)}}/set_parent_kid',
                    type : "POST",
                    data : {kidId:kidId,level_class_id:level_class_id},
                    beforeSend:function(data, statusText, xhr, wrapper){
                    },
                    success:function(data){
                    
                        window.location.reload();
                    

                    }
              });
                
            }


        });
        /* change the users kids  */

    </script>          
        <script type="text/javascript">
            var locations_url_path = '{{$locations_url_path}}';
        </script>
        
        
       
        <script src="{{ url('/') }}/assets/jquery-cookie/jquery.cookie.js"></script>

        <script type="text/javascript" src="{{ url('/') }}/assets/jquery-validation/dist/jquery.validate.min.js"></script>
        <script type="text/javascript" src="{{ url('/') }}/assets/jquery-validation/dist/additional-methods.js"></script>

        <script src="{{ url('/') }}/assets/jquery-slimscroll/jquery.slimscroll.min.js"></script>

        <script src="{{ url('/') }}/js/school_admin/flaty.js"></script>
       {{--  <script src="{{ url('/') }}/js/admin/flaty-demo-codes.js"></script> --}}
        <script src="{{ url('/') }}/js/admin/validation.js"></script>

        <script type="text/javascript" src="{{ url('/') }}/assets/bootstrap-fileupload/bootstrap-fileupload.min.js"></script>

        <script src="{{url('/')}}/assets/data-tables/latest/jquery.dataTables.min.js"></script>
        <script src="{{url('/')}}/assets/data-tables/latest/dataTables.bootstrap.min.js"></script>
        
        @if(Session::has('locale') && \Session::get('locale') == 'fr')
          <script src="{{ url('/') }}/assets/jquery-validation/localization/messages_fr.js"></script>
          <script src="{{ url('/') }}/js/admin/custom_validation_fr.js"></script>
        @else
              <script src="{{ url('/') }}/js/admin/custom_validation_en.js"></script>
        @endif
       
    </body>
</html>