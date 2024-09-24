function confirm_action(ref,evt,msg,title,confirm,cancel)
{
   var msg = msg || false;
  
    evt.preventDefault();  
    swal({
          title: title,
          text: msg,
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#DD6B55",
          confirmButtonText: confirm,
          cancelButtonText: cancel,
          closeOnConfirm: false,
          closeOnCancel: true
        },
        function(isConfirm)
        {
          if(isConfirm==true)
          {
            // swal("Performed!", "Your Action has been performed on that file.", "success");
            window.location = $(ref).attr('href');
          }
        });
}    

/*---------- Multi_Action-----------------*/

  function check_multi_action(frm_id,title,confirmation_msg,confirm,cancel,oops,oops_msg,action)
  {
    // var len = $('input[name="'+checked_record+'"]:checked').length;

    var len = $('input[name="checked_record[]"]:checked').length;
    var flag=1;
    var frm_ref = $("#"+frm_id);
    
    if(len<=0)
    {
      swal(oops+" ...",oops_msg);
      return false;
    }
    
    /*if(action=='delete')
    {
      var confirmation_msg = "Do you really want to delete selected record(s) ?";
    }
    else if(action == 'deactivate')
    {
      var confirmation_msg =  "Do you really want to deactivate selected record(s) ?";
    }
    else if(action == 'activate')
    {
      var confirmation_msg =  "Do you really want to activate selected record(s) ?";
    }*/
    
    swal({
          title: title,
          text: confirmation_msg,
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#DD6B55",
          confirmButtonText: confirm,
          cancelButtonText: cancel,
          closeOnConfirm: true,
          closeOnCancel: true
        },
        function(isConfirm)
        {

          if(isConfirm)
          {
            $('input[name="multi_action"]').val(action);
            $(frm_ref)[0].submit();
          }
          else
          {
           return false;
          }
        }); 
  }


  /* This function shows simple alert box for showing information */
  function showAlert(msg,type,confirm_btn_txt)
  {
      confirm_btn_txt = confirm_btn_txt || 'Ok';
      swal({
        title: "",
        text: msg,
        type: type,
        confirmButtonText: confirm_btn_txt
      });
      return false; 
  }





