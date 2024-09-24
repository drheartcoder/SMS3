<?php

namespace App\Http\Controllers\SchoolAdmin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Common\Services\CommonDataService;
use App\Common\Services\StudentService;
use App\Models\FeesSchoolModel;
use App\Models\ClubStudentsModel;
use App\Models\BusStudentsModel;
use App\Models\AcademicYearModel;
use App\Models\FeesTransactionModel;
use App\Models\PaymentSettingModel;
use App\Models\TransactionDetailsModel;
use App\Models\BrotherhoodModel;
use App\Models\SchoolAdminModel;

use Session;
use Validator;
use Flash;
use Sentinel;

class PaymentController extends Controller
{
    public function __construct(CommonDataService $CommonDataService,
    							StudentService $StudentService){   	

        $this->CommonDataService = $CommonDataService;
        $this->StudentService    = $StudentService;

        $this->FeesSchoolModel         = new FeesSchoolModel();
        $this->ClubStudentsModel       = new ClubStudentsModel();
        $this->BusStudentsModel        = new BusStudentsModel();
        $this->AcademicYearModel       = new AcademicYearModel();
        $this->FeesTransactionModel    = new FeesTransactionModel();
        $this->PaymentSettingModel     = new PaymentSettingModel();        
        $this->TransactionDetailsModel = new TransactionDetailsModel();
        $this->BrotherhoodModel        = new BrotherhoodModel();
        $this->SchoolAdminModel        = new SchoolAdminModel();

        $this->arr_view_data      = [];
        $this->module_url_path    = url(config('app.project.school_admin_panel_slug')).'/payment';
        $this->module_title       = translation('payment');
        
        $this->module_view_folder = "schooladmin.payment";
        $this->theme_color        = theme_color();
        $this->module_icon        = 'fa fa-money';
        $this->create_icon        = 'fa fa-plus-circle';
        $this->edit_icon          = 'fa fa-edit';
        $this->school_id          = Session::get('school_id');
        $this->academic_year      = Session::get('academic_year');

        $this->arr_view_data['module_icon']  =  $this->module_icon;
        $this->arr_view_data['create_icon']  =  $this->create_icon;
        $this->arr_view_data['edit_icon']    =  $this->edit_icon;
        $this->arr_view_data['view_icon']    =  'fa fa-eye';
        $this->arr_view_data['theme_color']  =  $this->theme_color;
        $this->arr_view_data['page_title']   =  $this->module_title;
        $this->arr_view_data['module_url_path']   =  $this->module_url_path;

        $this->wire_transfer_public_img_path = url('/').config('app.project.img_path.wire_transfer_receipts');
        $this->wire_transfer_base_img_path   = base_path().config('app.project.img_path.wire_transfer_receipts');

        $this->cheque_public_img_path = url('/').config('app.project.img_path.cheques');
        $this->cheque_base_img_path   = base_path().config('app.project.img_path.cheques');        

        $obj_data          = Sentinel::getUser();

        if($obj_data){
            $this->first_name        = $obj_data->first_name;
            $this->last_name         = $obj_data->last_name;
            $this->user_id           = $obj_data->id;
        }
        $obj_school_admin = $this->SchoolAdminModel->where('school_id',$this->school_id)->first();
        $this->school_admin_id = 0;
        if(isset($obj_school_admin) && !empty($obj_school_admin))
        {
            $this->school_admin_id = $obj_school_admin->user_id;
        }
        if(isset($obj_school_admin) && !empty($obj_school_admin))
        {
            $this->school_admin_id = $obj_school_admin->user_id;
        }
    }
    public function index($enc_id=FALSE){

    	$id  = base64_decode($enc_id);

        if(!is_numeric($id)){
            Flash::error(translation("something_went_wrong"));
            return redirect()->back();
        }
        \Session::forget('student_id');
        
    	$obj_student = $this->StudentService->get_student_details($id);
        if(count($obj_student)<=0)
        {
            Flash::error(translation("something_went_wrong"));
            return redirect()->back();
        }

    	$student_id     = isset($obj_student->user_id) ? $obj_student->user_id :0;
        $student_primary_key = isset($obj_student->id) ? $obj_student->id :0;
    	$level_class_id = isset($obj_student->level_class_id) ? $obj_student->level_class_id :0;
        $brother_hood   = isset($obj_student->brotherhood_id) ? $obj_student->brotherhood_id :'0';
        $discount = 0;
        if($brother_hood!='0')
        {
            $obj_brotherhood = $this->BrotherhoodModel->where('id',$brother_hood)->first();

            if(isset($obj_brotherhood) && count($obj_brotherhood)>0 ){
                $discount = $obj_brotherhood->discount;
            }
        }

    	\Session::put('student_id',$student_id);
        \Session::put('student_primary_key',$student_primary_key);

    	$arr_fees =[];
    	$level =0;
        $obj_level_class = $this->CommonDataService->get_level_class($level_class_id);
        
        $level = isset($obj_level_class['level_id']) ? $obj_level_class['level_id']  : 0 ;

    	$obj_fees = $this->FeesSchoolModel	
    									->with('get_fees')
    									->with(['fees_transaction'=>function($q) use($student_id){
                                            $q->with('get_transaction_details');    
    										$q->where('student_id',$student_id);
                                            $q->where('fees_type','MAIN');
                                            $q->where('school_id',$this->school_id);
                                            $q->where('academic_year_id',$this->academic_year);
    									}])
    									->where('school_id',$this->school_id)
    									->where('academic_year_id',$this->academic_year)
    									->where('level_id',$level)
    									->get(); 

  		if(isset($obj_fees) && !empty($obj_fees)){
  			$arr_fees = $obj_fees->toArray();
  		}
  		$arr_club_fees = [];
  		$club_fees = $this->ClubStudentsModel
  									->with('get_fees_transactions.get_transaction_details','get_club.get_supervisor')
                                    ->where('student_id',$student_id)
                                    ->whereHas('get_club',function($q){
                                        $q->where('school_id',$this->school_id);
                                        $q->where('academic_year_id',$this->academic_year);
                                    })
    								->get();

    	if(isset($club_fees) && !empty($club_fees)){
    		$arr_club_fees = $club_fees->toArray();

    	}

        $arr_bus_fees = [];
        $obj_bus = $this->BusStudentsModel
                    ->with('bus_details.get_bus_transports','bus_details.driver_details','fees_details')
                    ->whereHas('bus_details',function($q){
                        $q->where('academic_year_id',$this->academic_year);
                        $q->where('school_id',$this->school_id);
                    })
                    ->whereHas('fees_details',function($q){
                        
                    })
                    ->with('get_fees_transactions.get_transaction_details')
                    ->where('student_id',$student_id)
                    ->get();

        if(isset($obj_bus) && !empty($obj_bus)){
            $arr_bus_fees = $obj_bus->toArray();
        }            
         
        $obj_academic_year = $this->AcademicYearModel->where('id',$this->academic_year)->first();

        if(isset($obj_academic_year) &&  !empty($obj_academic_year)){

            $start_date = $obj_academic_year->start_date;
            $end_date = $obj_academic_year->end_date;
        }
                    
        $this->arr_view_data['module_title']  = translation('manage').' '.$this->module_title;
        $this->arr_view_data['arr_fees']      = $arr_fees;
        $this->arr_view_data['arr_club_fees'] = $arr_club_fees;
        $this->arr_view_data['arr_bus_fees']  = $arr_bus_fees;
        $this->arr_view_data['start_date']    = $start_date;
        $this->arr_view_data['end_date']      = $end_date;
        $this->arr_view_data['percent_discount']      = $discount;

        return view($this->module_view_folder.'.index', $this->arr_view_data);	
    }

    public function checkout(Request $request ){

        $main_fees_amount = 0;
        $club_fees_amount = 0;
        $bus_fees_amount = 0;

        if($request->has('checked_main_fees') && count($request->input('checked_main_fees'))){

            $request_fees_id = $request->input('checked_main_fees');
            $arr_fees_id =[];
            foreach($request_fees_id as $id){

                $arr_id = explode('_',$id);

                $fee_id = $arr_id[0];
                $month = $arr_id[1];
                $amount = $arr_id[2];
                $main_fees_amount += $amount;
            }
        }
        if($request->has('checked_club_fees') && count($request->input('checked_club_fees'))){
            $request_fees_id = $request->input('checked_club_fees');
            $arr_fees_id =[];
            foreach($request_fees_id as $id){

                $arr_id = explode('_',$id);
                $fee_id = $arr_id[0];
                $amount  = $arr_id[1];
                $club_fees_amount += $amount;
            }
        }    
        if($request->has('checked_bus_fees') && count($request->input('checked_bus_fees'))){
            $request_fees_id = $request->input('checked_bus_fees');
            $arr_fees_id =[];
            foreach($request_fees_id as $id){

                $arr_id = explode('_',$id);
                $fee_id = $arr_id[0];
                $amount  = $arr_id[1];
                $bus_fees_amount += $amount;
            }
        }
        $this->arr_view_data['main_fees_amount'] = $main_fees_amount;
        $this->arr_view_data['club_fees_amount'] = $club_fees_amount;
        $this->arr_view_data['bus_fees_amount']  = $bus_fees_amount;

        $main_fees  = ($request->has('checked_main_fees')) ?  $request->input('checked_main_fees') : array();
        $club_fees  = ($request->has('checked_club_fees')) ? $request->input('checked_club_fees')  : array();
        $bus_fees   = ($request->has('checked_bus_fees')) ? $request->input('checked_bus_fees') : array();

        if(count($main_fees)>0  || count($club_fees)>0 || count($bus_fees)>0){
            \Session::put('main_fees',$main_fees);
            \Session::put('club_fees',$club_fees);
            \Session::put('bus_fees',$bus_fees);

            $arr_payment_method = [];
            $arr_sorted = [];
            $obj_settings = $this->PaymentSettingModel->where('user_id',$this->school_admin_id)->first();

            if(isset($obj_settings) && !empty($obj_settings)){
                $arr_settings = $obj_settings->toArray();

			if(isset($arr_settings['enable_wire_transfer']) && $arr_settings['enable_wire_transfer']=='1'){

					array_push($arr_payment_method,'wire_transfer');
					//array_push($arr_sorted, $arr_settings['transfer_sort_order_of_display']);
					$arr_sorted[$arr_settings['transfer_sort_order_of_display']] = 'wire_transfer';
				}
				if(isset($arr_settings['enable_paypal']) && $arr_settings['enable_paypal']=='1'){

					array_push($arr_payment_method,'paypal');
					//array_push($arr_sorted, $arr_settings['paypal_sort_order_of_display']);
					$arr_sorted[$arr_settings['paypal_sort_order_of_display']] = 'paypal';
				}
				if(isset($arr_settings['enable_cheque_transfer']) && $arr_settings['enable_cheque_transfer']=='1'){

					array_push($arr_payment_method,'cheque_transfer');
					//array_push($arr_sorted, $arr_settings['cheque_sort_order_of_display']);
					$arr_sorted[$arr_settings['cheque_sort_order_of_display']] = 'cheque';
				}
				if(isset($arr_settings['enable_cash_transfer']) && $arr_settings['enable_cash_transfer']=='1'){

					array_push($arr_payment_method,'cheque_transfer');
					//array_push($arr_sorted, $arr_settings['cash_sort_order_of_display']);
					$arr_sorted[$arr_settings['cash_sort_order_of_display']] = 'cash';
				}
				if(count($arr_sorted)>0){
					ksort($arr_sorted);
				    $this->arr_view_data['arr_sorted'] = $arr_sorted;
				    $this->arr_view_data['arr_settings'] = $arr_settings;
				    return view($this->module_view_folder.'.checkout', $this->arr_view_data);  

				}
				else
				{
					Flash::error(translation('access_denied'));
					return redirect(url(config('app.project.school_admin_panel_slug')."/student"));
				}
            }
            else
            {
                Flash::error(translation('access_denied'));
                return redirect(url(config('app.project.school_admin_panel_slug')."/student"));
            }
            
        }
        Flash::error(translation('please_select_fees'));
        return redirect()->back();
    }

    public function store_payment(Request $request) {
        $total_fees_amount = 0;
        if(\Session::has('main_fees') && count(\Session::get('main_fees'))){
            $request_fees_id = Session::get('main_fees');
            foreach($request_fees_id as $id){
                $arr_id = explode('_',$id);
                $amount = $arr_id[2];
                $total_fees_amount+=$amount;
            }
        }        
        if(\Session::has('club_fees') && count(\Session::get('club_fees'))){
            $request_fees_id = Session::get('club_fees');
            $arr_fees_id =[];
            foreach($request_fees_id as $id){
                $arr_id = explode('_',$id);
                $amount = $arr_id[1];
                $total_fees_amount += $amount; 
            }
        }        
        if(\Session::has('bus_fees') && count(\Session::get('bus_fees'))){
            $request_fees_id = Session::get('bus_fees');
            $arr_fees_id =[];
            foreach($request_fees_id as $id){
                $arr_id = explode('_',$id);
                $amount = $arr_id[1];
                $total_fees_amount += $amount;
            }
        }        
        $total_fees_amount = round($total_fees_amount,2);
    	if(Session::has('student_id')){
    		if($request->has('payment_mode')) {
            
                $order_no = $this->generate_order_no();
                $transaction_id = 0;
                $status = 'SUCCESS';
                if($request->payment_mode=='cash'){

                        $payment_mode = 'CASH';
            
                        $arr_transaction_details ['amount']           = $total_fees_amount;
                        $arr_transaction_details ['order_no']         = $order_no;
                        $arr_transaction_details ['transaction_type'] = $payment_mode;
                        $arr_transaction_details ['student_id']       = Session::has('student_id') ? Session::get('student_id') :0;
                        $arr_transaction_details ['payment_done_by']  = $this->user_id;
                        $arr_transaction_details ['payment_date']     = date('Y-m-d');
                        $arr_transaction_details ['approval_status']  = 'SUCCESS';
                        $arr_transaction_details ['school_id']        = $this->school_id;
                        $arr_transaction_details ['academic_year_id'] = $this->academic_year;
                        $arr_transaction_details ['type'] = 'FEES';

                        $obj_transaction = $this->TransactionDetailsModel->create($arr_transaction_details);
                        $transaction_id = $obj_transaction->id;
                }
                elseif($request->payment_mode=='wire_transfer'){ 

                    $payment_mode = 'WIRE_TRANSFER';
                    if($request->hasFile('receipt')){
                       
                        $arr_image_size = [];
                        $arr_image_size = getimagesize($request->file('receipt'));

                        if(isset($arr_image_size) && $arr_image_size==false)
                        {
                            Flash::error(translation('not_valid_image_please_select_proper_image_format'));
                            return redirect(url(config('app.project.school_admin_panel_slug')."/student")); 
                        }

                        $minHeight = 250;
                        $minWidth  = 250;
                        $maxHeight = 2000;
                        $maxWidth  = 2000;

                        if(($arr_image_size[0] < $minWidth || $arr_image_size[0] > $maxWidth) && ($arr_image_size[1] < $minHeight || $arr_image_size[1] > $maxHeight))
                        {
                            
                            Flash::error(translation('please_upload_image_with_height_and_width_greater_than_or_equal_to_250_x_250_less_than_or_equal_to_2000_x_2000_for_best_result'));
                            return redirect(url(config('app.project.school_admin_panel_slug')."/student"));
                        }

                        $file_name = $request->file('receipt');
                        $file_extension = strtolower($request->file('receipt')->getClientOriginalExtension());
                        if(in_array($file_extension,['bmp','jpg','jpeg']))
                        {
                            $file_name = time().uniqid().'.'.$file_extension;
                            $isUpload = $request->file('receipt')->move($this->wire_transfer_base_img_path , $file_name);

                            $arr_transaction_details ['receipt_image']    = $file_name;
                            $arr_transaction_details ['amount']           = $total_fees_amount;
                            $arr_transaction_details ['order_no']         = $order_no;
                            $arr_transaction_details ['transaction_type'] = 'WIRE_TRANSFER';
                            $arr_transaction_details ['student_id']       = Session::has('student_id') ? Session::get('student_id') :0;
                            $arr_transaction_details ['payment_done_by']  = $this->user_id;
                            $arr_transaction_details ['payment_date']     = date('Y-m-d');
                            $arr_transaction_details ['approval_status']     = 'PENDING';
                            $arr_transaction_details ['school_id']        = $this->school_id;
                            $arr_transaction_details ['academic_year_id'] = $this->academic_year;
                            $arr_transaction_details ['type'] = 'FEES';

                            $obj_transaction = $this->TransactionDetailsModel->create($arr_transaction_details);
                            $transaction_id = $obj_transaction->id;
                         
                        }
                        else
                        {
                            Flash::error(translation('invalid_file_type_while_creating_payment'));
                            return redirect(url(config('app.project.school_admin_panel_slug')."/student"));
                        }
                    }
                    else{

                        Flash::error(translation('please_upload_receipt').' '.str_singular($this->module_title));
                        return redirect(url(config('app.project.school_admin_panel_slug')."/student"));
                    }
                }
                elseif($request->payment_mode=='cheque'){

                    if(!$request->has('bank_name') || !$request->has('account_holder_name') || !$request->has('cheque_number') ){
                        Flash::error(translation('all_fields_are_required'));
                        return redirect(url(config('app.project.school_admin_panel_slug')."/student"));
                    }

                    $payment_mode        = 'CHEQUE';
                    $bank_name           = $request->bank_name;
                    $account_holder_name = $request->account_holder_name;
                    $cheque_number       = $request->cheque_number;

                    $arr_transaction_details                        = [];
                    $arr_transaction_details['bank_name']           = $bank_name ;
                    $arr_transaction_details['account_holder_name'] = $account_holder_name ;
                    $arr_transaction_details['cheque_no']           = $cheque_number ;
                    $arr_transaction_details ['amount']             = $total_fees_amount;
                    $arr_transaction_details ['order_no']           = $order_no;
                    $arr_transaction_details ['transaction_type']   = $payment_mode;
                    $arr_transaction_details ['student_id']         = Session::has('student_id') ? Session::get('student_id') :0;
                    $arr_transaction_details ['payment_done_by']    = $this->user_id;
                    $arr_transaction_details ['payment_date']     = date('Y-m-d');
                    $arr_transaction_details ['approval_status']     = 'PENDING';
                    $arr_transaction_details ['school_id']        = $this->school_id;
                    $arr_transaction_details ['academic_year_id'] = $this->academic_year;
                    $arr_transaction_details ['type'] = 'FEES';

                    $obj_transaction     = $this->TransactionDetailsModel->create($arr_transaction_details);
                    $transaction_id      = $obj_transaction->id;
                }

                if(\Session::has('main_fees') && count(\Session::get('main_fees'))){
                    $request_fees_id = Session::get('main_fees');
                    $arr_fees_id =[];

                    foreach($request_fees_id as $id){

                        $arr_id = explode('_',$id);

                        $fee_id = $arr_id[0];
                        $month  = $arr_id[1];
                        $amount = $arr_id[2];

                        $arr_data                             = [];
                        $arr_data['school_fees_id']           = $fee_id ;
                        $arr_data['student_id']               = Session::has('student_id') ? Session::get('student_id') :0;
                        $arr_data['fees_type']                = 'MAIN';
                        $arr_data['months']                   = $month;
                        $arr_data['school_id']                = $this->school_id;
                        $arr_data['academic_year_id']         = $this->academic_year;
                        $arr_data['amount']                   = $amount;
                        $arr_data['payment_type']             = $payment_mode;
                        $arr_data['order_no']                 = $order_no;
                        $arr_data['transaction_id']           = $transaction_id;

                        $this->FeesTransactionModel->create($arr_data);
                    }
                }

                if(\Session::has('club_fees') && count(\Session::get('club_fees'))){
                    $request_fees_id = Session::get('club_fees');
                    $arr_fees_id =[];
                    foreach($request_fees_id as $id){

                        $arr_id = explode('_',$id);
                        $fee_id = $arr_id[0];
                        $amount  = $arr_id[1];

                        $arr_data                     = [];
                        $arr_data['school_fees_id']   = $fee_id ;
                        $arr_data['student_id']       = Session::has('student_id') ? Session::get('student_id') :0;
                        $arr_data['fees_type']        = 'CLUB';
                        $arr_data['school_id']        = $this->school_id;
                        $arr_data['academic_year_id'] = $this->academic_year;
                        $arr_data['amount']           = $amount;
                        $arr_data ['order_no']        = $order_no;
                        $arr_data['payment_type']     = $payment_mode;
                        $arr_data ['order_no']        = $order_no;
                        $arr_data ['transaction_id']  = $transaction_id;
                        
                        $this->FeesTransactionModel->create($arr_data);
                        
                    }
                }    
                if(\Session::has('bus_fees') && count(\Session::get('bus_fees'))){
                    $request_fees_id = Session::get('bus_fees');
                    $arr_fees_id =[];
                    foreach($request_fees_id as $id){
                        
                        $arr_id = explode('_',$id);
                        $fee_id = $arr_id[0];
                        $amount  = $arr_id[1];
                        $arr_data                     = [];
                        $arr_data['school_fees_id']   = $fee_id ;
                        $arr_data['student_id']       = Session::has('student_id') ? Session::get('student_id') :0;
                        $arr_data['fees_type']        = 'BUS';
                        $arr_data['school_id']        = $this->school_id;
                        $arr_data['academic_year_id'] = $this->academic_year;
                        $arr_data['amount']           = $amount;
                        $arr_data['payment_type']     = $payment_mode;
                        $arr_data ['order_no']        = $order_no;
                        $arr_data ['transaction_id']  = $transaction_id;

                        $this->FeesTransactionModel->create($arr_data);

                    }
                }
                \Session::forget('main_fees');
                \Session::forget('bus_fees');
                \Session::forget('club_fees');
                \Session::forget('student_id');
                
                Flash::success(translation('payment_done_successfully'));

                return redirect(url(config('app.project.school_admin_panel_slug')."/student"));
            }
    	}
        return redirect(url(config('app.project.school_admin_panel_slug')."/student"));
    }

    public function generate_order_no(){
        $today       = date("Ymd");
        $rand        = sprintf("%04d", rand(0,9999));
        $order_no    = 'ORD'. $today . $rand;
        $count = $this->FeesTransactionModel->where('order_no',$order_no)->count();
        if($count>0)
        {
            return $this->generate_order_no();
        }
        else
        {
            return $order_no;
        }
    }
}
