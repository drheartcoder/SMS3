@extends('parent.layout.master')    
@section('main_content')
<link rel="stylesheet" type="text/css" href="{{ url('/') }}/assets/chosen-bootstrap/chosen.min.css" />



<style type="text/css">
.profile-img{width: 130px;height: 130px;border-radius: 50% !important;overflow: hidden;padding: 0;}
.profile-img img{height: 100% !important;width: 100% ;}
</style>


<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
    <ul class="breadcrumb">
        <li>
            <i class="fa fa-home"></i>
            <a href="{{ url($parent_panel_slug) }}/dashboard">{{translation('dashboard')}}</a>
        </li>
        <span class="divider">
            <i class="fa fa-angle-right"></i>
        </span>
        <li> 
          <i class="{{$module_icon}}"></i>
          <li class="active">{{$page_title}}</li>
        </li>
    </ul>
</div>
<!-- END Breadcrumb -->

<!-- BEGIN Page Title -->
 <div class="page-title new-agetitle">
    <div>
        <h1>{{$page_title}}</h1>
    </div>
</div>

<!-- END Page Title -->

<!-- BEGIN Tiles -->
            <div class="row">
            <div class="col-md-12">
                <div class="box  box-navy_blue">
                    <div class="box-title">
                        <h3>{{-- <i class=""></i>Your Order-Checkout --}}</h3>
                    </div>
                    <div class="box-content studt-padding">
                        {!! Form::open([ 'url' => $module_url_path.'/store_payment',
                         'method'=>'POST',
                         'enctype'=>"multipart/form-data",
                        'id'=>"validation-form1" 
                         ]) !!}
                         {{ csrf_field() }}
                         
                            <div class="clearfix"> </div>
                            <div class="border-box">
                                <div class="table-responsive" style="border:0">
                                    <input type="hidden" name="multi_action" value="" />
                                    <table class="table table-advance canteenorder" id="table_module">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <a>{{translation('type')}}</a>
                                                </th>
                                                <th>
                                                    <a>{{translation('total')}} ({{config('app.project.currency')}})</a>
                                                </th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr role="row">
                                                <td>
                                                  {{translation('main_fees')}}
                                                </td>
                                                <td class="sorting_1">

                                                  {{number_format($main_fees_amount,2)}} 
                                                  <input type="hidden" name="main_fees_amount" value="{{$main_fees_amount}}">
                                                </td>
                                            </tr>
                                            <tr role="row">
                                                <td>
                                                  {{translation('club_fees')}}
                                                </td>
                                                <td class="sorting_1">
                                                  {{number_format($club_fees_amount,2)}} 
                                                  <input type="hidden" name="club_fees_amount" value="{{$club_fees_amount}}">
                                                </td>
                                            </tr>
                                            <tr role="row">
                                                <td>
                                                  {{translation('bus_fees')}}
                                                </td>
                                                <td class="sorting_1">
                                                  {{number_format($bus_fees_amount,2)}} 
                                                  <input type="hidden" name="bus_fees_amount" value="{{$bus_fees_amount}}">
                                                </td>
                                            </tr>
                        
                                            <tr role="row">
                                                <td class="sorting_1 text-right"><b>{{translation('total_amount')}}</b></td>
                                                <td><b> {{ number_format(round($main_fees_amount+$bus_fees_amount+$club_fees_amount,2),2) }} {{config('app.project.currency')}}</b></td>
                                                <input type="hidden" name="total_fees_amount" value="{{ round($main_fees_amount+$bus_fees_amount+$club_fees_amount,2)}}">
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="clearfix"></div>

                                <div class="student-cateeenorder-main-payment">
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-10 col-lg-offset-1 col-md-offset-0">
                                        <div class="payment-method-redio">
                                                <div class="mrg-tpspay">    
                                            @foreach($arr_sorted as $value)
                                            
                                            
                                                @if($value=='cash')
                                                    <div class="radio-btn mr-tp cash-paymnt">
                                                        <div class="paymt-radio">
                                                            <input type="radio" id="f-option" class="rdoPayment" name="payment_mode" value="cash" onclick="show1();" checked>
                                                            <label for="f-option">{{translation('cash')}}</label>
                                                            <div class="check"></div>
                                                        </div>
                                                    </div>    
                                                @endif
                                            
                                            
                                                @if($value=='paypal')
                                                    <div class="radio-btn sectd-img">
                                                        <input type="radio" id="s-option" class="rdoPayment" name="payment_mode" value="online" onclick="show2();">
                                                        <label for="s-option">{{translation('online')}}</label>
                                                        <div class="check">
                                                            <div class="inside"></div>
                                                        </div>
                                                    </div>
                                                @endif
                                            
                                            
                                                @if($value=='wire_transfer')
                                                    <div class="radio-btn wire-btn">
                                                        <div class="paymt-radio">
                                                            <input type="radio" id="r-option" class="rdoPayment" name="payment_mode" value="wire_transfer" onclick="show3();">
                                                            <label for="r-option">{{translation('wire_transfer')}}</label>
                                                            <div class="check"></div>
                                                        </div>
                                                    </div>        
                                                @endif
                                            
                                                @if($value=='cheque')
                                                    <div class="radio-btn cheque-btn">
                                                        <input type="radio" id="t-option" class="rdoPayment" name="payment_mode" value="cheque" onclick="show4();">
                                                        <label for="t-option">{{translation('cheque')}}</label>
                                                        <div class="check">
                                                            <div class="inside"></div>
                                                        </div>
                                                    </div>
                                                @endif
                                            
                                            @endforeach
                                                <div class="clearfix"></div>
                                            </div>
                                        
                                        </div>
                                        <div class="col-xs-12 col-sm-12 col-md-8 col-lg-6 col-md-offset-2 col-lg-offset-3">
                                            <div id="div1" class="hide-block">
                                            <div class="cart-box">
                                               
                                                    <div class="first-name-block">
                                                        <div class="first-name-block">
                                                            <div class="first-txt-input">
                                                                <input name="card type" placeholder="Card Type" type="text" />
                                                            </div>
                                                        </div>
                                                        <div class="first-txt-input input-pay">
                                                            <input name="Card Number" class="input-payment" placeholder="Card Number" type="text" />
                                                            <img src="{{url('images/pay.png')}}" alt="">
                                                        </div>
                                                    </div>
                                                    <div class="first-name-block">
                                                        <div class="row">
                                                            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                                                <div class="first-txt-input"><input name="Expiration Date" placeholder="Expiration Date" type="text" /></div>
                                                            </div>
                                                            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                                                <div class="first-txt-input top-space-ste"><input name="First name" class="max-widhtfits" placeholder="CVV" type="text" />
                                                                    <div class="img-cartds"><img src="{{url('images/credite_card.png')}}" alt=""></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="first-name-block">
                                                        <div class="first-txt-input">
                                                            <input name="Name on Card" placeholder="Name on Card" type="text" />
                                                        </div>
                                                    </div>
                                                    <div class="first-name-block">
                                                        <div class="first-txt-input">
                                                            <input name="Acceptance of Terms & Conditions" placeholder="Acceptance of Terms & Conditions" type="text" />
                                                        </div>
                                                    </div>
                                                    <div class="clearfix"></div>
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                            <div id="div2" class="hide-block">
                                            <div class="cart-box">
                                              
                                              
                                                    <div class="main-view-txt">
                                                        <div class="row">
                                                            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                                                <div class="main-view-txt-bold">{{translation('beneficiary_bank_name')}}</div>
                                                            </div>
                                                            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                                                <label class="main-view-txt-light">{{isset($arr_settings['beneficiary_bank_name']) ? $arr_settings['beneficiary_bank_name'] : ''}}</label>
                                                            </div>
                                                        </div>
                                                    </div>    
                                                    <div class="main-view-txt">
                                                        <div class="row">
                                                            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                                                <div class="main-view-txt-bold">{{translation('beneficiary_bank_address')}}</div>
                                                            </div>
                                                            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                                                <label class="main-view-txt-light">{{isset($arr_settings['beneficiary_bank_address']) ? $arr_settings['beneficiary_bank_address'] :''}}</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="main-view-txt">
                                                        <div class="row">
                                                            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                                                <div class="main-view-txt-bold">{{translation('account_name')}}</div>
                                                            </div>
                                                            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                                                <label class="main-view-txt-light">{{isset($arr_settings['account_name']) ? $arr_settings['account_name'] :'' }}</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="main-view-txt">
                                                        <div class="row">
                                                            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                                                <div class="main-view-txt-bold">{{translation('account_number')}}</div>
                                                            </div>
                                                            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                                                <label class="main-view-txt-light">{{isset($arr_settings['account_number']) ? $arr_settings['account_number'] :''}}</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="main-view-txt">
                                                        <div class="row">
                                                            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                                                <div class="main-view-txt-bold">{{translation('account_number')}}</div>
                                                            </div>
                                                            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                                                <label class="main-view-txt-light">{{isset($arr_settings['account_number']) ? $arr_settings['account_number'] :'' }}</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="main-view-txt">
                                                        <div class="row">
                                                            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                                                <div class="main-view-txt-bold">{{translation('swift_address')}}</div>
                                                            </div>
                                                            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                                                <label class="main-view-txt-light">{{isset($arr_settings['swift_address']) ? $arr_settings['swift_address']:'' }}</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="main-view-txt">
                                                        <div class="row">
                                                            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                                                <div class="main-view-txt-bold">{{translation('bank_code')}}</div>
                                                            </div>
                                                            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                                                <label class="main-view-txt-light">{{isset($arr_settings['bank_code']) ? $arr_settings['bank_code']:'' }}</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="main-view-txt">
                                                        <div class="row">
                                                            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                                                <div class="main-view-txt-bold">{{translation('comment')}}</div>
                                                            </div>
                                                            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                                                <label>{{isset($arr_settings['comment']) ? $arr_settings['comment'] :''}}</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="main-view-txt">
                                                        <div class="row">
                                                            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                                                <div class="main-view-txt-bold">{{translation('receipt')}}</div>
                                                            </div>
                                                            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6  input-group-block">
                                                                <div class="upload-block-clone">
                                                                    <input type="file" id="pdffile_0" class="hidden-input-block" name="receipt" onchange="Changefilename(this)" >
                                                                    <div class="input-group">
                                                                        <input type="text" class="form-control file-caption  kv-fileinput-caption" id="subfile" />
                                                                        <div class="btn btn-primary btn-file"><a class="file" onclick="$('#pdffile_0').click();">Browse...</a></div>
                                                                     </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="clearfix"></div>
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                            <div id="div3" class="hide-block">
                                            <div class="cart-box">
<!--
                                                    <div class="first-name-block">
                                                        <div class="row">
                                                        </div>
                                                    </div>    
                                                    <div class="first-name-block">
                                                        <div class="row">
                                                        </div>
                                                    </div>    
                                                    <div class="first-name-block">
                                                        <div class="row">
                                                        </div>
                                                    </div>    
-->
                                                    <div class="first-name-block">
                                                        <div class="row">
                                                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                                                <label class="payeename-fnts"><b>{{translation('payee_name')}}</b> :</label>
                                                                <label class="payeename-fnts-light">{{isset($arr_settings['cheque_payee_name']) ? $arr_settings['cheque_payee_name'] :''}}</label>
                                                            </div>
                                                          
                                                        </div>
                                                    </div>

                                                    <div class="first-name-block">
                                                        <div class="first-txt-input">
                                                            <input name="bank_name" placeholder="{{translation('bank_name')}}" type="text" id="bank_name"/>
                                                        </div>
                                                    </div>

                                                    <div class="first-name-block">
                                                        <div class="first-txt-input">
                                                            <input name="account_holder_name" placeholder="{{translation('account_holder_name')}}" id="account_holder_name" type="text" />
                                                        </div>
                                                    </div>

                                                    <div class="first-name-block">
                                                        <div class="first-txt-input">
                                                            <input name="cheque_number" placeholder="{{translation('cheque_number')}}" id="cheque_number" type="text" />
                                                        </div>
                                                    </div>

                                                    <div class="clearfix"></div>
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                            <div class="clearfix"></div>
                                               
                                                <div class="btn-d mrg-top addclass">
                                                    <button class="btn-org grays-clr" type="submit" >{{translation('pay_now')}}</button>
                                                </div>
                                            </div>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </form>
                    </div>
                    <div class="form-group back-btn-form-block">
                                   <div class="controls">
                                      <a href="{{ $module_url_path }}" class="btn btn-primary" style="float: right; margin-top: 20px"><i class="fa fa-arrow-left"></i> {{translation('back')}} </a>
                                   </div>
                                </div><div class="clearfix"></div>
                </div>
            </div>
            <!-- END Main Content -->
        </div>
    </div>

    <script>
 function validateDocument(files,type,element_id) 
 {
    //var default_img_path = site_url+'/front/images/uploadimg.png';

    if (typeof files !== "undefined") 
    {
      for (var i=0, l=files.length; i<l; i++) 
      {     
            var blnValid = false;
            var ext = files[i]['name'].substring(files[i]['name'].lastIndexOf('.') + 1);
            if(type=='Doc')
            {
                if(ext=="jpg" || ext=='jpeg' || ext=='bmp')
                {
                    blnValid = true; 
                }  
            }
            else
            {
                if(ext=="jpg" || ext=='jpeg' || ext=='bmp')
                {
                      blnValid = true;
                }  
            }
            
            if(blnValid ==false) 
            {
              if(type=='Doc')
              {
                showAlert("Sorry, " + files[0]['name'] + " is invalid, allowed extensions are: jpg, jpeg, bmp","error");
              }
              else
              {
                showAlert("Sorry, " + files[0]['name'] + " is invalid, allowed extensions are: jpg, jpeg, bmp","error");
              }
                return false;
            }
            else
            {   
                    /*var reader = new FileReader();
                    reader.readAsDataURL(files[0]);
                    reader.onload = function (e) 
                    { 
                            var image = new Image();
                            image.src = e.target.result;
                               
                            image.onload = function () 
                            {
                                
                                var height = this.height;
                                var width = this.width;

                                if (this.height < 150 || this.width <  150 ) 
                                { 
                                    showAlert("Height and Width must be greater than or equal to 150 X 150." ,"error");
                                    $(".fileupload-preview").html("");
                                    $(".fileupload").attr('class',"fileupload fileupload-new");
                                    $("#image").val('');
                                    return false;
                                }
                                else if (this.height > 2000 || this.width >  2000 ) 
                                { 
                                    showAlert("Height and Width must be less than or equal to 2000 X 2000." ,"error");
                                    $(".fileupload-preview").html("");
                                    $(".fileupload").attr('class',"fileupload fileupload-new");
                                    $("#image").val('');
                                    return false;
                                }
                                else
                                {
                                   //swal("Uploaded image has valid Height and Width.");
                                   return true;
                                }
                            };
         
                    }*/

                    if(files[0].size>10485760)
                    {
                     showAlert("File size should be less than 10 MB","error");
                    }
                }       
            }                
        }
        else
        {
          showAlert("No support for the File API in this web browser" ,"error");
        } 
  }
    </script>

  <script type="text/javascript">

        function Changefilename(event){
                      var file = event.files;
                      validateDocument(event.files,'Doc',null);
                      $(event).next().children('input').val(file[0].name);
                  }

        $(document).on('click', '.rdoPayment', function(){
            var rdoPayment = $(this).val();
            if(rdoPayment=='cash'){ $('.btn-d').addClass('addclass'); }
            else{ $('.btn-d').removeClass('addclass'); }
        });
        function show1() {
            document.getElementById('div1').style.display = 'none';
            document.getElementById('div2').style.display = 'none';
            document.getElementById('div3').style.display = 'none';

            $("#account_holder_name").removeAttr('data-rule-required');
            $("#bank_name").removeAttr('data-rule-required');
            $("#cheque_number").removeAttr('data-rule-required');

            $("#account_holder_name").removeAttr('pattern');
            $("#bank_name").removeAttr('pattern');
            $("#cheque_number").removeAttr('pattern');
            
            $("#account_holder_name").next('span').html('');
            $("#bank_name").next('span').html('');
            $("#cheque_number").next('span').html('');

        }
        
        function show2() {
            document.getElementById('div1').style.display = 'block';
            document.getElementById('div2').style.display = 'none';
            document.getElementById('div3').style.display = 'none';

            $("#account_holder_name").removeAttr('data-rule-required');
            $("#bank_name").removeAttr('data-rule-required');
            $("#cheque_number").removeAttr('data-rule-required');

            $("#account_holder_name").removeAttr('pattern');
            $("#bank_name").removeAttr('pattern');
            $("#cheque_number").removeAttr('pattern');

            $("#account_holder_name").next('span').html('');
            $("#bank_name").next('span').html('');
            $("#cheque_number").next('span').html('');

        }

        function show3() {
            document.getElementById('div1').style.display = 'none';
            document.getElementById('div2').style.display = 'block';
            document.getElementById('div3').style.display = 'none';

            $("#account_holder_name").removeAttr('data-rule-required');
            $("#bank_name").removeAttr('data-rule-required');
            $("#cheque_number").removeAttr('data-rule-required');

            $("#account_holder_name").removeAttr('pattern');
            $("#bank_name").removeAttr('pattern');
            $("#cheque_number").removeAttr('pattern');

            $("#account_holder_name").next('span').html('');
            $("#bank_name").next('span').html('');
            $("#cheque_number").next('span').html('');

        }

        function show4() {
            document.getElementById('div1').style.display = 'none';
            document.getElementById('div2').style.display = 'none';
            document.getElementById('div3').style.display = 'block';

            $("#account_holder_name").attr('data-rule-required','true');
            $("#bank_name").attr('data-rule-required','true');
            $("#cheque_number").attr('data-rule-required','true');

            $("#account_holder_name").attr('pattern',"^[a-zA-Z0-9 ]+$");
            $("#bank_name").attr('pattern',"^[a-zA-Z ]+$");
            $("#cheque_number").attr('data-rule-digits','true');
        }
        
    
    </script>
@endsection 