<?php

namespace App\Http\Controllers\SchoolAdmin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Common\Traits\MultiActionTrait;
use App\Common\Services\CommonDataService;

use App\Models\AcademicYearModel;
use App\Models\BookCategoryModel;
use App\Models\BookCategoryTranslationModel;
use App\Models\BookDetailsModel;
use App\Models\LibraryContentModel;
use App\Models\SchoolRoleModel;
use App\Models\ParentModel;
use App\Models\ProfessorModel;
use App\Models\EmployeeModel;
use App\Models\StudentModel;
use App\Models\IssueBookModel;

use App\Common\Services\LanguageService;

use Flash;
use Validator;
use Sentinel;
use Session;
use DB;
use Datatables;

class LibraryController extends Controller
{
    use MultiActionTrait;
    public function __construct(
                                    BookCategoryModel $category,
                                    AcademicYearModel $year,
                                    LanguageService $language,
                                    BookCategoryTranslationModel $book_translation,
                                    BookDetailsModel $book,
                                    LibraryContentModel $content,
                                    SchoolRoleModel $role,
                                    ParentModel $parent,
                                    ProfessorModel $professor,
                                    StudentModel $student,
                                    EmployeeModel $employee,
                                    IssueBookModel $issueBook,
                                    CommonDataService $CommonDataService
    							)
    {
    	
		$this->arr_view_data 	            = [];
        $this->BookCategoryModel            = $category;
        $this->AcademicYearModel            = $year;
        $this->BookCategoryTranslationModel = $book_translation;
        $this->BookDetailsModel             = $book;
        $this->LibraryContentModel          = $content;
        $this->SchoolRoleModel              = $role;
        $this->StudentModel                 = $student;
        $this->ProfessorModel               = $professor;
        $this->ParentModel                  = $parent;
        $this->StudentModel                 = $student;
        $this->EmployeeModel                = $employee;
        $this->IssueBookModel               = $issueBook;
        $this->CommonDataService            = $CommonDataService;
		$this->module_url_path 	            = url(config('app.project.role_slug.school_admin_role_slug')."/library");
		$this->module_view_folder           = "schooladmin.library";
		$this->module_title                 = translation('library');
		$this->theme_color                  = theme_color();
		$this->module_icon                  = 'fa fa-university';
		$this->create_icon                  = 'fa fa-plus-circle';
        $this->edit_icon                    = 'fa fa-edit';
        $this->school_id                    = \Session::get('school_id');
        $this->BaseModel                    = $this->BookCategoryModel;
        $this->LanguageService              = $language;
        $this->academic_year                = \Session::get('academic_year');

    }

    public function index($enc_id=FALSE)
    {
        $id = 0;
        $arr_categories      =   $arr_edit_category   =   [];
        if($enc_id)
        {
            $id =   base64_decode($enc_id);
        }

        if($id!=0)
        {
            $obj_edit_category = $this->BaseModel
                                      ->where('id',$id)
                                      ->first(); 

             if($obj_edit_category)
             {
                $arr_edit_category = $obj_edit_category->toArray();

             }   
        }
       

        $obj_categories     =   $this->BaseModel
                                     ->with('get_book_contents')
                                     ->where('school_id',$this->school_id)
                                     ->get();

        if(isset($obj_categories) && $obj_categories != null)
        {
           $arr_categories  =  $obj_categories->toArray();
        }


        if(isset($arr_edit_category) && !empty($arr_edit_category))
        {
            $this->arr_view_data['edit_categories']      = $arr_edit_category;
        }

        if(isset($arr_categories) && !empty($arr_categories))
        {
            $this->arr_view_data['categories']      = $arr_categories;
        }
        $this->arr_view_data['arr_lang']        = $this->LanguageService->get_all_language();  
        $this->arr_view_data['edit_mode']       = TRUE;
        $this->arr_view_data['page_title']      = translation("manage")." ".translation('book_category');
        $this->arr_view_data['module_title']    = $this->module_title;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        $this->arr_view_data['module_icon']     = $this->module_icon;  
        $this->arr_view_data['enc_id']          = $enc_id;       
        
        return view($this->module_view_folder.'.index',$this->arr_view_data);
    } 

    public function store(Request $request)
    {

        $arr_rules  =   $messages = [];
        $arr_data   =   $this->LanguageService->get_all_language();
        

        $arr_rules['book_category']   =   'required|regex:/^[a-zA-Z \-]+$/'; 
    
        $messages = array(
                        'regex'                => translation('please_enter_valid_text_format'),
                        'required'             => translation('this_field_is_required') 

                    );

        $validator = Validator::make($request->all(),$arr_rules,$messages);

        if($validator->fails())
        { 
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        $slug           =  strslug($request->input('book_category'));
        $does_exists     =  $this->BaseModel->whereHas('translations',function($query) use($slug){
                                                        $query->where('slug','=',trim($slug))
                                                              ->where('school_id','=',$this->school_id);
                                                    })
                                                   ->count();
        
        if($does_exists)
        {
            Flash::error(translation('book_category_already_exist'));            
            return redirect()->back();
        }

        $caregory_details = [];
        $caregory_details['school_id']             =   $this->school_id;
        
        $book_category  = $this->BaseModel->create($caregory_details);

        if($book_category)
        {
            /* update record into translation table */
            
            if(sizeof($arr_data) > 0 )
            {
                foreach ($arr_data as $lang) 
                {            
                    $arr_data = array();

                    $category_name       = trim($request->input('book_category'));

                    if(isset($category_name)  && $category_name != '')
                    { 
                        $translation = $book_category->translateOrNew($lang['locale']);
                        $translation->category_name    = $category_name;
                        $translation->book_category_id = $book_category->id;
                        $translation->slug             = $slug; 
                        $translation->locale           = $lang['locale'];
                        $translation->save();
                        Flash::success(str_singular($this->module_title).' '.translation('created_successfully'));
                    }
                }
            }

            Flash::success(translation('book_category_added_successfully')); 
        }
        else
        {
            Flash::error(translation('problem_occured_while_adding_new_book_category'));
        }

        return redirect()->back();
    }

    public function update(Request $request,$enc_id=FALSE)
    {
        $arr_rules  =   $messages = [];
        $arr_data   =   $this->LanguageService->get_all_language();

        $arr_rules['book_category']   =   'required|regex:/^[a-zA-Z ]*$/'; 
    
        $messages = array(
                        'regex'                => translation('please_enter_valid_text_format'),
                        'required'             => translation('this_field_is_required') 

                    );


        $validator = Validator::make($request->all(),$arr_rules,$messages);

        if($validator->fails())
        { 
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }
        
        $does_exists     =  $this->BaseModel
                                 ->where('id','=',base64_decode($enc_id))
                                 ->where('school_id','=',$this->school_id)
                                 ->first();
        
        if($does_exists)
        {
            /* update record into translation table */
            
            if(sizeof($arr_data) > 0 )
            {
                foreach ($arr_data as $lang) 
                {            
                    $arr_data = array();

                    $category_name       = trim($request->input('book_category'));

                    if(isset($category_name)  && $category_name != '')
                    { 
                        $translation = $does_exists->translateOrNew($lang['locale']);
                        $translation->category_name    = $category_name;
                        $translation->book_category_id = $does_exists->id;
                        $translation->slug             = strslug($category_name); 
                        $translation->locale           = $lang['locale'];
                        $translation->save();
                    }
                }
            }

            Flash::success(translation('book_category_added_successfully')); 
        }
        else
        {
            Flash::error(translation('problem_occured_while_adding_new_book_category'));
        }
        return redirect()->back();

    }

    public function arrange_locale_wise(array $arr_data)
    {
        if(sizeof($arr_data)>0)
        {
            foreach ($arr_data as $key => $data) 
            {
                unset($arr_data[$key]);

                $arr_data[$data['locale']] = $data;                    
            }

            return $arr_data;
        }
        else
        {
            return [];
        }
    } 

    public function manage_books()
    {
        $this->arr_view_data['page_title']          = translation("manage")." ".translation('library_content');
        $this->arr_view_data['module_title']        = str_plural(translation('library_content'));
        $this->arr_view_data['module_icon']         = $this->module_icon;
        $this->arr_view_data['module_url_path']     = $this->module_url_path;
        $this->arr_view_data['theme_color']         = $this->theme_color;

        return view($this->module_view_folder.'.manage',$this->arr_view_data);
    }

    public function get_records(Request $request,$status=FALSE)
    {
        $arr_current_user_access =[];
        $arr_current_user_access = $this->CommonDataService->current_user_access();
        $role=Session::get('role');
        $obj_book        = $this->get_books_details($request);
        
        $json_result     = Datatables::of($obj_book);
        $json_result     = $json_result->blacklist(['id']);

        if(array_key_exists('users.update', $arr_current_user_access))
        {    
            $json_result     = $json_result->editColumn('enc_id',function($data)
                            {
                                return base64_encode($data->id);
                            });
                            
        }                    

        $json_result     = $json_result
                            ->editColumn('get_author',function($data)
                            { 
                                if($data->author!=''){

                                    return $data->author;
                                }else{
                                    return  '-';
                                }

                            })
                            
                            ->editColumn('build_type',function($data)
                            { 
                                 
                                if($data->type){

                                    return translation(strtolower($data->type));
                                }else{
                                    return  '-';
                                }

                            })
                            ->editColumn('purchase_date',function($data)
                            { 
                                 
                                if($data->purchase_date!=null && $data->purchase_date!=''){

                                    return getDateFormat($data->purchase_date);
                                }else{
                                    return  '-';
                                }

                            })
                            ->editColumn('build_action_btn',function($data) use ($role,$arr_current_user_access)
                            {
                                $build_edit_action = $build_view_action ='';
                                if($role != null)
                                {  
                                    $view_href =  $this->module_url_path.'/view/'.base64_encode($data->id);

                                    $build_view_action = '<a class="green-color" href="'.$view_href.'" title="'.translation("view").'"><i class="fa fa-eye" ></i></a>';
                                    if(array_key_exists('library.update',$arr_current_user_access))
                                    {
                                        $edit_href =  $this->module_url_path.'/edit/'.base64_encode($data->id);
                                        $build_edit_action = '<a class="orange-color"  href="'.$edit_href.'" title="'.translation("edit").'"><i class="fa fa-edit" ></i></a>';
                                    }
                                   
                                }
                                return $build_view_action.'&nbsp;'.$build_edit_action;
                            })
                            ->editColumn('availability',function($data)
                            {
                                    $status = '';
                                    if($data->available_books>0)
                                    {
                                        $status = '<label class="label label-info">'.translation('available').'</label>';
                                    }
                                    else
                                    {
                                        $status = '<label class="label label-warning">'.translation('not_available').'</label>';
                                    }
                                    return $status;
                                   
                            })
                            ->editColumn('issue_book',function($data)use($status,$arr_current_user_access)
                            {   
                                $btn='';    
                                if($status == true)
                                {
                                    if($data->available_books >0)
                                    {
                                        if(array_key_exists('library.update',$arr_current_user_access)){
                                            $btn= '<a onclick="addId('.$data->id.')" data-toggle="modal" data-target="#myModal" title="'.translation('issue').'" style="width:auto;cursor: pointer;" id="'.base64_encode($data->id).'">'.translation('issue').'</a>';    
                                        }
                                        
                                    }
                                }
                                return $btn;
                            })
                            ->make(true);

        $build_result = $json_result->getData();
        
        return response()->json($build_result);
        
    }

    function get_books_details(Request $request)
    {
        
        $library_content          = $this->LibraryContentModel->getTable();
       
        $book_details             = $this->BookDetailsModel->getTable();
       
        $obj_book = DB::table($book_details)
                                ->select(DB::raw($book_details.".id as id,".
                                                 $book_details.".ISBN_no as ISBN_no, ".
                                                 $book_details.".book_no as book_no, ".
                                                 $book_details.".no_of_books as no_of_books, ".
                                                 $book_details.".available_books as available_books, ".
                                                 $book_details.".title as title, ".
                                                 $library_content.".purchase_date as purchase_date, ".
                                                 $library_content.".type, ".
                                                 $book_details.".author as author"))
                                ->join($library_content,$book_details.'.library_content_id','=',$library_content.'.id')
                                ->where($library_content.'.school_id','=',$this->school_id)
                                ->orderBy($book_details.'.created_at','DESC');

        /* ---------------- Filtering Logic ----------------------------------*/                    

        $search = $request->input('search');
        $search_term = $search['value'];

        if($request->has('search') && $search_term!="")
        {


            $obj_book = $obj_book ->WhereRaw("((".$library_content.".type LIKE '%".$search_term."%') ")
                                 ->orWhereRaw("(".$book_details.".author LIKE '%".$search_term."%') ")
                                 ->orWhereRaw("(".$book_details.".book_no LIKE '%".$search_term."%') ")
                                 ->orWhereRaw("(".$book_details.".title LIKE '%".$search_term."%') )");
                                 
                                 
        }
        return $obj_book;
    }

    public function create()
    {
        $arr_categories     =   [];
        $obj_categories     =   $this->BookCategoryModel->where('school_id',$this->school_id)->get();

        $arr_academic_years = [];
        $year_arr = $this->CommonDataService->get_academic_year_less_than($this->academic_year);
        if($year_arr!='')
        {
            $year_arr = explode(',', $year_arr);
            $obj_academic_years = $this->AcademicYearModel->whereIn('id',$year_arr)->get();
            if(count($obj_academic_years)>0){
                $arr_academic_years = $obj_academic_years->toArray();
            }
        }
        
        if(isset($obj_categories))
        {
            $arr_categories   = $obj_categories->toArray();
        }
        if(isset($arr_categories) && !empty($arr_categories))
        {
            $this->arr_view_data['categories']      = $arr_categories;    
        }

        $page_title = translation('add').' '.translation('library_content');
        $this->arr_view_data['page_title']      = $page_title;
        $this->arr_view_data['module_title']    = str_plural(translation('library_content'));
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['create_icon']     = $this->create_icon;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        $this->arr_view_data['arr_academic_years']     = $arr_academic_years;

        return view($this->module_view_folder.'.create', $this->arr_view_data);   
    }

    public function checkCategory(Request $request)
    {
        
        $category_name  =   strslug($request->input('category'));
        $exist          =   $this->BookCategoryTranslationModel->where('slug',trim($category_name))->first();
        
        if($exist)
        {
            return response()->json(array('status'=>'error'));
        }
        else
        {
            return response()->json(array('status'=>'success'));
        }
    }

    public function store_book(Request $request)
    {
        $arr_rules  =   $messages = [];

        if($request->input('type') == 'BOOK' || $request->input('type') == 'DISSERTATION' || $request->input('type') == 'CD')
        {
                if($request->input('type') == 'BOOK'){

                    $arr_rules['book_bill_no']    =   'required|regex:/^[a-zA-Z0-9 \-]+$/';
                    $arr_rules['book_ISBN_no']    =   'required|regex:/^[\d -]+$/';
                    $arr_rules['book_title']      =   'required';
                    $arr_rules['book_author']     =   'required|regex:/^[a-zA-Z \.]+$/';
                    $arr_rules['book_edition']    =   'required|regex:/^[a-zA-Z0-9 \-]+$/';
                    $arr_rules['book_shelf_no']   =   'required|regex:/^[a-zA-Z0-9 \-]+$/';
                    $arr_rules['book_position']   =   'required|regex:/^[a-zA-Z0-9 \-]+$/';
                    $arr_rules['book_cost']       =   'required|numeric|min:1|digits_between:1,10';
                    $arr_rules['book_publisher']  =   'required|regex:/^[a-zA-Z \.]+$/';
                    $arr_rules['no_of_copies']    =   'required|numeric|digits_between:1,10';
                    $arr_rules['book_category']   =   'required|numeric';
                    $arr_rules['purchase_date']   =   'required|date_format:"Y-m-d"';
                }      
                if($request->input('type') == 'DISSERTATION'){
                    $arr_rules['diss_title']         = 'required';
                    $arr_rules['diss_author']        = 'required|regex:/^[a-zA-Z \.]+$/';
                    $arr_rules['diss_book_shelf_no'] = 'required|regex:/^[a-zA-Z0-9 \-]+$/';
                    $arr_rules['diss_position']      = 'required|regex:/^[a-zA-Z0-9 \-]+$/';
                    $arr_rules['diss_no_of_copies']  = 'required|numeric|digits_between:1,10';
                    $arr_rules['diss_category']      = 'required|numeric';
                    $arr_rules['academic_year']      = 'required|numeric';
                }    
                if($request->input('type') == 'CD'){
                    $arr_rules['cd_title']         = 'required';
                    $arr_rules['cd_shelf_no']      = 'required|regex:/^[a-zA-Z0-9 \-]+$/';
                    $arr_rules['cd_position']      = 'required|regex:/^[a-zA-Z0-9 \-]+$/';
                    $arr_rules['cd_no_of_copies']  = 'required|numeric|digits_between:1,10';
                    $arr_rules['cd_category']      = 'required|numeric';
                    $arr_rules['cd_type']          = ['required','regex:/^(audio|video)$/'];
                    $arr_rules['cd_cost']          = 'numeric|min:0';
                    $arr_rules['cd_bill_no']       = 'regex:/^[a-zA-Z0-9 \-]+$/';
                    $arr_rules['purchase_date']    = 'date_format:"Y-m-d"';

                }
                $messages = array(
                    'required'             => translation('this_field_is_required'),
                    'regex'                => translation('please_enter_valid_text_format'),
                    'numeric'              => translation('please_enter_digits_only'),
                    'date'                 => translation('please_enter_valid_date'),
                    'book_cost.min'        => translation('please_enter_a_value_greater_than_or_equal_to_1'),
                    'date_format'          => translation('please_enter_valid_date'),
                );

                $validator = Validator::make($request->all(),$arr_rules,$messages);
                
                if($validator->fails())
                { 
                    return redirect()->back()->withErrors($validator)->withInput($request->all());
                }  

                $content  =   [];
                $content['school_id']         =   $this->school_id;
                $content['type']              =   $request->input('type');
                if($request->input('type') == 'BOOK'){
                    $content['purchase_date']     =   $request->input('purchase_date');
                    $content['bill_no']           =   $request->input('book_bill_no');    
                    $content['category_id']       =   $request->input('book_category');
                }
                elseif($request->input('type') == 'DISSERTATION'){
                    $content['category_id']       =   $request->input('diss_category');
                }
                else{
                    if($request->has('purchase_date')){
                        $content['purchase_date']     =   $request->input('purchase_date');
                    }
                    $content['bill_no']           =   $request->input('cd_bill_no');    
                    $content['category_id']       =   $request->input('cd_category');
                }
                
                $library_content              =   $this->LibraryContentModel->create($content);
                
                $details  =   [];

                if($request->input('type') == 'BOOK'){
                    $details['library_content_id'] = $library_content->id;
                    $details['book_no']            = $this->generate_book_no('book');
                    $details['ISBN_no']            = $request->input('book_ISBN_no');
                    $details['title']              = $request->input('book_title');
                    $details['author']             = $request->input('book_author');
                    $details['edition']            = $request->input('book_edition');
                    $details['publisher']          = $request->input('book_publisher');
                    $details['no_of_books']        = $request->input('no_of_copies');
                    $details['available_books']    = $request->input('no_of_copies');
                    $details['shelf_no']           = $request->input('book_shelf_no');
                    $details['book_position']      = $request->input('book_position');
                    $details['cost']               = $request->input('book_cost');

                }
                if($request->input('type') == 'DISSERTATION'){
                    $details['library_content_id'] = $library_content->id;
                    $details['book_no']            = $this->generate_book_no('diss');
                    $details['title']              = $request->input('diss_title');
                    $details['author']             = $request->input('diss_author');
                    $details['no_of_books']        = $request->input('diss_no_of_copies');
                    $details['available_books']    = $request->input('diss_no_of_copies');
                    $details['shelf_no']           = $request->input('diss_book_shelf_no');
                    $details['book_position']      = $request->input('diss_position');
                    $details['academic_year_id']   = $request->input('academic_year');
                }  
                if($request->input('type') == 'CD'){
                    $details['library_content_id'] = $library_content->id;
                    $details['book_no']            = $this->generate_book_no('cd');
                    $details['title']              = $request->input('cd_title');
                    $details['cd_type']            = $request->input('type');
                    $details['no_of_books']        = $request->input('cd_no_of_copies');
                    $details['available_books']    = $request->input('cd_no_of_copies');
                    $details['shelf_no']           = $request->input('cd_shelf_no');
                    $details['book_position']      = $request->input('cd_position');
                    $details['cost']               = $request->input('cd_cost');
                }
               
                $library_details                  =  $this->BookDetailsModel->create($details);

                if($library_content && $library_details)
                {
                    Flash::success(translation('library_content_added_successfully'));
                }
                else
                {
                    Flash::error(translation('problem_occured_while_adding_library_content'));
                }
        }
        else{
            Flash::error(translation('something_went_wrong'));    
        }
        return redirect()->back();
    }

    public function edit($enc_id)
    {
        $arr_categories  =  $arr_details  = $arr_content = [];
        $obj_categories  =   $this->BookCategoryModel->where('school_id',$this->school_id)->get();
        $obj_details     =   $this->BookDetailsModel->where('id',base64_decode($enc_id))->first();
        
        if(count($obj_details)>0)
        {
            $arr_details   = $obj_details->toArray();
        }
        if(isset($arr_details) && !empty($arr_details))
        {
            $this->arr_view_data['details']      = $arr_details;    
            $obj_content     =   $this->LibraryContentModel->where('id',$arr_details['library_content_id'])->first();
            if(isset($obj_content))
            {
                $arr_content   = $obj_content->toArray();       
            }
            if(isset($arr_content) && !empty($arr_content))
            {
                $this->arr_view_data['content']      = $arr_content;           
            }
        }

        if(count($obj_categories)>0)
        {
            $arr_categories   = $obj_categories->toArray();
        }
        if(isset($arr_categories) && !empty($arr_categories))
        {
            $this->arr_view_data['categories']      = $arr_categories;    
        }

        $arr_academic_years = [];
        $year_arr = $this->CommonDataService->get_academic_year_less_than($this->academic_year);
        if($year_arr!='')
        {
            $year_arr = explode(',', $year_arr);
            $obj_academic_years = $this->AcademicYearModel->whereIn('id',$year_arr)->get();
            if(count($obj_academic_years)>0){
                $arr_academic_years = $obj_academic_years->toArray();
            }
        }

        $page_title                                = translation("edit")." ".translation("library_content");
        $this->arr_view_data['enc_id']             = $enc_id;
        $this->arr_view_data['page_title']         = $page_title;
        $this->arr_view_data['module_title']       = str_plural(translation('library_content'));
        $this->arr_view_data['module_icon']        = $this->module_icon;
        $this->arr_view_data['create_icon']        = $this->edit_icon;
        $this->arr_view_data['module_url_path']    = $this->module_url_path;
        $this->arr_view_data['theme_color']        = $this->theme_color;
        $this->arr_view_data['arr_academic_years'] = $arr_academic_years;
        return view($this->module_view_folder.'.edit', $this->arr_view_data); 
    }

    public function update_content(Request $request,$enc_id)
    {

        $details = [];
        if($request->input('type') == 'BOOK' || $request->input('type') == 'DISSERTATION' || $request->input('type') == 'CD')
        {
            if($request->input('type') == 'BOOK')
            {
                  $arr_rules['book_bill_no']    =   'required|regex:/^[a-zA-Z0-9 \-]+$/';
                  $arr_rules['book_ISBN_no']    =   'required|regex:/^[\d -]+$/';
                  $arr_rules['book_title']      =   'required';
                  $arr_rules['book_author']     =   'required|regex:/^[a-zA-Z \.]+$/';
                  $arr_rules['book_edition']    =   'required|regex:/^[a-zA-Z0-9 \-]+$/';
                  $arr_rules['book_shelf_no']   =   'required|regex:/^[a-zA-Z0-9 \-]+$/';
                  $arr_rules['book_position']   =   'required|regex:/^[a-zA-Z0-9 \-]+$/';
                  $arr_rules['book_cost']       =   'required|numeric|min:1';
                  $arr_rules['book_publisher']  =   'required|regex:/^[a-zA-Z \.]+$/';
                  $arr_rules['no_of_copies']    =   'required|numeric';
                  $arr_rules['book_category']   =   'required|numeric';
                  $arr_rules['purchase_date']   =   'required|date_format:Y-m-d';

                  $messages = array(
                                'required'             => translation('this_field_is_required'),
                                'regex'                => translation('please_enter_valid_text_format'),
                                'numeric'              => translation('please_enter_digits_only'),
                                'date'                 => translation('please_enter_valid_date'),
                                'book_cost.min'        => translation('please_enter_a_value_greater_than_or_equal_to_1'),
                                'date_format'          =>  translation('please_enter_valid_date')
                                );
                  $validator = Validator::make($request->all(),$arr_rules,$messages);
                
                  if($validator->fails())
                  { 
                    return redirect()->back()->withErrors($validator)->withInput($request->all());
                  }
                  $library_details              =   $this->BookDetailsModel->where('id',base64_decode($enc_id))->first();
                  $library_content='';
                  
                  if($library_details)
                  {    
                      $diff = $library_details->no_of_books - $library_details->available_books;
                      if($request->input('no_of_copies')<$diff){
                            Flash::error(translation('provide_valid_no_of_books'));
                            return redirect()->back();
                      }

                      $content  =   [];
                      $content['category_id']       =   $request->input('book_category');
                      $content['type']              =   $request->input('type');
                      $content['purchase_date']     =   $request->input('purchase_date');
                      $content['bill_no']           =   $request->input('book_bill_no');

                      $library_content              =   $this->LibraryContentModel->where('id',$library_details->library_content_id)->update($content);

                      if($request->input('no_of_copies') < $library_details->no_of_books)
                      {
                        $count =    $library_details->no_of_books - $request->input('no_of_copies');
                        if($count<0)
                        {
                            Flash::error(translation('provide_valid_no_of_books'));
                            return redirect()->back();
                        }
                        $details['no_of_books']        =  $library_details->no_of_books - $count;
                        $details['available_books']    =  $library_details->available_books - $count;
                      }

                      elseif($request->input('no_of_copies') > $library_details->no_of_books)
                      {
                        $count =    $request->input('no_of_copies') - $library_details->no_of_books;
                        if($count<0)
                        {
                            Flash::error(translation('provide_valid_no_of_books'));
                            return redirect()->back();
                        }
                        $details['no_of_books']        =  $library_details->no_of_books + $count;
                        $details['available_books']    =  $library_details->available_books + $count; 
                      }

                      else
                      {
                        $details['no_of_books']        =  $request->input('no_of_copies');
                      
                      }
                      $details['ISBN_no']            =  trim($request->input('book_ISBN_no'));
                      $details['title']              =  trim($request->input('book_title'));
                      $details['author']             =  trim($request->input('book_author'));
                      $details['edition']            =  trim($request->input('book_edition'));
                      $details['publisher']          =  trim($request->input('book_publisher'));
                      $details['shelf_no']           =  trim($request->input('book_shelf_no'));
                      $details['book_position']      =  trim($request->input('book_position'));
                      $details['cost']               =  trim($request->input('book_cost'));

                      $library                       =  $this->BookDetailsModel->where('id',$library_details->id)->update($details);
                  } 
                  if($library_content && $library)
                  {
                    Flash::success(translation('library_content_updated_successfully'));
                  }
                  else
                  {
                    Flash::error(translation('problem_occured_while_updating_library_content'));
                  }
            }
            if($request->input('type') == 'DISSERTATION')
            {
                $arr_rules['diss_title']         = 'required';
                $arr_rules['diss_author']        = 'required|regex:/^[a-zA-Z \.]+$/';
                $arr_rules['diss_book_shelf_no'] = 'required|regex:/^[a-zA-Z0-9 \-]+$/';
                $arr_rules['diss_position']      = 'required|regex:/^[a-zA-Z0-9 \-]+$/';
                $arr_rules['diss_no_of_copies']  = 'required|numeric|digits_between:1,10';
                $arr_rules['diss_category']      = 'required|numeric';
                $arr_rules['academic_year']      = 'required|numeric';

                  $messages = array(
                                'required'             => translation('this_field_is_required'),
                                'regex'                => translation('please_enter_valid_text_format'),
                                'numeric'              => translation('please_enter_digits_only'),
                                'date'                 => translation('please_enter_valid_date'),
                                'book_cost.min'        => translation('please_enter_a_value_greater_than_or_equal_to_1'),
                                
                                );
                  $validator = Validator::make($request->all(),$arr_rules,$messages);

                  if($validator->fails())
                  { 
                    return redirect()->back()->withErrors($validator)->withInput($request->all());
                  }
                  $library_details              =   $this->BookDetailsModel->where('id',base64_decode($enc_id))->first();
                  $library_content='';

                  if($library_details)
                  {
                      $diff = $library_details->no_of_books - $library_details->available_books;
                      if($request->input('no_of_copies')<$diff){
                            Flash::error(translation('provide_valid_no_of_books'));
                            return redirect()->back();
                      }

                      $content  =   [];
                      $content['category_id']       =   $request->input('diss_category');

                      $library_content              =   $this->LibraryContentModel->where('id',$library_details->library_content_id)->update($content);

                      if($request->input('diss_no_of_copies') < $library_details->no_of_books)
                      {
                        $count =    $library_details->no_of_books - $request->input('diss_no_of_copies');
                        if($count<0)
                        {
                            Flash::error(translation('provide_valid_no_of_books'));
                            return redirect()->back();
                        }
                        $details['no_of_books']        =  $library_details->no_of_books - $count;
                        $details['available_books']    =  $library_details->available_books - $count;
                      }

                      elseif($request->input('diss_no_of_copies') > $library_details->no_of_books)
                      {
                        $count =    $request->input('diss_no_of_copies') - $library_details->no_of_books;
                        
                        if($count<0)
                        {
                            Flash::error(translation('provide_valid_no_of_books'));
                            return redirect()->back();
                        }
                        $details['no_of_books']        =  $library_details->no_of_books + $count;
                        $details['available_books']    =  $library_details->available_books + $count; 
                      }

                      else
                      {
                        $details['no_of_books']        =  $request->input('diss_no_of_copies');
                      
                      }
                      $details['title']              =  trim($request->input('diss_title'));
                      $details['author']             =  trim($request->input('diss_author'));
                      $details['shelf_no']           =  trim($request->input('diss_book_shelf_no'));
                      $details['book_position']      =  trim($request->input('diss_position'));
                      $details['academic_year_id']   =  trim($request->input('academic_year'));
                      $library                       =  $this->BookDetailsModel->where('id',$library_details->id)->update($details);
                  } 
                  if($library_content && $library)
                  {
                    Flash::success(translation('library_content_updated_successfully'));
                  }
                  else
                  {
                    Flash::error(translation('problem_occured_while_updating_library_content'));
                  }
            }
            if($request->input('type') == 'CD')
            {
                $arr_rules['cd_title']         = 'required';
                $arr_rules['cd_shelf_no']      = 'required|regex:/^[a-zA-Z0-9 \-]+$/';
                $arr_rules['cd_position']      = 'required|regex:/^[a-zA-Z0-9 \-]+$/';
                $arr_rules['cd_no_of_copies']  = 'required|numeric|digits_between:1,10';
                $arr_rules['cd_category']      = 'required|numeric';
                $arr_rules['cd_type']          = ['required','regex:/^(audio|video)$/'];
                $arr_rules['cd_bill_no']    =   'regex:/^[a-zA-Z0-9 \-]+$/';
                $arr_rules['cd_cost']       =   'numeric|min:1';
                $arr_rules['purchase_date']   =   'date_format:Y-m-d';

                  $messages = array(
                                'required'             => translation('this_field_is_required'),
                                'regex'                => translation('please_enter_valid_text_format'),
                                'numeric'              => translation('please_enter_digits_only'),
                                'date'                 => translation('please_enter_valid_date'),
                                'book_cost.min'        => translation('please_enter_a_value_greater_than_or_equal_to_1'),
                                
                                );
                  $validator = Validator::make($request->all(),$arr_rules,$messages);
                  if($validator->fails())
                  { 
                    return redirect()->back()->withErrors($validator)->withInput($request->all());
                  }
                  $library_details              =   $this->BookDetailsModel->where('id',base64_decode($enc_id))->first();
                  $library_content='';
                  
                  if($library_details)
                  {
                      $diff = $library_details->no_of_books - $library_details->available_books;
                      if($request->input('cd_no_of_copies')<$diff){
                            Flash::error(translation('provide_valid_no_of_books'));
                            return redirect()->back();
                      }

                      $content  =   [];
                      $content['category_id']       =   $request->input('cd_category');
                      $content['purchase_date']     =   $request->input('purchase_date');
                      $content['bill_no']           =   $request->input('cd_bill_no');

                      $library_content              =   $this->LibraryContentModel->where('id',$library_details->library_content_id)->update($content);

                      if($request->input('cd_no_of_copies') < $library_details->no_of_books)
                      {
                        $count =    $library_details->no_of_books - $request->input('cd_no_of_copies');
                        if($count<0)
                        {
                            Flash::error(translation('provide_valid_no_of_books'));
                            return redirect()->back();
                        }
                        $details['no_of_books']        =  $library_details->no_of_books - $count;
                        $details['available_books']    =  $library_details->available_books - $count;
                      }

                      elseif($request->input('cd_no_of_copies') > $library_details->no_of_books)
                      {
                        $count =    $request->input('cd_no_of_copies') - $library_details->no_of_books;
                        if($count<0)
                        {
                            Flash::error(translation('provide_valid_no_of_books'));
                            return redirect()->back();
                        }
                        $details['no_of_books']        =  $library_details->no_of_books + $count;
                        $details['available_books']    =  $library_details->available_books + $count; 
                      }

                      else
                      {
                        $details['no_of_books']        =  $request->input('cd_no_of_copies');
                      
                      }
                      $details['title']              =  trim($request->input('cd_title'));
                      $details['shelf_no']           =  trim($request->input('cd_shelf_no'));
                      $details['book_position']      =  trim($request->input('cd_position'));
                      $details['cd_type']            =  trim($request->input('cd_type'));
                      $details['cost']               =  trim($request->input('cd_cost'));

                      $library                       =  $this->BookDetailsModel->where('id',$library_details->id)->update($details);
                  } 
                  if($library_content && $library)
                  {
                    Flash::success(translation('library_content_updated_successfully'));
                  }
                  else
                  {
                    Flash::error(translation('problem_occured_while_updating_library_content'));
                  }
            }

        }
        return redirect()->back();
    }

    public function view($enc_id)
    {   
        $id       = base64_decode($enc_id);
        
        $book_details =   $this->BookDetailsModel
                               ->with('library_content','academic_year') 
                               ->where('id',$id)
                               ->first();
                               
        $arr_book_details = [];
        if($book_details)
        {
            $arr_book_details = $book_details->toArray();
        }
       
        $arr_data = $arr_category = []; 
        $arr_data    = $arr_book_details;

        $category_name =   $this->BookCategoryModel
                                ->with('translations')
                               ->where('id',$arr_data['library_content']['category_id'])
                               ->first();

         if($category_name)
        {
            $arr_category = $category_name->toArray();
        }
        $this->arr_view_data['category_name']                = isset($arr_category['category_name'])?$arr_category['category_name']:'';
        $this->arr_view_data['page_title']                   = translation("view").' '.translation('library_content');
        $this->arr_view_data['module_title']                 = str_plural(translation('library_content'));
        $this->arr_view_data['module_url_path']              = $this->module_url_path.'/manage_library_contents';
        $this->arr_view_data['arr_data']                     = $arr_data;
        $this->arr_view_data['module_icon']                  = $this->module_icon;
        $this->arr_view_data['theme_color']                  = $this->theme_color;
        
        return view($this->module_view_folder.'.view', $this->arr_view_data);
    }

    public function issue()
    {
        $obj_data = $this->SchoolRoleModel
                         ->with('role_details')
                         ->whereHas('role_details',function($query){
                            $query->where('is_approved','APPROVED');
                         })->get();

        $arr_data = [];
        $year = $this->AcademicYearModel->where('id',$this->academic_year)->where('school_id',$this->school_id)->first();
        if($year)
        {
            $this->arr_view_data['year']        = $year;       
        }
        if(isset($obj_data))
        {
            $arr_data   =   $obj_data->toArray();
        }
        if(isset($arr_data) && !empty($arr_data))
        {
            $this->arr_view_data['arr_data']        = $arr_data;    
        }
        $this->arr_view_data['page_title']          = translation("books");
        $this->arr_view_data['module_title']        = str_plural(translation('library_content'));
        $this->arr_view_data['module_icon']         = $this->module_icon;
        $this->arr_view_data['module_url_path']     = $this->module_url_path;
        $this->arr_view_data['theme_color']         = $this->theme_color;

        return view($this->module_view_folder.'.issue_book',$this->arr_view_data);
    }

    public function issue_book(Request $request)
    {
       $exist   = '';
       $str_user          =   $request->input('user');
       $str_issue_date    =   $request->input('issue_date');
       $str_due_date      =   $request->input('due_date');
       $str_user_id       =   $request->input('user_id');
       $str_user_name     =   $request->input('user_name');
       $book_id           =   $request->input('book_id');

       if($str_user_id =='' && $str_user_name=='')
       {
            Flash::error(translation('provide_atleast_user_name_or_user_no'));
            return redirect()->back();
       }


       if($str_user == 'employee')
       { 
            if($str_user_name != '')
            {
                $exist = $this->EmployeeModel->where('user_id',$str_user_name)->first();    
                $str_user_id = $exist->employee_no;
            }
            elseif ($str_user_id != '') {
                $exist = $this->EmployeeModel->where('employee_no',$str_user_id)->first();
            }
            
       }
       if($str_user == 'professor')
       {
            if($str_user_name != '')
            {
                $exist = $this->ProfessorModel->where('user_id',$str_user_name)->first(); 
                $str_user_id = $exist->professor_no;   
            }
            elseif ($str_user_id != '') 
            {
                $exist = $this->ProfessorModel->where('professor_no',$str_user_id)->first();
            }
       }
       if($str_user == 'parent')
       {
            if($str_user_name != '')
            {
                $exist = $this->ParentModel->where('user_id',$str_user_name)->first();    
                $str_user_id = $exist->parent_no;
            }
            elseif ($str_user_id != '') 
            {
                $exist = $this->ParentModel->where('parent_no',$str_user_id)->first();
            }

       }
       if($str_user == 'student')
       {
            if($str_user_name != '')
            {
                $exist = $this->StudentModel->where('user_id',$str_user_name)->first(); 
                $str_user_id = $exist->student_no;   
            }
            elseif ($str_user_id != '') 
            {
                $exist = $this->StudentModel->where('student_no',$str_user_id)->first();
            }
       }
      
       if(!$exist)
       {
         Flash::error(translation('provide_valid_user_details'));
         return redirect()->back();
       }

       if(!empty($str_issue_date))
       {
            $issue_book['issue_date']   =   $str_issue_date;
       }
       if(!empty($str_due_date))
       {
            $issue_book['due_date']     =  $str_due_date;    
       }

       $issue_book['user_type']        =   $str_user;
       $issue_book['library_book_id']  =   $book_id;
       $issue_book['status']           =   'ISSUE';
       $issue_book['user_id']          =   $exist->user_id;
       $issue_book['user_no']          =   $str_user_id;
       $issue_book['academic_year_id'] =   $this->academic_year;

       $issue  =   $this->IssueBookModel->create($issue_book);
       
       if($issue)
       {
            $book_details =  $this->BookDetailsModel->where('id',$book_id)->first();
            if($book_details)
            {
                $update['available_books'] = $book_details->available_books-1;
                $this->BookDetailsModel->where('id',$book_id)->update($update);
            }
            Flash::success(translation('book_issued_successfully'));            
       }
       else
       {
            Flash::error(translation('problem_occured while_issuing_book'));                
       }
       return redirect()->back();
    }

    public function manage_return_books()
    {
        $year = $this->AcademicYearModel->where('id',$this->academic_year)->where('school_id',$this->school_id)->first();
        if($year)
        {
            $this->arr_view_data['year']        = $year;       
        }
        
        $this->arr_view_data['page_title']          = translation("reissuereturn_books");
        $this->arr_view_data['module_title']        = str_plural(translation('library_content'));
        $this->arr_view_data['module_icon']         = $this->module_icon;
        $this->arr_view_data['module_url_path']     = $this->module_url_path;
        $this->arr_view_data['theme_color']         = $this->theme_color;

        return view($this->module_view_folder.'.return_list',$this->arr_view_data);
    }

    public function get_return_books(Request $request)
    {
        $arr_current_user_access=[];
    
        $arr_current_user_access = $this->CommonDataService->current_user_access();

        $role= Session::get('role');
        $obj_book        = $this->get_return_books_details($request);
        
        $json_result     = Datatables::of($obj_book);
        $json_result     = $json_result->blacklist(['id']);

        if(array_key_exists('library.update', $arr_current_user_access))
        {    
            $json_result     = $json_result->editColumn('enc_id',function($data)
                            {
                                return base64_encode($data->id);
                            });
                            
        }                    

        $json_result     = $json_result
                            ->editColumn('link_book_no',function($data)
                            { 
                                $obj = $this->BookDetailsModel->where('book_no',$data->book_no)->first();
                                if(isset($obj->id)){
                                    $view_href =  $this->module_url_path.'/view/'.base64_encode($obj->id);
                                    $build_view_action = '<a href="'.$view_href.'" target="_blank" title="'.translation('view').'">'.$data->book_no.'</a>';
                                    return $build_view_action;
                                     
                                }else{
                                    return  '';
                                }

                            })
                            ->editColumn('user_no',function($data){
                                $build_view_action='';
                                if($data->user_type=='employee'){
                                    $obj = $this->EmployeeModel->where('employee_no',$data->user_no)->first();
                                    $view_href =  url('school_admin/employee/view').'/'.base64_encode($obj->id);
                                    $build_view_action = '<a href="'.$view_href.'" target="_blank" title="'.translation('view').'">'.$data->user_no.'</a>';
                                }
                                if($data->user_type=='student'){
                                    $obj = $this->StudentModel->where('student_no',$data->user_no)->first();
                                    $view_href =  url('school_admin/student/view').'/'.base64_encode($obj->id);
                                    $build_view_action = '<a href="'.$view_href.'" target="_blank" title="'.translation('view').'">'.$data->user_no.'</a>';
                                    
                                }
                                if($data->user_type=='professor'){
                                    $obj = $this->ProfessorModel->where('professor_no',$data->user_no)->first();
                                    $view_href =  url('school_admin/professor/view').'/'.base64_encode($obj->id);
                                    $build_view_action = '<a href="'.$view_href.'" target="_blank" title="'.translation('view').'">'.$data->user_no.'</a>';
                                    
                                }
                                if($data->user_type=='parent'){
                                    $obj = $this->ParentModel->where('parent_no',$data->user_no)->first();
                                    $view_href =  url('school_admin/parent/view').'/'.base64_encode($obj->id);
                                    $build_view_action = '<a href="'.$view_href.'" target="_blank" title="'.translation('view').'">'.$data->user_no.'</a>';
                                    
                                }
                                return $build_view_action;
                            })
                            ->editColumn('user_type',function($data)
                            { 
                                return translation($data->user_type);

                            })    
                            ->editColumn('issue_date',function($data)
                            { 
                                 
                                if($data->issue_date!=null && $data->issue_date!=''){

                                    return getDateFormat($data->issue_date);
                                }else{
                                    return  '-';
                                }

                            })
                            ->editColumn('due_date',function($data)
                            { 
                                 
                                if($data->due_date!=null && $data->due_date!=''){

                                    return getDateFormat($data->due_date);
                                }else{
                                    return  '-';
                                }

                            })
                            ->editColumn('status',function($data)
                            { 
                                 
                                if($data->status!=null && $data->status!=''){
                                    if($data->status == 'ISSUE' || $data->status == 'REISSUE')
                                    {
                                        return '<label class="label label-info">'.translation('issue').'</label>';
                                    }
                                    elseif($data->status == 'RETURNED')
                                    {
                                        return '<label class="label label-info label-lime">'.translation('returned').'</label>';
                                    }

                                }

                            })
                            ->editColumn('build_action_btn',function($data)use($arr_current_user_access)
                            { 
                                $return_btn = '';
                                $reissue_btn='';
                                if(array_key_exists('library.update',$arr_current_user_access)){
                                    if($data->status!=null && $data->status!=''){
                                        if($data->status == 'ISSUE' || $data->status == 'REISSUE')
                                        {
                                            $return_link      = $this->module_url_path.'/return/'.base64_encode($data->id);
                                            $return_btn = '<a class="orange-color" href="'.$return_link.'" title="'.translation('return').'"><i class="fa fa-reply" ></i></a>';
                                            $today = date('Y-m-d'); 
                                            $due_date = $data->due_date;
                                            if($due_date!='0000-00-00'){
                                                $datetime1 = date_create('2018-08-15');
                                                $datetime2 = date_create($due_date);
                                                $interval = date_diff($datetime1, $datetime2);
                                                $days = $interval->format('%R%a');
                                                if($days>=0){
                                                    
                                                    $reissue_btn      = '<a onclick="addId('.$data->id.')" data-toggle="modal" data-target="#myModal" title="'.translation('reissue').'" style="width:auto;cursor:pointer;" data-due_date="'.$data->due_date.'">'.translation('reissue').'</a>';    
                                                }
                                            }
                                        }
                                    }
                                }    
                                if($return_btn=='' && $reissue_btn==''){
                                    return '-';    
                                }
                                return $return_btn.'&nbsp;'.$reissue_btn;
                            })
                            ->make(true);

        $build_result = $json_result->getData();
        
        return response()->json($build_result);
        
    }

    function get_return_books_details(Request $request)
    {
       
        $book_details             = $this->BookDetailsModel->getTable();
        $issue_book               = $this->IssueBookModel->getTable();
        $library_content          = $this->LibraryContentModel->getTable();
       
        $obj_book = DB::table($issue_book)
                                ->select(DB::raw($issue_book.".id as id,".
                                                 $book_details.".ISBN_no as ISBN_no, ".
                                                 $book_details.".book_no as book_no, ".
                                                 $issue_book.".user_type as user_type, ".
                                                 $issue_book.".user_id as user_id, ".
                                                 $issue_book.".user_no as user_no, ".
                                                 $book_details.".title as title, ".
                                                 $issue_book.".status as status, ".
                                                 $issue_book.".issue_date as issue_date, ".
                                                 $issue_book.".due_date as due_date"))
                                ->join($book_details,$issue_book.'.library_book_id','=',$book_details.'.id')
                                ->join($library_content,$book_details.'.library_content_id','=',$library_content.'.id')
                                ->where($library_content.'.school_id','=',$this->school_id)
                                ->where($issue_book.'.academic_year_id','=',$this->academic_year)
                                ->orderBy($issue_book.'.created_at','DESC');

        /* ---------------- Filtering Logic ----------------------------------*/                    

        $search = $request->input('search');
        $search_term = $search['value'];

        if($request->has('search') && $search_term!="")
        {

            $obj_book = $obj_book
                                 ->orWhereRaw("(".$book_details.".book_no LIKE '%".$search_term."%') ")
                                 ->orWhereRaw("(".$book_details.".title LIKE '%".$search_term."%') ")
                                 ->orWhereRaw("(".$issue_book.".user_type LIKE '%".$search_term."%') ")
                                 ->orWhereRaw("(".$issue_book.".user_id LIKE '%".$search_term."%') ")
                                 ->orWhereRaw("(".$issue_book.".status LIKE '%".$search_term."%') ")
                                 ->orWhereRaw("(".$issue_book.".issue_date LIKE '%".$search_term."%') ")
                                 ->orWhereRaw("(".$issue_book.".due_date LIKE '%".$search_term."%') )");         
                                 
        }
        return $obj_book;
    }

    function return_book($enc_id)
    {
        $book   = $this->IssueBookModel->where('id',base64_decode($enc_id))->first(['library_book_id']);
        $return_date    = getdate();
        $date = $return_date['year'].'-'.$return_date['mon'].'-'.$return_date['mday'];
        $data['return_date']    =  $date;
        $data['status']         = 'RETURNED';

        $return         = $this->IssueBookModel->where('id',base64_decode($enc_id))->update($data);
        if($return)
        {
            $book_details = $this->BookDetailsModel->where('id',$book->library_book_id)->first();
            if(isset($book_details) && !empty($book_details))
            {
                $update_data['available_books'] = $book_details->available_books+1;
                $this->BookDetailsModel->where('id',$book->library_book_id)->update($update_data);

                Flash::success(translation('book_returned_successfully'));         
            }
            else
            {
                Flash::error(translation('problem_occured_while_returning_book'));
            }
        }
        else
        {
            Flash::error(translation('problem_occured_while_returning_book'));
        }

        return redirect()->back();

    }

    function getData(Request $request)
    {
        $details = $this->IssueBookModel->with('user_details')->where('id',$request->input('id'))->first();

        if($details)
        {
            return response()->json(array('status'=>'success','details'=>$details));
        }
        else
        {
            return response()->json(array('status'=>'error'));
        }
    }

    function reissue_book(Request $request)
    {
       $str_due_date = $request->input('due_date');
       $id           = $request->input('book_id');
       
       $count  =$this->IssueBookModel->where('id',$id)->first(['no_of_reissued']);
       $issue_book['due_date']      =   $str_due_date;  
       $issue_book['no_of_reissued']=   $count->no_of_reissued +1;    
       $issue_book['status']        =   'REISSUE';    
       $reissue = $this->IssueBookModel->where('id',$id)->update($issue_book);

       if($reissue)
       {
            Flash::success(translation('book_renewed_successfully')); 
       }
       else
       {
            Flash::error(translation('problem_occured_while_renewing_book'));
       }
        return redirect()->back();

    }

    function return_view($enc_id)
    {
        $id       = base64_decode($enc_id);
        
        $book_details =   $this->IssueBookModel 
                               ->with('book_details')
                               ->where('id',$id)
                               ->first();
        
        $arr_book_details = $arr_data = [];
        if($book_details)
        {
            $arr_book_details = $book_details->toArray();
        }
       
        $arr_data    = $arr_book_details;

        $this->arr_view_data['page_title']                   = translation("view").' '.str_singular(translation('reissuereturn_books'));
        $this->arr_view_data['module_title']                 = $this->module_title;
        $this->arr_view_data['module_url_path']              = $this->module_url_path.'/manage_library_contents';
        $this->arr_view_data['module_url_path1']             = $this->module_url_path.'/return_book';
        $this->arr_view_data['arr_data']                     = $arr_data;
        $this->arr_view_data['module_icon']                  = $this->module_icon;
        $this->arr_view_data['theme_color']                  = $this->theme_color;
        
        return view($this->module_view_folder.'.issue_book_view', $this->arr_view_data);    
    }

    /*
    | get_users() : get users list on role
    | Auther  : sayali B
    | Date    : 26-06-2018
    */
    public function get_users(Request $request)
    {
        $options = '';
        $users_data  = [];
        $role = $request->input('role');
        if($role == config('app.project.role_slug.employee_role_slug'))
        {
            $users_data = $this->CommonDataService->get_employees();
            if(isset($users_data) && count($users_data)>0)
            {
                $options .= "<option value= '' >".translation('select_user')."</option>";
                foreach($users_data as $data)
                {       
                        $options .= "<option value='".$data->user_id."'>".ucwords($data->user_name)." (".$data->national_id.")</option>"; 
                    
                }
            }
        }

        elseif($role == config('app.project.role_slug.professor_role_slug'))
        {
            $users_data = $this->CommonDataService->get_professor_by_year($this->school_id,$this->academic_year);  
            if(isset($users_data) && count($users_data)>0)
            {
                $options .= "<option value= '' >".translation('select_user')."</option>";
                foreach($users_data as $data)
                {
                        $options .= "<option value='".$data->user_id."'>".ucwords($data->user_name)." (".$data->national_id.")</option>"; 
                    
                }
            } 
        } 

        elseif($role == config('app.project.role_slug.student_role_slug'))
        {
            $arr_user = [];
            $users_data = $this->CommonDataService->get_students();  
                if(isset($users_data) && $users_data!=null)
                {
                    $arr_user = $users_data->toArray();
                }
            if(isset($arr_user) && count($arr_user)>0)
            {
                $options .= "<option value= '' >".translation('select_user')."</option>";
                foreach($arr_user as $data)
                {
                        $options .= "<option value='".$data['user_id']."'>".ucwords($data['get_user_details']['first_name']).' '.ucwords($data['get_user_details']['last_name'])." (".$data['get_user_details']['national_id'].")</option>"; 
                    
                }
            } 
        }  

        elseif($role == config('app.project.role_slug.parent_role_slug'))
        {
            $arr_user = [];
            $users_data = $this->CommonDataService->get_parent();  
             if(isset($users_data) && $users_data!=null)
                {
                    $arr_user = $users_data->toArray();
                }

            if(isset($arr_user) && count($arr_user)>0)
            {
                $options .= "<option value= '' >".translation('select_user')."</option>";
                foreach($arr_user as $data)
                {
                        $options .= "<option value='".$data['user_id']."'>".ucwords($data['get_parent_details']['first_name'])." ".ucwords($data['get_parent_details']['last_name'])."(".$data['get_parent_details']['national_id'].")</option>"; 
                    
                }
            } 
        }         
        return $options;
    }

    public function get_user_no(Request $request)
    {
        $role   =   $request->input('user_type');
        $id     =   $request->input('user_id');
        $data   =   '';

        if($role == config('app.project.role_slug.employee_role_slug'))
        {
            $users_data = $this->EmployeeModel->where('user_id',$id)->first();
            if(isset($users_data) && count($users_data)>0)
            {
               $data = $users_data->employee_no;
            }
        }

        elseif($role == config('app.project.role_slug.professor_role_slug'))
        {
            $users_data = $this->ProfessorModel->where('user_id',$id)->first();
            if(isset($users_data) && count($users_data)>0)
            {
               $data = $users_data->professor_no;
            } 
        } 

        elseif($role == config('app.project.role_slug.student_role_slug'))
        {
            $users_data = $this->StudentModel->where('user_id',$id)->first();
            if(isset($users_data) && count($users_data)>0)
            {
               $data = $users_data->student_no;
            }
        }  

        elseif($role == config('app.project.role_slug.parent_role_slug'))
        {
            $users_data = $this->ParentModel->where('user_id',$id)->first();
            if(isset($users_data) && count($users_data)>0)
            {
               $data = $users_data->parent_no;
            }
        }        

        return $data;
    }
    public function generate_book_no($type){

        $number = rand(0,999999); 
        $number = str_pad($number, 6, 0, STR_PAD_LEFT);
        if($type=="book"){   
            $book_no = 'BK'.date('Y').$number;
        }
        elseif($type=="cd"){
            $book_no = 'CD'.date('Y').$number;
        }
        else{
            $book_no = 'DI'.date('Y').$number;
        }

        $count = $this->BookDetailsModel->where('book_no',$book_no)->count();
        if($count){
            return $this->generate_book_no($type);
        }
        
        return $book_no;
    
    }
   
}
