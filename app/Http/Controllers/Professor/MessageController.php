<?php

namespace App\Http\Controllers\Professor;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\MessageModel;
use App\Common\Services\CommonDataService;
use App\Models\UserModel;

use Session;
use Sentinel;


class MessageController extends Controller
{
    public function __construct(CommonDataService $CommonDataService){

        $this->arr_view_data                = [];
        $this->module_url_path              = url(config('app.project.professor_panel_slug')).'/message';
        $this->module_title                 = translation('message');
 
        $this->module_view_folder           = "professor.message";
        $this->theme_color                  =  theme_color();
        $this->module_icon                  = 'fa fa-comments-o';
        $this->create_icon                  = 'fa fa-plus-circle';
        $this->edit_icon                    = 'fa fa-edit';
        $this->school_id                    = Session::get('school_id');
        $this->academic_year                = Session::get('academic_year');
        $this->level_class_id               = Session::get('level_class_id');

        $this->MessageModel      = new MessageModel();
        $this->CommonDataService = $CommonDataService;
        $this->UserModel        = new UserModel();

        $this->profile_image_public_img_path = url('/').config('app.project.img_path.user_profile_images');
        $this->profile_image_base_img_path   = base_path().config('app.project.img_path.user_profile_images');

        $this->arr_view_data['page_title']      = translation('message');
        $this->arr_view_data['module_title']    = translation('message');
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        $this->arr_view_data['module_icon']     = $this->module_icon;
        $this->arr_view_data['profile_image_public_img_path']     = $this->profile_image_public_img_path;

        $obj_data          = Sentinel::getUser();
        if($obj_data)
        {
            $this->user_id    = $obj_data->id;
            $this->first_name = $obj_data->first_name;
            $this->last_name  = $obj_data->last_name;
            if($obj_data->profile_image!=''){
                $this->profile_image= $this->profile_image_public_img_path.$obj_data->profile_image;    
            }
            else{
                $this->profile_image= url('/').'/images/default-profile.png';   
            }
        }

    }
    public function index(){

        $arr_parents =[];
        $obj_levels = $this->CommonDataService->get_levels_for_professor($this->academic_year,$this->user_id,'optional');
        $parent_ids = [];
        if(isset($obj_levels) && count($obj_levels)>0){

            foreach($obj_levels as $obj_level){
                $level_class_id = $obj_level->level_class_id;
                $obj_parents  = $this->CommonDataService->get_parent(0,$level_class_id);

                if(isset($obj_parents) && count($obj_parents)>0){
                    foreach($obj_parents as $parent){
                        if(!in_array($parent['get_parent_details']['id'],$parent_ids))
                        {
                            array_push($parent_ids,$parent['get_parent_details']['id']);
                            $arr_data                  = [];
                            $arr_data['id']            = isset($parent['get_parent_details']['id']) ? $parent['get_parent_details']['id'] :'' ;
                            $arr_data['first_name']    = isset($parent['get_parent_details']['first_name']) ? $parent['get_parent_details']['first_name'] :'' ;
                            $arr_data['last_name']     = isset($parent['get_parent_details']['last_name']) ? $parent['get_parent_details']['last_name'] :'';
                            $arr_data['profile_image'] = isset($parent['get_parent_details']['profile_image']) ? $parent['get_parent_details']['profile_image'] :'';
                            $arr_data['level'] = isset($parent['get_level_class']['level_details']['level_name']) ? $parent['get_level_class']['level_details']['level_name'] :'';
                            $arr_data['class'] = isset($parent['get_level_class']['class_details']['class_name']) ? $parent['get_level_class']['class_details']['class_name'] :'';
                            $first_name = isset($parent['get_user_details']['first_name']) ? $parent['get_user_details']['first_name'] :'';
                            $last_name = isset($parent['get_user_details']['last_name']) ? $parent['get_user_details']['last_name'] :'';
                            $arr_data['student'] = $first_name.' '.$last_name;
                            array_push($arr_parents,$arr_data);
                        }
                        
                    }     
                }
            }
        }
        
        $this->arr_view_data['arr_parents']  = $arr_parents;
        
        return view($this->module_view_folder.'.index', $this->arr_view_data);
    }

    public function get_chat(Request $request,$enc_id=FALSE){

        $id = base64_decode($enc_id);

        if($request->has('last_message') && $request->last_message!="undefined"){
            $last_message = $request->last_message;
        }
        else
        {
            $last_message = 0;
        }
        $data = [];
        $data['user_details']=[];
        $data['chat_div'] = '';
        $data['last_id'] = 0;
        $div ='';
        if($last_message == 0){
            $div .= '<section id="tabone">';
                        $div .= '<div class="messages-section-main">';
        }
                
        $user_details = $this->UserModel->where('id',$id)->first();

        if(isset($user_details) && count($user_details)>0){
            $first_name                   = ucfirst($user_details->first_name);
            $last_name                    = ucfirst($user_details->last_name);
            $name                         = $first_name.' '.$last_name;
            $data['user_details']['name'] = $name;
            $data['user_details']['id'] = base64_encode($user_details->id);
            
            if(isset($user_details->profile_image) && $user_details->profile_image!=''){
                $profile_image = $this->profile_image_public_img_path.$user_details->profile_image;
            }
            else{
                $profile_image = url('/').'/images/default-profile.png';
            }

            $chat = $this->MessageModel
                                ->whereRaw('(( from_user_id='.$this->user_id.' AND to_user_id='.$id.') OR (from_user_id='.$id.' AND to_user_id='.$this->user_id.'))' )
                                ->where('school_id',$this->school_id);
                                if($last_message!=0){
                                   $chat->where('id','>',$last_message);
                                }
                                
                                $chat = $chat->get();

            $this->MessageModel
                                ->where('to_user_id',$this->user_id)
                                ->where('school_id',$this->school_id)
                                ->update(['is_read'=>1]);                    



            if(isset($chat) && count($chat)>0){

                //$arr_chat = $chat->toArray();     
                
                foreach($chat as $key=>$message){
                    $data['last_id'] = $message->id;
                    $message_date = '';
                    if(isset($message->message_date) && $message->message_date!='0000-00-00'){
                        $message_date = date_create($message->message_date);
                        $message_date = date_format($message_date,'d M');
                    }
                    if(isset($message->message_time) && $message->message_time!='00:00:00')
                    {
                        $message_time = date_create($message->message_time);
                        $message_time = date_format($message_time,'h:i a');
                    }

                    if($key==0 && $message->from_user_id != $this->user_id){


                    }

                    if($message->from_user_id == $this->user_id){

                        $div .= '<div class="left-message-block right-message-block';
                        if($key==count($chat)-1){
                            $div .= ' message-id" data-message='.$message->id;
                        }
                        else{
                            $div .='"';    
                        }
                        $div .='>';
                            $div .= '<div class="rights-message-profile">';
                                $div .= '<div class="left-message-profile">';
                                    $div .= '<img src="'.$this->profile_image.'"" alt="" />';
                                $div .= '</div>';
                            $div .= '</div>';
                            $div .= '<div class="left-message-content">';
                                $div .= '<div class="actual-message">';
                                    $div .= $message->text_message;
                                $div .= '</div>';
                                $div .= '<div class="message-time">';
                                    $div .= $message_date.' '.$message_time;
                                $div .= '</div>';
                            $div .= '</div>';
                        $div .= '</div>';

                    }
                    else{
                        
                        $div .= '<div class="left-message-block';
                        if($key==count($chat)-1){
                            $div .= ' message-id" data-message='.$message->id;
                        }
                        else{
                            $div .='"';    
                        }
                        $div .='>';
                            $div .= '<div class="left-message-profile-main">';
                                $div .= '<div class="left-message-profile">';
                                    $div .= '<img src="'.$profile_image.'"" alt="" />';
                                $div .= '</div>';
                            $div .= '</div>';
                            $div .= '<div class="left-message-content">';
                                $div .= '<div class="actual-message">';
                                    $div .= $message->text_message;
                                $div .= '</div>';
                                $div .= '<div class="message-time">';
                                    $div .= $message_date.' '.$message_time;
                                $div .= '</div>';
                            $div .= '</div>';
                        $div .= '</div>';    
                        
                    }

                    
                }
                
            }
        }
        if($last_message == 0){
            $div .= '</div>';
                    $div .= '</section>';
        }                        
                $data['chat_div']  = $div;
                $data['count']  = count($chat);

                
        return json_encode($data);
    }

    public function send_message(Request $request){

        $message      = $request->message;
        $from_user_id = $this->user_id;
        $to_user_id   = base64_decode($request->id);
        $message_date = date('Y-m-d');
        $message_time = date('Y-m-d H:i:s');
        
        $arr_data                 = [];
        $arr_data['school_id']    = $this->school_id;
        $arr_data['from_user_id'] = $from_user_id;
        $arr_data['to_user_id']   = $to_user_id;
        $arr_data['message_date'] = $message_date;
        $arr_data['message_time'] = $message_time;
        $arr_data['text_message'] = $message;
        $arr_data['is_read'] = 0;

        $message = $this->MessageModel->create($arr_data);

        $data = [];
        $data['user_details']['name'] ='';
        $data['user_details']['id'] = base64_encode(0);
        $data['count'] = 0;
        $data['chat_div'] = '';
        $data['last_id'] = $message->id;
        $id = base64_decode($request->id);

        $chat_count = $this->MessageModel
                                ->whereRaw('(( from_user_id='.$this->user_id.' AND to_user_id='.$id.') OR (from_user_id='.$id.' AND to_user_id='.$this->user_id.'))' )
                                ->where('school_id',$this->school_id)
                                ->count();
        $data['count'] = $chat_count;                        
        
        $div ='';
        if($chat_count==1){
            $div .= '<section id="tabone">';
                        $div .= '<div class="messages-section-main">';
        }
                
        $user_details = $this->UserModel->where('id',$id)->first();

        if(isset($user_details) && count($user_details)>0){
            $first_name                   = ucfirst($user_details->first_name);
            $last_name                    = ucfirst($user_details->last_name);
            $name                         = $first_name.' '.$last_name;
            $data['user_details']['name'] = $name;
            $data['user_details']['id'] = base64_encode($user_details->id);
            
            if(isset($user_details->profile_image) && $user_details->profile_image!=''){
                $profile_image = $this->profile_image_public_img_path.$user_details->profile_image;
            }
            else{
                $profile_image = url('/').'/images/default-profile.png';
            }
                    
            $message_date = '';
            if(isset($message->message_date) && $message->message_date!='0000-00-00'){
                $message_date = date_create($message->message_date);
                $message_date = date_format($message_date,'d M');
            }
            if(isset($message->message_time) && $message->message_time!='00:00:00')
            {
                $message_time = date_create($message->message_time);
                $message_time = date_format($message_time,'h:i a');
            }

            if($message->from_user_id == $this->user_id){

                $div .= '<div class="left-message-block right-message-block message-id" data-message="'.$message->id.'">';
                    $div .= '<div class="rights-message-profile">';
                        $div .= '<div class="left-message-profile">';
                            $div .= '<img src="'.$this->profile_image.'"" alt="" />';
                        $div .= '</div>';
                    $div .= '</div>';
                    $div .= '<div class="left-message-content">';
                        $div .= '<div class="actual-message">';
                            $div .= $message->text_message;
                        $div .= '</div>';
                        $div .= '<div class="message-time">';
                            $div .= $message_date.' '.$message_time;
                        $div .= '</div>';
                    $div .= '</div>';
                $div .= '</div>';

                
            }
            else{

                $div .= '<div class="left-message-block message-id" data-message="'.$message->id.'">';
                    $div .= '<div class="left-message-profile-main">';
                        $div .= '<div class="left-message-profile">';
                            $div .= '<img src="'.$profile_image.'"" alt="" />';
                        $div .= '</div>';
                    $div .= '</div>';
                    $div .= '<div class="left-message-content">';
                        $div .= '<div class="actual-message">';
                            $div .= $message->text_message;
                        $div .= '</div>';
                        $div .= '<div class="message-time">';
                            $div .= $message_date.' '.$message_time;
                        $div .= '</div>';
                    $div .= '</div>';
                $div .= '</div>';    

            }
        }
        if($chat_count==1){
            $div .= '</div>';
                    $div .= '</section>';
        }            
                $data['chat_div']  = $div;
        return json_encode($data);
    }
}
