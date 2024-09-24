<?php
namespace App\Http\Controllers\SchoolAdmin;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\PaymentSettingModel;
use App\Events\ActivityLogEvent;
use App\Models\ActivityLogsModel;

use Validator;
use Flash;
use Sentinel;
use Hash;
use Image;

class PaymentSettingsController extends Controller
{

    public function __construct(
                                PaymentSettingModel $payment_settings,
                                ActivityLogsModel $activity_logs
                               )
    {
        $this->PaymentSettingModel = $payment_settings;
        $this->BaseModel          = $this->PaymentSettingModel;
        $this->ActivityLogsModel  = $activity_logs;
        
        $this->arr_view_data      = [];
        
        $this->module_url_path    = url(config('app.project.school_admin_panel_slug')).'/payment_settings';

        $this->module_title       = translation('payment_settings');
        $this->module_view_folder = "schooladmin.payment_settings";
        
        $this->theme_color        = theme_color();
        $this->module_icon        = "fa-cogs";

         /* Activity Section */
        $this->first_name = $this->last_name =$this->ip_address ='';
        $obj_data          = Sentinel::getUser();
        if($obj_data){
            $this->first_name        = $obj_data->first_name;
            $this->last_name         = $obj_data->last_name;
            $this->user_id           = $obj_data->id;
        }
        /* Activity Section */


    }
    public function index()
    {

        $arr_account_settings = array();

        $arr_data  = [];
        
        $obj_data  = $this->PaymentSettingModel->where('user_id',$this->user_id)->first();
        
        if($obj_data)
        {
           $arr_data = $obj_data->toArray();    
        }
        else
        {

          $obj_data  = $this->PaymentSettingModel->where('user_id',1)->first();
          if(isset($obj_data) && $obj_data!=null)
          {
            $arr_data = $obj_data->toArray();       
          }
          
        }
        
        $this->arr_view_data['arr_data']        = $arr_data;
        $this->arr_view_data['page_title']      = translation('payment_settings');
        $this->arr_view_data['module_title']    = translation('payment_settings');
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        $this->arr_view_data['module_icon']     = $this->module_icon;
       
        return view($this->module_view_folder.'.index',$this->arr_view_data);
    }
 

    public function update(Request $request)
    {

        $arr_rules = array();
        $type = '';
        $type = $request->input('type');

        //for Wire Bank Transfer 
        if($type=='bank_transfer')
        {
            $arr_rules['bank_status']            = "required";
            $arr_rules['transfer_sort_order']    = "required|max:1"; 
            $arr_rules['bank_name']              = "required";
            $arr_rules['bank_address']           = "required";
            $arr_rules['account_name']           = "required";
            $arr_rules['account_number']         = "required";
            $arr_rules['swift_address']          = "required";
            $arr_rules['comment']                = "required";
        }

        //for PayPal Website Standard
        else if($type=='paypal')
        {
           $arr_rules['payal_status']           = "required";
           $arr_rules['paypal_sort_order']      = "required|max:1"; 
           $arr_rules['mid']                    = "required";
           $arr_rules['merchant_key']           = "required";
        }
        //for Cheque Transfer Settings
        else if($type=='cheque_transfer')
        {
           $arr_rules['cheque_status']          = "required";
           $arr_rules['cheque_sort_order']      = "required|max:1"; 
            $arr_rules['payee_name']             = "required"; 
        }

        $messages = array('max'                => translation('please_enter_no_more_than_1_characters'),
                          'required'           => translation('this_field_is_required')
                        );
        $validator = Validator::make($request->all(),$arr_rules);

        if($validator->fails())
        {      
            return redirect()->back()->withErrors($validator)->withInput();  
        }

        $form_data = $request->all();
        $arr_data  = array();

        $bank_transferSort =0;
        $paypal_sort_order = 0;
        $cheque_transfer_sort_order =0;

        $getData = $this->BaseModel->where('id',$this->user_id)->first();
        if($getData)
        {
          $data = $getData->toArray();
         
          $bank_transferSort = $data['transfer_sort_order_of_display'];
          $paypal_sort_order = $data['paypal_sort_order_of_display'];
          $cheque_transfer_sort_order = $data['cheque_sort_order_of_display'];
        }
        $error = '';
        if($type=='bank_transfer')
        {
            if( $form_data['transfer_sort_order'] == $paypal_sort_order ||   $form_data['transfer_sort_order'] == $cheque_transfer_sort_order )
            {
              $error = 1;
             
            }
        }

        if($type=='paypal')
        {
            if( $form_data['paypal_sort_order'] == $bank_transferSort ||   $form_data['paypal_sort_order'] == $cheque_transfer_sort_order )
            {
              $error = 1;
            }
        }


        if($type=='cheque_transfer')
        {
            if( $form_data['cheque_sort_order'] == $paypal_sort_order ||   $form_data['cheque_sort_order'] == $bank_transferSort )
            {
               $error = 1;
            }
        }

        if($type=='cash_transfer')
        {
            if( $form_data['cash_sort_order'] == $paypal_sort_order ||   $form_data['cash_sort_order'] == $bank_transferSort )
            {
               $error = 1;
            }
        }

        if($error == 1 )
        {
          Flash::error('Sort order already exists for another payment setting');  
          return redirect()->back(); 
        }

       

        if($type=='bank_transfer')
        {
          $arr_data['user_id']                          = $this->user_id;
          $arr_data['enable_wire_transfer']             = $form_data['bank_status'];
          $arr_data['transfer_sort_order_of_display']   = $form_data['transfer_sort_order'];  
          $arr_data['beneficiary_bank_name']            = $form_data['bank_name'];
          $arr_data['beneficiary_bank_address']         = $form_data['bank_address'];                      
          $arr_data['account_name']                     = $form_data['account_name'];  
          $arr_data['account_number']                   = $form_data['account_number'];  
          $arr_data['swift_address']                    = $form_data['swift_address'];  
          $arr_data['bank_code']                        = $form_data['bank_code'];  
          $arr_data['comment']                          = $form_data['comment'];  
       }
       else if($type=='paypal')
       {
          $arr_data['enable_paypal']                    = $form_data['payal_status'];  
          $arr_data['paypal_sort_order_of_display']     = $form_data['paypal_sort_order'];  
          $arr_data['mid']                              = $form_data['mid'];  
          $arr_data['merchant_key']                     = $form_data['merchant_key'];  
       }
       else if($type=='cheque_transfer')
       {
          $arr_data['enable_cheque_transfer']           = $form_data['cheque_status']; 
          $arr_data['cheque_sort_order_of_display']     = $form_data['cheque_sort_order']; 
          $arr_data['cheque_payee_name']                = $form_data['payee_name'];  
       }
       else if($type=='cash_transfer')
       {
          $arr_data['enable_cash_transfer']           = $form_data['cash_status']; 
          $arr_data['cash_sort_order_of_display']     = $form_data['cash_sort_order']; 
       }

        $obj_data = $this->PaymentSettingModel->updateOrCreate(['user_id'=>$this->user_id],$arr_data);
        
        if($obj_data)
        {

            /*-------------------------------------------------------
            |   Activity log Event
            --------------------------------------------------------*/
            $arr_event                     = [];
            $arr_event['ACTIVITY_MESSAGE'] = str_singular($this->module_title).' Updated By '.$this->first_name." ".$this->last_name."";
            $arr_event['IP_ADDRESS']       = $this->ip_address;
            $arr_event['ACTION']           = 'EDIT';
            $arr_event['MODULE_TITLE']     = $this->module_title;
            $this->save_activity($arr_event);
            /*----------------------------------------------------------------------*/

            Flash::success(str_singular($this->module_title).' '.translation('updated_successfully')); 
        }
        else
        {
            Flash::error(translation('problem_occured_while_updating').' '.str_singular($this->module_title));  
        } 
      
        return redirect()->back();
  }
   
}
