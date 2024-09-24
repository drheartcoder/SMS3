<?php

namespace App\Http\Controllers\SchoolAdmin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\NewsModel;
use App\Models\NewsMediaModel;
use App\Common\Services\CommonDataService;

use DB;
use Flash;
use Session;
use Sentinel;
use Validator;
use Datatables;


class NewsController extends Controller
{
    
        
    public function __construct(CommonDataService $common_data_service,
                                NewsModel $news
                               )
    {

        $this->arr_view_data                = [];
        $this->module_url_path              = url(config('app.project.school_admin_panel_slug')).'/news';
        $this->module_title                 = translation('news');
 
        $this->module_view_folder           = "schooladmin.news";
        $this->theme_color                  =  theme_color();
        $this->module_icon                  = 'fa fa-newspaper-o';
        $this->create_icon                  = 'fa fa-plus-circle';
        $this->edit_icon                    = 'fa fa-edit';
        $this->view_icon                    = 'fa fa-eye';
        $this->school_id                    = Session::get('school_id');
        $this->academic_year                = Session::get('academic_year');
        $this->arr_view_data['page_title']  = translation('course_material');
		$this->CommonDataService            = $common_data_service;
		$this->NewsModel 					= $news;
		$this->BaseModel					= $this->NewsModel;
		$this->role 						= config('app.project.school_admin_panel_slug');

        $obj_data          = Sentinel::getUser();
        if($obj_data)
        {
            $this->user_id    = $obj_data->id;
            $this->first_name = $obj_data->first_name;
            $this->last_name  = $obj_data->last_name;
            $this->email      = $obj_data->email;
        }

        
        $role = Sentinel::findRoleBySlug($this->role);
    
        $this->arr_current_user_access = $this->CommonDataService->current_user_access();


 		$this->news_public_path = url('/').config('app.project.img_path.news_media');
        $this->news_base_path   = base_path().config('app.project.img_path.news_media');  
        $this->newsUploadImagePath = public_path().config('app.project.img_path.news_media');
    }

    /*
    | index() : List News
    | Auther  : Padmashri
    | Date    : 22-06-2018
    */
    public function index(){

        $page_title = translation("manage")." ".str_plural($this->module_title);
        
        $this->CommonDataService->getNewsPublishDate();
         


        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['page_title']      = $page_title;
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        
        return view($this->module_view_folder.'.index',$this->arr_view_data);
    }

    /*
    | get_details() : To get the List News
    | Auther  : Padmashri
    | Date    : 22-06-2018
    */
    public function get_details(Request $request){

        $arr_academic_year = '';
        $academic_year = $this->CommonDataService->get_academic_year_less_than($this->academic_year); 
        if(!empty($academic_year)){
            $arr_academic_year = explode(',',$academic_year);
        }
        $news             = $this->BaseModel->getTable();
        $obj_user		  = DB::table($news)
                                ->select(DB::raw(
                                				 $news.".id  as id,".
                                				 $news.".news_title  as    news_title,".
                                                 $news.".publish_date, ".
                                                 $news.".end_date, ".
                                                 $news.".added_date_time, ".
                                                 $news.".start_time, ".
                                                 $news.".end_time, ".
                                             	 $news.".is_published "
                                             	 ))
                                ->whereNull($news.'.deleted_at')
                                ->where($news.'.school_id','=',$this->school_id)
                                ->whereIn($news.'.academic_year_id',$arr_academic_year)
                                ->orderBy($news.'.id','desc');



        $search = $request->input('search');
        $search_term = $search['value'];

        if($request->has('search') && $search_term!="")
        {
            $obj_user = $obj_user->WhereRaw("( (".$room_management_details.".news_title LIKE '%".$search_term."%') ")
                                     ->orWhereRaw("(".$room_management_details.".publish_date LIKE '%".$search_term."%') ")
                                     ->orWhereRaw("(".$prefixed_room_details.".end_date LIKE '%".$search_term."%') ")
                                     ->orWhereRaw("(".$prefixed_room_details.".added_date_time LIKE '%".$search_term."%') ") 
                                     ->orWhereRaw("(".$class_trans.".start_time LIKE '%".$search_term."%') ")
                                     ->orWhereRaw("(".$level_trans.".end_time LIKE '%".$search_term."%')) ");
        }
        /* ---------------- Filtering Logic ----------------------------------*/                    
        return $obj_user;
    }

    /*
    | get_records() : To get the List News
    | Auther  : Padmashri
    | Date    : 22-06-2018
    */
    public function get_records(Request $request){

        $arr_current_user_access  = $this->arr_current_user_access;
    	$obj_user        = $this->get_details($request);
        $current_context = $this;

        $json_result     = Datatables::of($obj_user);
        $json_result     = $json_result->blacklist(['id']);
        
      
            $json_result     = $json_result->editColumn('enc_id',function($data) use ($current_context){
                                return base64_encode($data->id);
                            });
                          
             $json_result     = $json_result->editColumn('build_action_btn',function($data) use ($current_context,$arr_current_user_access){
                                           
                                $build_delete_action =  $build_edit_action =  ''; 
                               if(array_key_exists('news.update', $arr_current_user_access))
                               {
                                $edit_href =  $this->module_url_path.'/edit/'.base64_encode($data->id);
                                $build_edit_action = '<a class="orange-color" href="'.$edit_href.'" title="'.translation('edit').'"><i class="fa fa-edit" ></i></a>';
                               }
                                    
                               if(array_key_exists('news.delete', $arr_current_user_access))
                               { 

                                $delete_href =  $this->module_url_path.'/delete/'.base64_encode($data->id);
                                $build_delete_action = '<a class="red-color"  href="'.$delete_href.'" title="'.translation('delete').'" onclick="return confirm_action(this,event,\''.translation('do_you_really_want_to_delete_this_record').' ?\',\''.translation("are_you_sure").'\',\''.translation('yes').' \',\''.translation("no").'\')"><i class="fa fa-trash" ></i></a>';

							   }

                                $build_reply_action = '';
                               
                                $view_href =  $this->module_url_path.'/view/'.base64_encode($data->id);
                                $build_reply_action  = '<a class="green-color" href="'.$view_href.'" title="'.translation('view').'"><i class="fa fa-eye"></i></a>';
                                
                                return $build_reply_action.'&nbsp;'.$build_edit_action.'&nbsp;'.$build_delete_action;
                                     
                                });
         
        $json_result =      $json_result->editColumn('build_checkbox',function($data){
                           
                                $build_checkbox = '<div class="check-box"><input type="checkbox" class="filled-in case" name="checked_record[]"  id="mult_change_'.base64_encode($data->id).'" value="'.base64_encode($data->id).'" /><label for="mult_change_'.base64_encode($data->id).'"></label></div>'; 
                                
                                return $build_checkbox;
                                })
                                ->editColumn('news_image',function($data){
                                        $img = $this->get_news_media($data->id);
                                        
                                        if($img!='')
                                        {

                                        $image =  '<img src="'.$img.'" alt="" height="70" width="80"/>';
                                        }else{
                                            $image =  '<img src="'.url('/').'/images/default-old.png" alt="" height="70" width="80"/>';
                                        }
                                        return $image;
                                })
       						    ->editColumn('publish_date',function($data){
                           				return getDateFormat($data->publish_date).' '.getTimeFormat($data->start_time).' '.translation('to').' '.getDateFormat($data->end_date).' '.getTimeFormat($data->end_time);
                                })
                                ->editColumn('created_at',function($data){
                           				return getDateTimeFormat($data->added_date_time);
                                })
                                ->editColumn('is_live',function($data){
                                		$isLive = '';
                           				if($data->is_published == 1){
                           					$isLive = translation('yes');
                           				}else{
                           					$isLive = translation('no');
                           				}
                           				return $isLive;
                                })
                                ->make(true);

        $build_result = $json_result->getData();
        
        return response()->json($build_result);
    }

    /*
    | create() : Create News
    | Auther  : Padmashri
    | Date    : 22-06-2018
    */
    public function create(){
		$this->arr_view_data['page_title']      = translation('add')." ".$this->module_title;
        $this->arr_view_data['module_title']    = $this->module_title;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color'] 	= $this->theme_color;
        $this->arr_view_data['module_icon']		= $this->module_icon;
        $this->arr_view_data['create_icon'] 	= $this->create_icon;
        return view($this->module_view_folder.'.create',$this->arr_view_data);
    }

    /*
    | store() : Store  News
    | Auther  : Padmashri
    | Date    : 22-06-2018
    */
    public function store(Request $request){
        

        $arr_rules = [];
        $arr_rules['news_title']    = 'required';
        $arr_rules['description']   = 'required';
        $arr_rules['publish_date'] 	= 'required|date';
		$arr_rules['end_date']   	= 'required|date';
        $arr_rules['start_time']  	= 'required';
        $arr_rules['end_time']  	= 'required';

        $messages = array(  'required'             => translation('this_field_is_required'),
                            'date'                 => translation('please_enter_valid_date'));

        $validator = Validator::make($request->all(),$arr_rules,$messages);
        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        } 

        
        $news_title     =   trim($request->input('news_title'));
        $description    =   trim($request->input('description'));
		$publish_date   =   trim($request->input('publish_date'));
        $end_date  		=   trim($request->input('end_date'));
        $start_time  	=   trim($request->input('start_time'));
        $end_time  		=   trim($request->input('end_time'));
        $media_url  	=   $request->input('media_url');
        $news_media  	=   $request->file('news_media');
        $media_images   = 	$request->file('media_images');
        
         
       

        $arr_data = [];     
        $arr_data['school_id']          = $this->school_id;
        $arr_data['news_title']         = $news_title;
        $arr_data['description']        = $description;
        $arr_data['publish_date']       = isset($publish_date) ? date('Y-m-d',strtotime($publish_date)):'0000-00-00'; 
        $arr_data['end_date']           = isset($end_date) ? date('Y-m-d',strtotime($end_date)):'0000-00-00'; 
        $arr_data['start_time']         = isset($start_time)&&$start_time!=''?$start_time:'';
        $arr_data['end_time']           = isset($end_time)&&$end_time!=''?$end_time:'';
        $arr_data['video_url']          = isset($media_url)&&$media_url!=''?$media_url:'';        
        $arr_data['academic_year_id']	= $this->academic_year;
        $arr_data['added_date_time']    = date('Y-m-d H:i:s');

        
           
                
        
        $res = $this->NewsModel->create($arr_data);
        if($res){

            /*Add Media To the other table 
            Please Note : Code is created by considering the future requirement if we need to add add more functionlity for news media like pdf,doc,mp4*/
            if(!empty($news_media)){
	         if($request->hasFile('news_media')){
                  $file_validation = Validator::make(array('file'=>$request->file('news_media')),
                                                            array('file'=>'mimes:doc,pdf,mp4'));
            
                    if($request->file('news_media')->isValid() && $file_validation->passes())
                    {

                        $news_media = array();
                        $file_name       = '';
                        $excel_file_name = $request->file('news_media');
                        $name = explode('.', $_FILES['news_media']['name']);

                        $fileName = $request->file('news_media')->getClientOriginalName();
                        $fileExtension   = strtolower($request->file('news_media')->getClientOriginalExtension()); 


                        $newFileName = '';
                        $pos = strrpos($fileName,'.'.$fileExtension);

                        if($pos !== false)
                        {
                            $newFileName = substr_replace($fileName,'',$pos,strlen('.'.$fileExtension));
                        }



                        $file_name       = $newFileName.'_'.sha1(uniqid().$excel_file_name.uniqid()).'.'.$fileExtension;

                      
                        $request->file('news_media')->move($this->newsUploadImagePath,$file_name); 
                        
                        $news_media['news_id']       = $res->id;
                        $news_media['media_name']    = $file_name;
                        $news_media['video_url']     = '';
                        $news_media['media_type']    = 'other';
                        $resImage  = NewsMediaModel::create($news_media);
                    }
                }
		    }

          


        	
        	if(!empty($media_images)){
        	    for($i=0;$i<count($media_images);$i++){
                    
                     if($request->hasFile('media_images.'.$i)){
                          $image_validation = Validator::make(array('file'=>$request->file('media_images.'.$i)),
                                                                    array('file'=>'mimes:png,jpeg,jpg'));
                            
                            if($request->file('media_images.'.$i)->isValid() && $image_validation->passes())
                            {

                                $news_media =  array();
                                $file_name       = '';
                                $excel_file_name = $request->file('media_images.'.$i);
                              
                                $fileExtension   = strtolower($request->file('media_images.'.$i)->getClientOriginalExtension()); 
                                $file_name       = sha1(uniqid().$excel_file_name.uniqid()).'.'.$fileExtension;

                                $request->file('media_images.'.$i)->move($this->newsUploadImagePath,$file_name); 
                                
                                $news_media['news_id']       = $res->id;
                                $news_media['media_name']    = $file_name;
                                $news_media['media_type']    = 'img';
                                $news_media['video_url']     = '';
                                $resImage  = NewsMediaModel::create($news_media);
                            }
                      }
                }
			}


        	/*Add Media To the other table */
            Flash::success($this->module_title." ".translation("created_successfully"));
            return redirect()->back();
        }else{
            Flash::error("something_went_wrong_while_creating ".$this->module_title);
            return redirect()->back();
        }        

    }
     /*
    | edit()  : Edit  News
    | Auther  : Padmashri
    | Date    : 26-05-2018
    */
    public function edit($enc_id=FALSE)
    {
        $id = base64_decode($enc_id);

        if(!is_numeric($id)){

            Flash::error(translation('something_went_wrong'));
            return redirect($this->module_url_path);
        }

        $obj_data = $arr_data =  $arr_selected_user = [];
        $obj_data = NewsModel::with(['get_news_media'])->where('id',$id)->first();
        if($obj_data){
           $arr_news_data = $obj_data->toArray();
        }

        $arr_images = $arr_youtube = $arr_other =  array();
        if(!empty($arr_news_data['get_news_media'])){
            foreach ($arr_news_data['get_news_media'] as $key => $value) {
                if($value['media_type'] == 'img' ){
                    array_push($arr_images, $value);
                }
 
                if($value['media_type'] == 'other' ){
                    
                    array_push($arr_other, $value);
                }
            }

        }
        
        $this->arr_view_data['page_title']      = translation('edit')." ".$this->module_title;
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['edit_mode']       = TRUE;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['enc_id']          = $enc_id;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['edit_icon']       = $this->edit_icon;
        
        $this->arr_view_data['arr_news_data']      = $arr_news_data;
        $this->arr_view_data['arr_images']         = $arr_images;
        $this->arr_view_data['arr_other']          = $arr_other;


     

        $this->arr_view_data['newsUploadImagePath']     = $this->newsUploadImagePath;
        $this->arr_view_data['newsUploadImageBasePath'] = $this->news_base_path;
        $this->arr_view_data['newsUploadImagePublicPath'] = $this->news_public_path;

        
        return view($this->module_view_folder.'.edit',$this->arr_view_data);
    }

    /*
    | edit()  : Update  News
    | Auther  : Padmashri
    | Date    : 26-05-2018
    */
    public function update(Request $request,$enc_id=FALSE)
    {
        $id = base64_decode($enc_id);     
        if(!is_numeric($id)){
            
            Flash::error(translation('something_went_wrong'));
            return redirect($this->module_url_path);
        }
        $arr_rules = [];
        $arr_rules['news_title']    = 'required';
        $arr_rules['description']   = 'required';
        $arr_rules['publish_date']  = 'required|date';
        $arr_rules['end_date']      = 'required|date';
        $arr_rules['start_time']    = 'required';
        $arr_rules['end_time']      = 'required';

        $messages = array(  'required'             => translation('this_field_is_required'),
                            'date'                 => translation('please_enter_valid_date'));

        $validator = Validator::make($request->all(),$arr_rules,$messages);
      
        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        } 

        $news_title     =   trim($request->input('news_title'));
        $description    =   trim($request->input('description'));
        $publish_date   =   trim($request->input('publish_date'));
        $end_date       =   trim($request->input('end_date'));
        $start_time     =   trim($request->input('start_time'));
        $end_time       =   trim($request->input('end_time'));
        $media_url      =   $request->input('media_url');
        $news_media     =   $request->file('news_media');
        $media_images   =   $request->file('media_images');

        $arr_data = [];     
        $arr_data['news_title']         = $news_title;
        $arr_data['description']        = $description;
        $arr_data['publish_date']       = isset($publish_date) ? date('Y-m-d',strtotime($publish_date)):'0000-00-00'; 
        $arr_data['end_date']           = isset($end_date) ? date('Y-m-d',strtotime($end_date)):'0000-00-00'; 
        $arr_data['start_time']         = isset($start_time)&&$start_time!=''?$start_time:'';
        $arr_data['end_time']           = isset($end_time)&&$end_time!=''?$end_time:'';
        $arr_data['video_url']          = isset($media_url)&&$media_url!=''?$media_url:'';        
        $res = $this->NewsModel->where('id',$id)->update($arr_data);
        /* Update Image */

     
        /* Images */
        if(!empty($media_images)){
                for($i=0;$i<count($media_images);$i++){
                    
                     if($request->hasFile('media_images.'.$i)){
                        
                          $image_validation = Validator::make(array('file'=>$request->file('media_images.'.$i)),
                                                                    array('file'=>'mimes:png,jpeg,jpg'));
                            
                            if($request->file('media_images.'.$i)->isValid() && $image_validation->passes())
                            {

                                $data_array      = array();   
                                $file_name       = '';
                                $excel_file_name = $request->file('media_images.'.$i);
                              
                                

                               $fileName = $request->file('media_images.'.$i)->getClientOriginalName();
                               $fileExtension   = strtolower($request->file('media_images.'.$i)->getClientOriginalExtension()); 


                                $newFileName = '';
                                $pos = strrpos($fileName,'.'.$fileExtension);

                                if($pos !== false)
                                {
                                    $newFileName = substr_replace($fileName,'',$pos,strlen('.'.$fileExtension));
                                }

                                $file_name       = $newFileName.'_'.sha1(uniqid().$excel_file_name.uniqid()).'.'.$fileExtension;

                                $request->file('media_images.'.$i)->move($this->newsUploadImagePath,$file_name); 
                                
                                $data_array['news_id']       = $id;
                                $data_array['media_name']    = $file_name;
                                $data_array['media_type']    = 'img';
                                $data_array['video_url']     = '';
                                $resImage  = NewsMediaModel::create($data_array);
                            }
                      }
                }
        }
        /* Images */

        

         /*Add Media To the other table 
            Please Note : Code is created by considering the future requirement if we need to add add more functionlity for news media like pdf,doc,mp4*/
            if(!empty($news_media)){
             if($request->hasFile('news_media')){

                /*Unlink old  document and delete the image*/
                $old_document_name = $request->input('old_document_name');
                if($old_document_name){

                        $fileURL = $this->news_base_path.'/'.$old_document_name;

                        if(file_exists($fileURL))
                        {
                             $unlink_path    = $this->newsUploadImagePath.'/'.$old_document_name;
                                @unlink($unlink_path);
                        }
                        $deleteMedia = NewsMediaModel::where('news_id','=',$id)->where('media_type','=','other')->delete();

                }
                /*Unlink old  document and delete the image*/



                  $file_validation = Validator::make(array('file'=>$request->file('news_media')),
                                                            array('file'=>'mimes:doc,docx,pdf,mp4'));
            
                    if($request->file('news_media')->isValid() && $file_validation->passes())
                    {

                        $array_data = array();
                        $file_name       = '';
                        $excel_file_name = $request->file('news_media');
                      

                        $fileName = $request->file('news_media')->getClientOriginalName();
                        $fileExtension   = strtolower($request->file('news_media')->getClientOriginalExtension()); 


                        $newFileName = '';
                        $pos = strrpos($fileName,'.'.$fileExtension);

                        if($pos !== false)
                        {
                            $newFileName = substr_replace($fileName,'',$pos,strlen('.'.$fileExtension));
                        }
 
                        $file_name       = $newFileName.'_'.sha1(uniqid().$excel_file_name.uniqid()).'.'.$fileExtension;

                        $request->file('news_media')->move($this->newsUploadImagePath,$file_name); 
                        
                        $array_data['news_id']       = $id;
                        $array_data['media_name']    = $file_name;
                        $array_data['video_url']     = '';
                        $array_data['media_type']    = 'other';

                        $res  = NewsMediaModel::create($array_data);

                     }

                }
            }

 
        if($res || $resImage){
            Flash::success($this->module_title." ".translation("updated_successfully"));
            return redirect()->back();
        }else{
            Flash::error("something_went_wrong_while_updating ".$this->module_title);
            return redirect()->back();
        }        
       
    }


    /*
    | store() : View
    | Auther  : Padmashri
    | Date    : 21-06-2018
    */
    public function view($enc_id)
    {


        $id = base64_decode($enc_id);
        if(!is_numeric($id)){
            
            Flash::error(translation('something_went_wrong'));
            return redirect($this->module_url_path);
        }
        $obj_data = $arr_data =  $arr_selected_user = [];
        $obj_data = NewsModel::with(['get_news_media'])->where('id',$id)->first();
        if($obj_data){
           $arr_news_data = $obj_data->toArray();
        }

        $arr_images = $arr_youtube = $arr_other =  array();
        if(!empty($arr_news_data['get_news_media'])){
            foreach ($arr_news_data['get_news_media'] as $key => $value) {
                if($value['media_type'] == 'img' ){
                    array_push($arr_images, $value);
                }
 
                if($value['media_type'] == 'other' ){
                    array_push($arr_other, $value);
                }
            }

        }

        $this->arr_view_data['page_title']      = translation('view')." ".$this->module_title;
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['edit_mode']       = TRUE;
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']        = $this->theme_color;
        $this->arr_view_data['module_icon']        = $this->module_icon;
        $this->arr_view_data['view_icon']          = $this->view_icon;
        $this->arr_view_data['arr_news_data']      = $arr_news_data;
        $this->arr_view_data['arr_images']         = $arr_images;
        $this->arr_view_data['arr_other']          = $arr_other;

        $this->arr_view_data['newsUploadImagePath']     = $this->newsUploadImagePath;
        $this->arr_view_data['newsUploadImageBasePath'] = $this->news_base_path;
        $this->arr_view_data['newsUploadImagePublicPath'] = $this->news_public_path;

        return view($this->module_view_folder.'.view',$this->arr_view_data);
    }


    /*
    | store() : Download  document
    | Auther  : Padmashri
    | Date    : 21-06-2018
    */
    public function download_document($enc_id)
    {


        $arr_document = [];
        if(isset($enc_id))
        {
            $document_id = base64_decode($enc_id);
            $obj_documents = NewsMediaModel::where('id',$document_id)
                                                    ->select('media_name')
                                                    ->first();
                                                
            if($obj_documents)
            {
                  $arr_document    = $obj_documents->toArray();
                  $file_name       = $arr_document['media_name'];
                  $pathToFile      = $this->newsUploadImagePath.'/'.$file_name;

                  $file_exits      = file_exists($pathToFile);
                  if($file_exits)
                  { 
                    return response()->download($pathToFile); 
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


    /*
    | delete()  : delete() delete the news 
    | Auther  : Padmashri
    | Date    : 2-07-2018
    */
    function delete($enc_id){
         $id = base64_decode($enc_id);
         $res = $this->delete_news($id);
        if($res){
            Flash::success($this->module_title." ".translation("deleted_successfully"));
            return redirect()->back();
        }else{
            Flash::error("something_went_wrong_while_deleting".$this->module_title);
            return redirect()->back();
        }        
       
    }


      /*
    | delete()  : multi_action() multiaction for  the news 
    | Auther  : Padmashri
    | Date    : 2-07-2018
    */
    public function multi_action(Request $request)
    {
        $arr_rules = array();
        $arr_rules['multi_action'] = "required";
        $arr_rules['checked_record'] = "required";

        $validator = Validator::make($request->all(),$arr_rules);

        if($validator->fails())
        {
            Flash::error(translation('please_select_record_to_perform_multiaction'));
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $multi_action = $request->input('multi_action');
        $checked_record = $request->input('checked_record');

        /* Check if array is supplied*/
        if(is_array($checked_record) && sizeof($checked_record)<=0)
        {
            Flash::error(translation('problem_occured_while_doing').' '.translation('multiaction') );
            return redirect()->back();
        }

        foreach ($checked_record as $record_id) 
        {  
            if($multi_action=="delete")
            {
               $this->delete_news(base64_decode($record_id));    
               Flash::success($this->module_title.' '.translation('deleted_succesfully')); 
            } 
            elseif($multi_action=="activate")
            {
               $this->perform_activate(base64_decode($record_id)); 
               Flash::success($this->module_title.' '.translation('activated_successfully')); 
            }
            elseif($multi_action=="deactivate")
            {
               $this->perform_deactivate(base64_decode($record_id));    
               Flash::success($this->module_title.' '.translation('deactivated_successfully'));  
            }
            elseif($multi_action=="promote")
            {
                $this->promote_students(base64_decode($record_id));
                Flash::success($this->module_title.' '.translation('promoted_successfully'));  
            }
        }
        return redirect()->back();
    }


    /*
    | delete_news()  : delete() delete the news
    | Auther  : Padmashri
    | Date    : 2-07-2018
    */
    function delete_news($id){
        $flag = 0;
        $getData = NewsModel::with(['get_news_media'])->where('id',$id)->first();
        if(!empty($getData)){
            $arr_data = $getData->toArray();
            if(!empty($arr_data['get_news_media'])){
                foreach ($arr_data['get_news_media'] as $key => $value){
                if($value['media_name']!='' && !empty($value['media_name']))   
                {     
                        $unlink_path    = $this->newsUploadImagePath.'/'.$value['media_name'];
                        @unlink($unlink_path);
                }

                $delImg = NewsMediaModel::where('news_id',$id)->where('id',$value['id'])->delete();
                    
                }
            }

            $flag = NewsModel::where('id',$id)->delete();
        }
        return $flag;

    }


    public function delete_image(Request $request){
        $flag = 'error';
        $imageId = $request->input('id');
        $value = NewsMediaModel::where('id',$imageId)->first();
        if(!empty($value)){

            if($value['media_name']!='' && !empty($value['media_name']))   
            {     
                    $unlink_path    = $this->newsUploadImagePath.'/'.$value['media_name'];
                    @unlink($unlink_path);
            }

            $delImg = NewsMediaModel::where('id',$imageId)->delete();
            if($delImg){
                $flag = 'done';
            }
         }
        return $flag;
    }

     public function get_news_media($news_id)
    {

        $arr_document = [];
        if(isset($news_id))
        {
            $obj_documents = NewsMediaModel::where('id',$news_id)
                                                    ->select('media_name')
                                                    ->where('media_type','img')
                                                    ->orderBy('id','desc')
                                                    ->first();
                                                
            if($obj_documents)
            {
                  $arr_document    = $obj_documents->toArray();
                  //dump($arr_document);
                  $file_name       = $arr_document['media_name'];
                  $pathToFile      = $this->newsUploadImagePath.'/'.$file_name;

                  $file_exits      = file_exists($pathToFile);
                  if($file_exits)
                  { 
                    return $this->news_public_path.'/'.$file_name;
                  }
                  else
                  {
                    
                   return '';
                  }
                  
             }
        }
        else
        {
           return '';
        } 
    }
}
