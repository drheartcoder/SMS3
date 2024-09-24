@extends('schooladmin.layout.master')    
@section('main_content')
<style type="text/css">
  body{overflow-x:hidden;}
</style>

<!-- BEGIN Breadcrumb -->
<div id="breadcrumbs">
   <ul class="breadcrumb">
      <li>
         <i class="fa fa-home">
         </i>
         <a href="{{ url($school_admin_panel_slug.'/dashboard') }}">{{translation('dashboard')}}
         </a>
      </li>
      <span class="divider">
      <i class="fa fa-angle-right">
      </i>
      <i class="fa {{$module_icon}}">
      </i>
      </span> 
      <li class="active">  {{ isset($page_title)?$page_title:"" }}
      </li>
   </ul>
</div>
<!-- END Breadcrumb -->

<!-- BEGIN Page Title -->
    <div class="page-title new-agetitle">
        <div>
            <h1><i class="fa fa-money"></i> {{translation('payment_settings')}}</h1>
        </div>
    </div>
<!-- END Page Title -->


                <!-- BEGIN Main Content -->
      <div class="row">
        <div class="col-md-12">
            <div class="box {{ $theme_color }}">
                <div class=" box-title">
                    <h3><i class="fa fa-money"></i> {{translation('payment_settings')}}</h3>
                    <div class="box-tool">
                    </div>
                </div>
                <div class="box-content">

                    @include('admin.layout._operation_status')  
                    <div class="row" >
                      <div class="col-sm-12 col-lg-6">  
                                   {!! Form::open([ 'url' => $school_admin_panel_slug.'/payment_settings/update',
                                 'method'=>'POST',
                                 'id'=>'validation-form1',
                                 'class'=>'form-horizontal' 
                                ]) !!} 
                   
                            {{ csrf_field() }}
                            <h3>Wire Bank Transfer (WBT)</h3><br />
                            <input type="hidden" name="type" value="bank_transfer">
                        <div class="form-group">
                            <label class="col-sm-6 col-lg-4 control-label">Enable Wire Bank <i class="red">*</i></label>
                            <div class="col-sm-6 col-lg-8 controls">

                           <div class="radio-btns">
                              <div class="radio-btn">
                                <input type="radio" name="bank_status" id="bank_status_en" <?php if(isset($arr_data['enable_wire_transfer']) && $arr_data['enable_wire_transfer']==1) {echo "checked";}  ?> value="1" >
                                <label for="bank_status_en">Enable</label>
                                <div class="check"></div>
                              </div>  
                              <div class="radio-btn">
                               <input type="radio" name="bank_status" id="bank_status_dis" <?php if(isset($arr_data['enable_wire_transfer']) && $arr_data['enable_wire_transfer']==0) {echo "checked";}  ?> value="0">  
                                <label for="bank_status_dis">Disable</label>
                                <div class="check"></div>
                              </div>
                            </div>
                            <span class='help-block'>{{ $errors->first('bank_status') }}</span>  
                            </div>
                         </div>
 
                         <div class="form-group">
                            <label class="col-sm-6 col-lg-4 control-label">Sort Order of Display <i class="red">*</i></label>
                            <div class="col-sm-6 col-lg-8 controls">
                               <input type="text" id="transfer_sort_order" name="transfer_sort_order" value="{{isset($arr_data['transfer_sort_order_of_display'])?$arr_data['transfer_sort_order_of_display']:''}}" class="form-control notAllowedZero" id="sort_order_wire" placeholder="Sort Order of Display" data-rule-required="true" data-rule-number="true" data-rule-maxlength="1" >
                               <span class='help-block sortErrorCls' for="sort_order_wire" id="error_transfer_sort_order" style="font-color:red!important;">{{ $errors->first('transfer_sort_order') }}</span>
                            </div>
                         </div>

                         <div class="form-group">
                            <label class="col-sm-6 col-lg-4 control-label">Beneficiary Bank Name <i class="red">*</i></label>
                            <div class="col-sm-6 col-lg-8 controls">
                              <input type="text" name="bank_name" value="{{isset($arr_data['beneficiary_bank_name'])?$arr_data['beneficiary_bank_name']:''}}" class="form-control" placeholder="Beneficiary Bank Name" data-rule-required="true">
                                <span class='help-block'>{{ $errors->first('bank_name') }}</span>
                            </div>
                         </div>

                          <div class="form-group">
                            <label class="col-sm-6 col-lg-4 control-label">Beneficiary Bank Address <i class="red">*</i></label>
                            <div class="col-sm-6 col-lg-8 controls">
                              <textarea name="bank_address"  class="form-control" placeholder="Beneficiary Bank Addres" col="3" rows="5" data-rule-required="true">{{isset($arr_data['beneficiary_bank_address'])?$arr_data['beneficiary_bank_address']:''}}</textarea>
                                <span class='help-block'>{{ $errors->first('bank_address') }}</span>
                            </div>
                         </div>

                          <div class="form-group">
                            <label class="col-sm-6 col-lg-4 control-label">Account Name <i class="red">*</i></label>
                            <div class="col-sm-6 col-lg-8 controls">
                              <input type="text" name="account_name" value="{{isset($arr_data['account_name'])?$arr_data['account_name']:''}}" class="form-control" placeholder="Account Names" data-rule-required="true">
                                <span class='help-block'>{{ $errors->first('account_name') }}</span>
                            </div>
                         </div>

                          <div class="form-group">
                            <label class="col-sm-6 col-lg-4 control-label">Account Number <i class="red">*</i></label>
                            <div class="col-sm-6 col-lg-8 controls">
                              <input type="text" name="account_number" value="{{isset($arr_data['account_number'])?$arr_data['account_number']:''}}" class="form-control" placeholder="Account Number" data-rule-required="true">
                                <span class='help-block'>{{ $errors->first('account_number') }}</span>
                            </div>
                         </div>

                          <div class="form-group">
                            <label class="col-sm-6 col-lg-4 control-label">SWIFT Address <i class="red">*</i></label>
                            <div class="col-sm-6 col-lg-8 controls">
                               <input type="text" name="swift_address" value="{{isset($arr_data['swift_address'])?$arr_data['swift_address']:''}}" class="form-control" placeholder="SWIFT Address" data-rule-required="true">
                                <span class='help-block'>{{ $errors->first('swift_address') }}</span>
                            </div>
                         </div>

                          <div class="form-group">
                            <label class="col-sm-6 col-lg-4 control-label">Bank Code <i class="red">*</i></label>
                            <div class="col-sm-6 col-lg-8 controls">
                               <input type="text" name="bank_code" value="{{isset($arr_data['bank_code'])?$arr_data['bank_code']:''}}" class="form-control" placeholder="Bank Code" data-rule-required="true">
                                <span class='help-block'>{{ $errors->first('bank_code') }}</span>
                            </div>
                         </div>

                          <div class="form-group">
                            <label class="col-sm-6 col-lg-4 control-label">Comment <i class="red"></i></label>
                            <div class="col-sm-6 col-lg-8 controls">
                                <textarea name="comment" class="form-control" placeholder="Comment" col="4" rows="5">{{isset($arr_data['comment'])?$arr_data['comment']:''}}</textarea>
                                <span class='help-block'>{{ $errors->first('comment') }}</span>
                            </div>
                         </div>
                         @if(array_key_exists('payment_settings.update', $arr_current_user_access) || array_key_exists('payment_settings.create', $arr_current_user_access))
                          <div class="form-group">
                               <div class="col-sm-9 col-sm-offset-6 col-lg-10 col-lg-offset-4">
                                  {!! Form::Submit('Save',['class'=>'btn btn-primary']) !!}        
                              </div>
                         </div>
                         @endif
                    {!! Form::close() !!}
                 </div>

                 
                 <!--Code for paypal websiter standard-->

                    <div class="col-sm-12 col-lg-6">  
                                   {!! Form::open([ 'url' => $school_admin_panel_slug.'/payment_settings/update',
                                 'method'=>'POST',
                                 'id'=>'validation-form2',
                                 'class'=>'form-horizontal' 
                                ]) !!} 
                   
                            {{ csrf_field() }}
                            <h3>Paypal Website Standard</h3><br />
                            <input type="hidden" name="type" value="paypal">
                           <div class="form-group">
                            <label class="col-sm-6 col-lg-4 control-label">Enable Paypal <i class="red">*</i></label>
                            <div class="col-sm-6 col-lg-8 controls">
                               <div class="radio-btns">
                                <div class="radio-btn">
                                  <input type="radio" name="payal_status" id="paypal_en" value="1" <?php if(isset($arr_data['enable_paypal']) && $arr_data['enable_paypal']==1) {echo "checked";}  ?> > 
                                  <label for="paypal_en">Enable</label>
                                  <div class="check"></div>
                                </div>
                               <div class="radio-btn"> 
                               <input type="radio" name="payal_status" id="paypal_dis" value="0" <?php if(isset($arr_data['enable_paypal']) && $arr_data['enable_paypal']==0) {echo "checked";}  ?> >
                               <label for="paypal_dis">Disable</label>
                                  <div class="check"></div>
                               </div>
                              </div>  
                                <span class='help-block'>{{ $errors->first('gst_number') }}</span>
                            </div>
                         </div>
 
                         <div class="form-group">
                            <label class="col-sm-6 col-lg-4 control-label">Sort Order of Display <i class="red">*</i></label>
                            <div class="col-sm-6 col-lg-8 controls">
                               <input type="text" name="paypal_sort_order" id="paypal_sort_order" value="{{isset($arr_data['paypal_sort_order_of_display'])?$arr_data['paypal_sort_order_of_display']:''}}" class="form-control notAllowedZero" placeholder="Sort Order of Display" data-rule-required="true" data-rule-number="true" data-rule-maxlength="1" >
                               <span class='help-block sortErrorCls' for="paypal_sort_order" id="error_paypal_sort_order" style="font-color:red!important;">{{ $errors->first('paypal_sort_order') }}</span>
                            </div>
                         </div>

                         <div class="form-group">
                            <label class="col-sm-6 col-lg-4 control-label">Client ID <i class="red">*</i></label>
                            <div class="col-sm-6 col-lg-8 controls">
                              <input type="text" name="mid" value="{{isset($arr_data['mid'])?$arr_data['mid']:''}}" class="form-control" placeholder="MID" data-rule-required="true" >
                                <span class='help-block'>{{ $errors->first('mid') }}</span>
                            </div>
                         </div>

                          <div class="form-group">
                            <label class="col-sm-6 col-lg-4 control-label">Secret key <i class="red">*</i></label>
                            <div class="col-sm-6 col-lg-8 controls">
                              <input type="text" name="merchant_key" value="{{isset($arr_data['merchant_key'])?$arr_data['merchant_key']:''}}" class="form-control" placeholder="Merchant Key" data-rule-required="true">
                                <span class='help-block'>{{ $errors->first('merchant_key') }}</span>
                            </div>
                         </div>
                        @if(array_key_exists('payment_settings.update', $arr_current_user_access) || array_key_exists('payment_settings.create', $arr_current_user_access))
                         
                        <div class="form-group">
                            <div class="col-sm-3 col-sm-offset-6 col-lg-10 col-lg-offset-4">

                                {!! Form::Submit('Save',['class'=>'btn btn-primary']) !!}        

                            </div>
                       </div>
                       @endif
                    {!! Form::close() !!}
                 </div>


                 <!--Code for Cheque Transfer Settings-->

                  <div class="col-sm-12 col-lg-6">  
                                   {!! Form::open([ 'url' => $school_admin_panel_slug.'/payment_settings/update',
                                 'method'=>'POST',
                                 'id'=>'validation-form3',
                                 'class'=>'form-horizontal' 
                                ]) !!} 
                   
                            {{ csrf_field() }}
                           <h3>Cheque Transfer Settings</h3><br />
                           <input type="hidden" name="type" value="cheque_transfer">
                         <div class="form-group">
                            <label class="col-sm-6 col-lg-4 control-label">Enable Cheque Transfer <i class="red">*</i></label>
                            <div class="col-sm-6 col-lg-8 controls">
                              <div class="radio-btns">
                                <div class="radio-btn">
                                 <input type="radio" name="cheque_status" id="en_cheque" value="1" <?php if(isset($arr_data['enable_cheque_transfer']) && $arr_data['enable_cheque_transfer']==1) {echo "checked";}  ?> >
                                 <label for="en_cheque">Enable</label>
                                 <div class="check"></div>
                                </div> 
                                <div class="radio-btn">
                                  <input type="radio" name="cheque_status" value="0" id="dis_cheque" <?php if(isset($arr_data['enable_cheque_transfer']) && $arr_data['enable_cheque_transfer']==0) {echo "checked";}  ?> > 
                                  <label for="dis_cheque">Disable </label>
                                 <div class="check"></div>
                                </div>
                              </div>  
                              <span class='help-block'>{{ $errors->first('cheque_status') }}</span>
                            </div>
                         </div>
 
                        <div class="form-group">
                            <label class="col-sm-6 col-lg-4 control-label">Sort Order of Display <i class="red">*</i></label>
                            <div class="col-sm-6 col-lg-8 controls">
                               <input type="text" name="cheque_sort_order"  id="cheque_sort_order"  value="{{isset($arr_data['cheque_sort_order_of_display'])?$arr_data['cheque_sort_order_of_display']:''}}" class="form-control notAllowedZero" placeholder="Sort Order of Display" data-rule-required="true" data-rule-number="true" data-rule-maxlength="1" >
                               <span class='help-block sortErrorCls' for="cheque_sort_order" id="error_cheque_sort_order" style="font-color:red!important;">{{ $errors->first('cheque_sort_order') }}</span>
                            </div>
                         </div>

                         <div class="form-group">
                            <label class="col-sm-6 col-lg-4 control-label">Cheque Payee Name <i class="red">*</i></label>
                            <div class="col-sm-6 col-lg-8 controls">
                               <input type="text" name="payee_name" pattern="^[a-zA-Z ]*$" value="{{isset($arr_data['cheque_payee_name'])?$arr_data['cheque_payee_name']:''}}" class="form-control" placeholder="Cheque Payee Name" data-rule-required="true" >
                               <span class='help-block'>{{ $errors->first('payee_name') }}</span>
                            </div>
                         </div>

                        @if(array_key_exists('payment_settings.update', $arr_current_user_access) || array_key_exists('payment_settings.create', $arr_current_user_access))                          
                        <div class="form-group">
                            <div class="col-sm-3 col-sm-offset-6 col-lg-10 col-lg-offset-4">

                                {!! Form::Submit('Save',['class'=>'btn btn-primary']) !!}        

                            </div>
                       </div>
                       @endif
                    {!! Form::close() !!}
                 </div>

                 <div class="col-sm-12 col-lg-6">  
                           {!! Form::open([ 'url' => $school_admin_panel_slug.'/payment_settings/update',
                         'method'=>'POST',
                         'id'=>'validation-form4',
                         'class'=>'form-horizontal' 
                        ]) !!} 
                   
                            {{ csrf_field() }}
                           <h3>Cash Settings</h3><br />
                           <input type="hidden" name="type" value="cash_transfer">
                         <div class="form-group">
                            <label class="col-sm-6 col-lg-4 control-label">Enable Cash <i class="red">*</i></label>
                            <div class="col-sm-6 col-lg-8 controls">
                              
                               <div class="radio-btns">
                                <div class="radio-btn"> 
                                  <input type="radio" name="cash_status" id="enable_cash_status" value="1" <?php if(isset($arr_data['enable_cash_transfer']) && $arr_data['enable_cash_transfer']==1) {echo "checked";}  ?> > 
                                  <label for="enable_cash_status">Enable</label>
                                  <div class="check"></div>
                                </div>
                                <div class="radio-btn">   
                                  <input type="radio" name="cash_status" id="disable_cash_status" value="0" <?php if(isset($arr_data['enable_cash_transfer']) && $arr_data['enable_cash_transfer']==0) {echo "checked";}  ?> >  
                                  <label for="disable_cash_status">Disable</label>
                                  <div class="check"></div>
                                </div>
                              </div>    
                              
                            </div>
                         </div>
 
                        <div class="form-group">
                            <label class="col-sm-6 col-lg-4 control-label">Sort Order of Display <i class="red">*</i></label>
                            <div class="col-sm-6 col-lg-8 controls">
                               <input type="text" name="cash_sort_order"  id="cash_sort_order"  value="{{isset($arr_data['cash_sort_order_of_display'])?$arr_data['cash_sort_order_of_display']:''}}" class="form-control notAllowedZero" placeholder="Sort Order of Display" data-rule-required="true" data-rule-number="true" data-rule-maxlength="1" >
                               <span class='help-block sortErrorCls' for="cash_sort_order" id="error_cash_sort_order" style="font-color:red!important;">{{ $errors->first('cash_sort_order') }}</span>
                            </div>
                         </div>
                        @if(array_key_exists('payment_settings.update', $arr_current_user_access) || array_key_exists('payment_settings.create', $arr_current_user_access)) 
                        <div class="form-group">
                            <div class="col-sm-3 col-sm-offset-6 col-lg-10 col-lg-offset-4">

                                {!! Form::Submit('Save',['class'=>'btn btn-primary']) !!}        

                            </div>
                       </div>
                       @endif
                    {!! Form::close() !!}
                 </div>

           </div>     
         </div>  
        </div>
    </div>
  </div>
   <!-- END Main Content -->
   <script type="text/javascript">
      
   /* $(document).on("blur",".notAllowedZero", function()
    {            
       var value = this.value;
       if(value == 0)
       {
         swal('Please enter valid sort order');
         $(this).val('');
       }
    });*/

/*     $(document).ready(function()
     {
        $('#transfer_sort_order').numeric();
        $('#paypal_sort_order').numeric();
        $('#cheque_sort_order').numeric();
    });*/

    $(document).on("change","#transfer_sort_order", function()
    {     
         $('.sortErrorCls').html('');
          var sort_order = $(this).val();
  
          var paypal_sort_order = $("#paypal_sort_order").val();
          var cheque_sort_order = $("#cheque_sort_order").val();
          var cash_sort_order   = $("#cash_sort_order").val();  
          if((sort_order == paypal_sort_order) || (sort_order == cheque_sort_order) || (sort_order == cash_sort_order) )
          {  
              $('#error_transfer_sort_order').html('{{translation('already_exists')}}');  
              $(this).val('');
          }
    });
     


    $(document).on("change","#paypal_sort_order", function()
    {     
         $('.sortErrorCls').html('');

          var sort_order = $(this).val();
          var transfer_sort_order = $("#transfer_sort_order").val();
          var cheque_sort_order = $("#cheque_sort_order").val();
          var cash_sort_order   = $("#cash_sort_order").val();
          if((sort_order == transfer_sort_order) || (sort_order == cheque_sort_order) || (sort_order == cash_sort_order) )
          {  
            
            $('#error_paypal_sort_order').html('{{translation('already_exists')}}');    
            $(this).val('');
          }

    });
     


    $(document).on("change","#cheque_sort_order", function()
    {     
         $('.sortErrorCls').html('');

          var sort_order = $(this).val();
          var paypal_sort_order = $("#paypal_sort_order").val();
          var transfer_sort_order = $("#transfer_sort_order").val();
          var cash_sort_order   = $("#cash_sort_order").val();

          if((sort_order == transfer_sort_order) || (sort_order == paypal_sort_order) || (sort_order == cash_sort_order) )
          {
               $('#error_cheque_sort_order').html('{{translation('already_exists')}}');    
               $(this).val('');
          }

    });
     
 
    $(document).on("change","#cash_sort_order", function()
    {     
         $('.sortErrorCls').html('');

          var sort_order = $(this).val();
          var paypal_sort_order = $("#paypal_sort_order").val();
          var transfer_sort_order = $("#transfer_sort_order").val();
          var cheque_sort_order = $("#cheque_sort_order").val();

          if((sort_order == transfer_sort_order) || (sort_order == paypal_sort_order) || (sort_order == cheque_sort_order) )
          {
            $('#error_cash_sort_order').html('{{translation('already_exists')}}');    
            
              $(this).val('');
          }

    });

   </script>
                
@endsection

