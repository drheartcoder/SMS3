<?php

namespace App\Http\Controllers\Parent;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;


use App\Common\Services\CommonDataService;
use App\Common\Services\StudentService;

use App\Models\StudentModel;
use App\Models\TransactionDetailsModel;
use App\Models\FeesTransactionModel;
use App\Models\UserTranslationModel;

use App\Models\FeesSchoolModel;
use App\Models\ClubStudentsModel;
use App\Models\BusStudentsModel;
use App\Models\AcademicYearModel;

use Flash;
use Validator;
use Sentinel;
use Session;
use DB;
use Datatables;
use PDF;

class TransactionController extends Controller
{
	public function __construct(CommonDataService $CommonDataService,
                                StudentService $StudentService){

        $this->CommonDataService = $CommonDataService;
        $this->StudentService    = $StudentService;

		$this->TransactionDetailsModel = new TransactionDetailsModel();
		$this->FeesTransactionModel = new FeesTransactionModel();

        $this->arr_view_data     = [];
        $this->module_url_path   = url(config('app.project.parent_panel_slug')).'/transactions';
        $this->module_title      = translation("transactions");
        $this->BaseModel         = $this->TransactionDetailsModel;
        $this->FeesSchoolModel   = new FeesSchoolModel();
        $this->ClubStudentsModel = new ClubStudentsModel();
        $this->BusStudentsModel  = new BusStudentsModel();
        $this->AcademicYearModel = new AcademicYearModel(); 
        $this->UserTranslationModel = new UserTranslationModel();    
        $this->StudentModel         = new StudentModel();    

        $this->module_view_folder           = "parent.transactions";
        $this->theme_color                  = theme_color();
        $this->module_icon                  = 'fa fa-exchange';
        $this->create_icon                  = 'fa fa-plus-circle';
        $this->edit_icon                    = 'fa fa-edit';
        $this->school_id                    = Session::get('school_id');  
        $this->academic_year				= Session::get('academic_year');
        $this->student_id				    = Session::get('kid_id');
        $this->kid_id                       = Session::has('kid_id')?Session::get('kid_id'):0;            
        
        $this->arr_view_data['page_title']      = translation('transactions');
   		$this->arr_view_data['module_title']    = translation('transactions');
   		$this->arr_view_data['module_icon']     = 'fa fa-exchange';
   		$this->arr_view_data['module_url_path'] = $this->module_url_path;
   		$this->arr_view_data['theme_color']     = $this->theme_color;
   		$this->arr_view_data['create_icon']     = 'fa fa-plus-circle';
   		$this->arr_view_data['edit_icon']       = 'fa fa-edit';
   		$this->arr_view_data['view_icon']       = 'fa fa-eye';

        $this->first_name = $this->last_name ='';

        /* Activity Section */
        $obj_data          = Sentinel::getUser();
        if($obj_data)
        {
        	$this->user_id    = $obj_data->id;
        	$this->first_name = $obj_data->first_name;
        	$this->last_name  = $obj_data->last_name;
        	$this->email      = $obj_data->email;
        }

        $this->wire_transfer_public_img_path = url('/').config('app.project.img_path.wire_transfer_receipts');
        $this->wire_transfer_base_img_path   = base_path().config('app.project.img_path.wire_transfer_receipts');

        $this->cheque_public_img_path = url('/').config('app.project.img_path.cheques');
        $this->cheque_base_img_path   = base_path().config('app.project.img_path.cheques');
	}
	/*
    | get_records() : redirecting to listing page 
    | Auther        : Pooja K  
    | Date          : 25 June 2018
    */
    public function index(){

    	$this->arr_view_data['page_title']      = str_plural(translation("manage")." ".$this->module_title);
    	return view($this->module_view_folder.'.index', $this->arr_view_data);
    }

    /*
    | get_records() : get listing using ajax 
    | Auther        : Pooja K  
    | Date          : 25 June 2018
    */ 
    public function get_records(Request $request)
    {
        $obj_custom = $this->get_transaction_records($request);

        $role = Sentinel::findRoleBySlug(config('app.project.role_slug.parent_role_slug'));
        $arr_current_user_access = isset($role->permissions) && ($role->permissions!='') ? $role->permissions : [] ;

        $json_result  = Datatables::of($obj_custom);

        $json_result = $json_result->blacklist(['id']);                    

  
        $json_result =  $json_result
        				->editColumn('build_date',function($data){
        					return getDateFormat($data->payment_date);
        				})
        				->editColumn('build_transaction_type',function($data){
        					return translation(strtolower($data->transaction_type));
        				})
                        ->editColumn('build_type',function($data){
                            return translation(strtolower($data->type));
                        })
                        ->editColumn('build_action_btn',function($data)
                        {
                            $build_view_action = '';
                         
                            $view_href =  $this->module_url_path.'/view/'.base64_encode($data->id);
                            $build_view_action = '<a class="green-color" href="'.$view_href.'" title="view"><i class="fa fa-eye" ></i></a>'; 

                            return $build_view_action.'&nbsp;';  
                        })
                        ->editColumn('build_approval',function($data) 
                        {
                        	$str ='';
                        	if(isset($data->approval_status)){

                        		if($data->approval_status=='APPROVED'){
                        			$str = '<span class="label green-color ">'.translation('approved').'</span>';
                        		}
                        		else if($data->approval_status=='SUCCESS'){
                        			$str = '<span class="label light-blue-color">'.translation('success').'</span>';
                        		}
                        		else if($data->approval_status=='FAILED'){
                        			$str = '<span class="label light-orange-color">'.translation('failed').'</span>';
                        		}
                        		else if($data->approval_status=='PENDING'){
		                        	$str = '<span class="label pink-color">'.translation('pending').'</span>';
		                        }
                        		else{
                        			$str = '<span class="label red-color">'.translation('rejected').'</span>';
                        		}
                        	}
                            return $str;  
                        })
                        ->make(true);

        $build_result = $json_result->getData();

        return response()->json($build_result);
    }

    /*
    | get_transaction_records() : Exam listing using ajax 
    | Auther        : Pooja K  
    | Date          : 25 June 2018
    */
    public function get_transaction_records(Request $request,$fun_type='')
    {
        $school_id     = $this->school_id;
        $locale = '';

        if(Session::has('locale'))
        {
            $locale = Session::get('locale');
        }
        else
        {
            $locale = 'en';
        }
                      
  		$transaction_details 		   = $this->TransactionDetailsModel->getTable();
        $user_translation              = $this->UserTranslationModel->getTable();
  		

        $obj_custom = DB::table($transaction_details)
                        ->select(DB::raw(   

                                           $transaction_details.".id,".
                                            $transaction_details.".payment_date,".
                                            $transaction_details.".transaction_type,".
                                            $transaction_details.".approval_status,".
                                            $transaction_details.".order_no,".
                                            $transaction_details.".user_no,".
                                            $transaction_details.".type,".
                                           "CONCAT(".$user_translation.".first_name,' ',".$user_translation.".last_name) as parent_name,".
                                            $transaction_details.".amount"

                                        ))
                        				->leftJoin($user_translation,$user_translation.'.user_id','=',$transaction_details.'.payment_done_by')
                                        ->where($transaction_details.'.school_id',$this->school_id)
                                        ->where($transaction_details.'.academic_year_id',$this->academic_year)
                                        ->where($user_translation.'.locale',Session::get('locale'))
                        				->whereRaw('('.$transaction_details.'.student_id='.$this->student_id.' OR '.$transaction_details.'.payment_done_by='.$this->user_id.')')
                        				->orderBy($transaction_details.'.id','DESC');

        if($fun_type=='export'){
            $search_term = $request->input('search');
        }else{
            $search = $request->input('search');
            $search_term = $search['value'];
        }
        if($request->has('search') && $search_term!="")
        {
            $obj_custom = $obj_custom->whereRaw("( ( CONCAT(".$user_translation.".first_name,' ',".$user_translation.".last_name) LIKE '%".$search_term."%') ")
                                     ->orWhereRaw("(payment_date LIKE '%".$search_term."%') ")
                                     ->orWhereRaw("(transaction_type LIKE '%".$search_term."%') ")
                                     ->orWhereRaw("(approval_status LIKE '%".$search_term."%') ")
                                     ->orWhereRaw("(order_no LIKE '%".$search_term."%') ")
                                     ->orWhereRaw("(user_no LIKE '%".$search_term."%') ")
                                     ->orWhereRaw("(".$transaction_details.".amount LIKE '%".$search_term."%') )");
        }

        if($fun_type=="export")
        {
            return $obj_custom->get();
        }
        else
        {

            return $obj_custom;
        }
    }

    /*
    | view() : view details of transactions 
    | Auther        : Pooja K  
    | Date          : 26 June 2018
    */
    public function view($enc_id=FALSE){

    	$id= base64_decode($enc_id);

        $obj_transaction_details = $this->TransactionDetailsModel
                                        ->where('id',$id)
                                        ->with('get_transactions.get_main_fees.get_fees','get_transactions.get_bus_fees.route_details','get_transactions.get_club_fees.get_club','get_student','get_parent')
                                        ->with('canteen_transactions')
                                        ->first();
          
        if(isset($obj_transaction_details) && count($obj_transaction_details)>0){

            $arr_transaction_details = $obj_transaction_details->toArray();
            if($arr_transaction_details['get_transactions']==null && $arr_transaction_details['canteen_transactions']==null)
            {
                Flash::error(translation('no_data_available'));
                    return redirect()->back();                  
            }            
            $this->arr_view_data['module_title'] = str_plural(translation("view")." ".$this->module_title);
            $this->arr_view_data['arr_data']   = $arr_transaction_details;    
            return view($this->module_view_folder.'.view', $this->arr_view_data);
    	}	
		Flash::error(translation('no_data_available'));
    	return redirect()->back();					
    }

    public function download_document($enc_id=FALSE)
    {
        if($enc_id)
        {
            $id = base64_decode($enc_id);
            $obj_documents = $this->TransactionDetailsModel
                                                    ->where('id',$id)
                                                    ->select('receipt_image','transaction_type')
                                                    ->first();
            if($obj_documents)
            {
				$arr_document    = $obj_documents->toArray();
				$file_name       = $arr_document['receipt_image'];
				if($arr_document['transaction_type']=='WIRE_TRANSFER'){

					$pathToFile      = $this->wire_transfer_base_img_path.$file_name;	
				}
				else{
					$pathToFile      = $this->cheque_base_img_path.$file_name;	
				}
				
				$file_exits      = file_exists($pathToFile);
				if($file_exits)
				{
					return response()->download($pathToFile, $file_name); 
				}
				else
				{
					Flash::error(translation("error_while_downloading_an_document"));
				}
                  
             }
        }
        else
        {
           Flash::error(translation("error_while_downloading_an_document"));
        }
        return redirect()->back();
    }

    public function change_status(Request $request){

    	if($request->has('id') && $request->has('status')){

    		$id = $request->id;
    		$status = $request->status;
    		$obj_transaction =  $this->TransactionDetailsModel->with('get_transactions')->where('id',$id)->first();
    		$obj_transaction->approval_status  = $status;
    		$obj_transaction->update();

    	}
    }

    /*
    | export() : Export List
    | Auther  : Padmashri
    | Date    : 17-12-2018
    */
    public function export(Request $request)
    {       
            $file_type = config('app.project.export_file_formate');


            $obj_data = $this->get_transaction_records($request,'export');
            if(sizeof($obj_data)<=0){
                Flash::error(translation("no_records_found_to_export"));
                return redirect()->back();
            }
            if(sizeof($obj_data)>500 && $request->file_format == $file_type){
                Flash::error(translation("too_many_records_to_export"));
                return redirect()->back();
            }


            $sheetTitlePDF = $sheetTitle = '';

            $student         = $this->StudentModel->where('school_id',$this->school_id)
                                                  ->with('get_user_details')
                                                  ->where('is_active',1)
                                                  ->where('has_left',0)
                                                  ->where('user_id',$this->kid_id)
                                                  ->first();
                                 
            if(!empty($student))
            {
                $sheetTitle =$this->module_title.'-'.date('d-m-Y').'-'.uniqid(). ' ( '.ucfirst($student->get_user_details->first_name)." ".ucfirst($student->get_user_details->last_name).")";  
                $sheetTitlePDF = $this->module_title.'-'.date('d-m-Y').' ( '.ucfirst($student->get_user_details->first_name)." ".ucfirst($student->get_user_details->last_name).")";  
            }
            else
            {
                $sheetTitlePDF = $sheetTitle = ucwords($this->module_title).'-'.date('d-m-Y').'-'.uniqid();    
            }
            
            if($request->file_format == $file_type){
                \Excel::create($sheetTitle, function($excel) use($obj_data,$sheetTitle,$sheetTitlePDF) 
                    {
            
                        $excel->sheet(ucwords($this->module_title), function($sheet) use($obj_data,$sheetTitle,$sheetTitlePDF) 
                        {
                            $arr_fields['id']              = translation('sr_no');;
                            $arr_fields['transaction_id']  = translation('transaction_id');
                            $arr_fields['payment_done_by'] = translation('payment_done_by');
                            $arr_fields['payment_date']    = translation('payment_date');
                            $arr_fields['payment_mode']    = translation('payment_mode');
                            $arr_fields['type']            = translation('type');
                            $arr_fields['status']          = translation('status');
                            $arr_fields['amount']          = translation('amount').' '.(config('app.project.currency'));
                                
                            
                            
                            $sheet->row(2, ['',$sheetTitlePDF,'','','']);
                            $sheet->row(4, $arr_fields);

                            // To set Colomn head
                            $j = 'A'; $k = '4';
                            $totalHead = 7;
                            for($i=0; $i<=$totalHead;$i++)
                            {
                                $sheet->cell($j.$k, function($cells) {
                                    $cells->setBackground('#495b79');
                                    $cells->setFontWeight('bold');
                                    $cells->setAlignment('center');
                                    $cells->setFontColor('#ffffff');
                                });
                                $j++;
                            }
                            $sheet->setColumnFormat([
                                'D' => "#",
                            ]);


                            
                            if(sizeof($obj_data)>0) 
                            {
                                
                                $arr_tmp = [];
                                foreach($obj_data as $key => $result)
                                {       


                                    $status ='';
                                    if(isset($result->approval_status)){

                                        if($result->approval_status=='APPROVED'){
                                            $status = translation('approved');
                                        }
                                        else if($result->approval_status=='SUCCESS'){
                                            $status = translation('success');
                                        }
                                        else if($result->approval_status=='FAILED'){
                                            $status = translation('failed');
                                        }
                                        else if($result->approval_status=='PENDING'){
                                            $status = translation('pending');
                                        }
                                        else{
                                            $status = translation('rejected');
                                        }
                                    }
                                    $arr_tmp[$key]['id']              = intval($key+1);
                                    $arr_tmp[$key]['transaction_id']  = $result->order_no;
                                    $arr_tmp[$key]['payment_done_by'] = $result->parent_name;
                                    $arr_tmp[$key]['payment_date']    = getDateFormat($result->payment_date);
                                    $arr_tmp[$key]['payment_mode']    = translation(strtolower($result->transaction_type));
                                    $arr_tmp[$key]['type']            = translation(strtolower($result->type));
                                    $arr_tmp[$key]['status']          = $status;
                                    $arr_tmp[$key]['amount']          = $result->amount;
                                    
                                    
                                }
                                $sheet->rows($arr_tmp);
                             }
                        });
                    })->export($file_type);     
            }
            
            if($request->file_format == 'pdf')
            {
                $school_name = $this->CommonDataService->get_school_name();

                $school_address = $this->CommonDataService->get_school_address();

                $school_email = $this->CommonDataService->get_school_email();

                $school_logo = $this->CommonDataService->get_school_logo();

                $this->arr_view_data['arr_data']      = $obj_data;
                $this->arr_view_data['school_name']   = $school_name;    
                $this->arr_view_data['school_address']= $school_address;
                $this->arr_view_data['school_email']  = $school_email;
                $this->arr_view_data['school_logo']   = $school_logo;
                $this->arr_view_data['sheetTitlePDF']   = $sheetTitlePDF;

                $pdf = PDF::loadView($this->module_view_folder.'.export', $this->arr_view_data);
                return $pdf->download($this->module_view_folder.'.pdf', $this->arr_view_data);
            }
    }
    
}
