<?php

namespace App\Http\Controllers\Professor;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Common\Traits\MultiActionTrait;

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
    
    use MultiActionTrait;
    public function __construct(CommonDataService $common_data_service,
                                NewsModel $news
                               )
    {

        $this->arr_view_data                = [];
        $this->module_url_path              = url(config('app.project.professor_panel_slug')).'/news';
        $this->module_title                 = translation('news');
 
        $this->module_view_folder           = "professor.news";
        $this->theme_color                  =  theme_color();
        $this->module_icon                  = 'fa fa-newspaper-o';
        $this->create_icon                  = 'fa fa-plus-circle';
        $this->edit_icon                    = 'fa fa-edit';
        $this->view_icon                    = 'fa fa-eye';
        $this->school_id                    = Session::get('school_id');
        $this->academic_year                = Session::get('academic_year');
        $this->CommonDataService            = $common_data_service;
		$this->NewsModel 					= $news;
		$this->BaseModel					= $this->NewsModel;
		$this->role 						= config('app.project.professor_panel_slug');

        $obj_data          = Sentinel::getUser();
        if($obj_data)
        {
            $this->user_id    = $obj_data->id;
            $this->first_name = $obj_data->first_name;
            $this->last_name  = $obj_data->last_name;
            $this->email      = $obj_data->email;
        }

        
        $role = Sentinel::findRoleBySlug($this->role);
        $this->arr_current_user_access = isset($role->permissions) && ($role->permissions!='') ? $role->permissions : [] ;	


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


        /* update news publish id */
         

         

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
                                ->where($news.'.is_published',1)
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
                               

                                $build_reply_action = '';
                               
                                $view_href =  $this->module_url_path.'/view/'.base64_encode($data->id);
                                $build_reply_action  = '<a class="green-color" href="'.$view_href.'" title="'.translation('view').'"><i class="fa fa-eye"></i></a>';
                                
                                return $build_reply_action;
                                     
                                });
         
        $json_result =      $json_result->editColumn('publish_date',function($data){
                           				return getDateFormat($data->publish_date);
                                        /*.' '.getTimeFormat($data->start_time).' '.translation('to').' '.getDateFormat($data->end_date).' '.getTimeFormat($data->end_time)*/
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
                                ->make(true);

        $build_result = $json_result->getData();
        
        return response()->json($build_result);
    }

   


    /*
    | store() : View
    | Auther  : Padmashri
    | Date    : 21-06-2018
    */
    public function view($enc_id)
    {


        $id = base64_decode($enc_id);
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
