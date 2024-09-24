/*$('#locality').on('keyup',function(){
	var city = $('#locality').val(); 	
  
	$.ajax({
              url  :city_ajax_url,
              type :'POST',
              data :{'city':city ,'_token':token},
              success:function(data){
                $('#locality').append(data);
                $('#locality').trigger('chosen:updated');
            	}  
         });
});

$('#country').on('keyup',function(){
	var country = $('#country').val();
	$.ajax({
              url  :country_ajax_url,
              type :'POST',
              data :{'country':country ,'_token':token},
              success:function(data){
                
            	}  
         });
});*/

$("#locality").keyup(function(){
   var key = $("#locality").val();

   $.ajax({
      type: "get",
      url: city_ajax_url+key,
      data:{'keyword':$(this).val(),'country':$("#country").val()},
      beforeSend: function(){
        $("#locality").css("background","#FFF url(LoaderIcon.gif) no-repeat 165px");
      },
      success: function(data){
        if(data.length==0)
        {
          $("#suggesstion-box").hide(); 
        }
        else
        {
          $("#suggesstion-box").show();
          $("#suggesstion-box").html(data);
          $("#locality").css("background","#FFF");
        }
      }
   });
});

$("#country").keyup(function(){
   var key = $("#country").val();
   $.ajax({
      type: "get",
      url: country_ajax_url+key,
      data:'keyword='+$(this).val(),
      beforeSend: function(){
        $("#country").css("background","#FFF url(LoaderIcon.gif) no-repeat 165px");
      },
      success: function(data){
        if(data.length==0)
        {
          $("#suggesstion-box-country").hide();  
        }
        else
        {
          $("#suggesstion-box-country").show();
          $("#suggesstion-box-country").html(data);
          $("#country").css("background","#FFF");  
        }
        
      }
   });
});

$('#country').on('blur',function(){
    $("#suggesstion-box-country").on('blur',function(){
      $("#suggesstion-box-country").hide();  
    });
});

$('#locality').on('blur',function(){
  $("#suggesstion-box").on('blur',function(){
      $("#suggesstion-box").hide();  
    });
});
