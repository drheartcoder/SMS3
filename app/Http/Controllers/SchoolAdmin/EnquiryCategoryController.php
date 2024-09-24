<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;


use App\Common\Traits\MultiActionTrait;

use App\Models\EnquiryCategoryModel;  
use App\Common\Services\LanguageService;
use App\Events\ActivityLogEvent;
use App\Models\ActivityLogsModel;


use Sentinel;
use Validator;
use Flash;
 
class EnquiryCategoryController extends Controller
{
    use MultiActionTrait;

    public function __construct(EnquiryCategoryModel $enquiry_category,
                                LanguageService $langauge,
                                ActivityLogsModel $activity_logs)
    {        
        $this->arr_view_data     =   [];
        
        $this->EnquiryCategoryModel          =   $enquiry_category;
        $this->BaseModel                     =   $this->EnquiryCategoryModel;
        $this->ActivityLogsModel = $activity_logs;

        $this->module_title      =   "Enquiry Category";
        $this->LanguageService   =   $langauge;
        $this->module_url_path   =   url(config('app.project.admin_panel_slug')."/enquiry_category");
        $this->module_view_folder=   "admin.enquiry_category";


          /* Activity Section */
        $this->first_name = $this->last_name =$this->ip_address ='';
        $obj_data          = Sentinel::getUser();
        if($obj_data){
            $this->first_name        = $obj_data->first_name;
            $this->last_name         = $obj_data->last_name;
            $this->ip_address        = isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:false;  
        }
        /* Activity Section */
    }   
    

    public function index()
    {
        $arr_lang = array();
        $arr_data = array();
        $obj_data = $this->BaseModel->get();
        $arr_lang = $this->LanguageService->get_all_language();

        if(sizeof($obj_data)>0)
        {
            foreach ($obj_data as $key => $data) 
            {
                $arr_tmp = array();
                
                foreach ($arr_lang as $key => $lang) 
                {
                    $arr_tmp[$key]['title']     = $lang['title'];
                    $arr_tmp[$key]['is_avail']  = $data->hasTranslation($lang['locale']);
                }    
                    $data->arr_translation_status = $arr_tmp;
        
                    unset($obj_data->translations);
            }
        }

        if(!$obj_data)
        {
            $arr_data = $obj_data->toArray();
        }
        $this->arr_view_data['arr_data']        = $arr_data;
        $this->arr_view_data['page_title']      = "Manage ".str_plural( $this->module_title);
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['module_url_path'] = $this->module_url_path;

        return view($this->module_view_folder.'.index',$this->arr_view_data);
    }


    public function create()
    {

        $this->arr_view_data['edit_mode']       = TRUE;
        $this->arr_view_data['arr_lang']        = $this->LanguageService->get_all_language();  
        $this->arr_view_data['page_title']      = "Create ".str_singular( $this->module_title);
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['module_url_path'] = $this->module_url_path;

        return view($this->module_view_folder.'.create',$this->arr_view_data);
    }


    public function store(Request $request)
    {  
        

        /* English Required */
            $arr_lang =  $this->LanguageService->get_all_language();      
            
            /* insert record into translation table */
            if(sizeof($arr_lang) > 0 )
            {
                foreach ($arr_lang as $lang) 
                {            
                    $arr_rules['enquiry_category_'.$lang['locale']] = "required";        
                }

            }    

        $validator = Validator::make($request->all(),$arr_rules);

        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }   

        

        /* Check if location already exists with given translation */
            
        if(sizeof($arr_lang) > 0 )
            {
                foreach ($arr_lang as $lang) 
                { 
                       $does_exists = $this->BaseModel
                            ->whereHas('translations',function($query) use($request)
                                        {
                                            $query->where('locale','en');
                                            $query->where('category_name',$request->input('enquiry_category_en'));      
                                        })
                            ->count();   
                }

            }    
                      
        if($does_exists)
        {
            Flash::error(str_singular($this->module_title).' Already Exists.');
            return redirect()->back();
        }


        $enquiry_category = $request->input('enquiry_category_en');
        /* Insert into Location Table */

        $arr_data = array();
        $arr_data['category_name'] = $enquiry_category;

        $entity = $this->BaseModel->create($arr_data);

        if($entity)      
        {
            $arr_lang =  $this->LanguageService->get_all_language();      
         
            /* insert record into translation table */
            if(sizeof($arr_lang) > 0 )
            {
                foreach ($arr_lang as $lang) 
                {            
                    $arr_data = array();
                    $enquiry_category = $request->input('enquiry_category_'.$lang['locale']);

                    if (isset($enquiry_category) && $enquiry_category != '') 
                    { 
                        $translation = $entity->translateOrNew($lang['locale']);

                        $translation->enquiry_category_id    = $entity->id;
                        $translation->title  = $enquiry_category;
                        $translation->save();
                        Flash::success(str_singular($this->module_title).' Created Successfully');
                    }

                 }
                /*-------------------------------------------------------
                |   Activity log Event
                --------------------------------------------------------*/
                $arr_event                     = [];
                $arr_event['ACTIVITY_MESSAGE'] = str_singular($this->module_title).' Created By '.$this->first_name." ".$this->last_name."";
                $arr_event['IP_ADDRESS']       = $this->ip_address;
                $arr_event['ACTION']           = 'ADD';
                $arr_event['MODULE_TITLE']     = $this->module_title;
                $this->save_activity($arr_event);
                /*----------------------------------------------------------------------*/

            }
            else
            {
                Flash::error('Problem Occured, While Creating '.str_singular($this->module_title));
            }
        }

       

        return redirect()->back();
    }

    public function edit($enc_id)
    {
        $id       = base64_decode($enc_id);
    
        $obj_data = $this->BaseModel->where('id', $id)->with(['translations'])->first();
        $arr_data = [];

        if($obj_data)
        {
           $arr_data = $obj_data->toArray();
           /* Arrange Locale Wise */
           $arr_data['translations'] = $this->arrange_locale_wise($arr_data['translations']);
        }

        $this->arr_view_data['edit_mode']       = TRUE;
        $this->arr_view_data['enc_id']          = $enc_id;
        $this->arr_view_data['arr_lang']        = $this->LanguageService->get_all_language();  
        $this->arr_view_data['arr_data']        = $arr_data; 
        $this->arr_view_data['page_title']      = "Edit ".str_singular($this->module_title);
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['module_url_path'] = $this->module_url_path;

        return view($this->module_view_folder.'.edit',$this->arr_view_data);
    }


    public function update(Request $request, $enc_id)
    {
        $id = base64_decode($enc_id);

        $arr_rules = array();
        /* English Required */
            $arr_lang =  $this->LanguageService->get_all_language();      
         
            /* insert record into translation table */
            if(sizeof($arr_lang) > 0 )
            {
                foreach ($arr_lang as $lang) 
                {            
                    $arr_rules['enquiry_category_'.$lang['locale']] = "required";        
                }

            }    
         
        $validator = Validator::make($request->all(),$arr_rules);
        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        else
        {
            /* Get All Active Languages */ 
            $arr_lang = $this->LanguageService->get_all_language();  

            /* Retrieve Existing FAQ*/
            $entity = $this->BaseModel->where('id',$id)->first();

            if(!$entity)
            {
                Flash::error('Problem Occured While Retriving '.str_singular($this->module_title));
                return redirect()->back();   
            }

            /* Check if location type already exists with given translation */
                if(sizeof($arr_lang) > 0 )
                {
                    foreach ($arr_lang as $lang) 
                    {     
                        $does_exists = $this->BaseModel
                                ->where('id','<>',$id)
                                ->whereHas('translations',function($query) use($request)
                                            {
                                                $query->where('locale','en')
                                                      ->where('category_name',$request->input('enquiry_category_en'));      
                                            })
                                ->count()>0;   
                    }
                }                
            if($does_exists)
            {
                Flash::error(str_singular($this->module_title).' Already Exists');
                return redirect()->back();
            }
            
            $enquiry_category = $request->input('enquiry_category_en');
            $arr_data = array();
            $arr_data['category_name'] = $enquiry_category;

            $this->BaseModel->where('id','=',$id)->update($arr_data);

            /* Insert Multi Lang Fields */

            if(sizeof($arr_lang) > 0)
            { 
                foreach($arr_lang as $lang)
                {
                    $enquiry_category = $request->input('enquiry_category_'.$lang['locale']);

                    if(isset($enquiry_category) && $enquiry_category != '')
                    {
                        /* Get Existing Language Entry */
                        $translation = $entity->getTranslation($lang['locale']);    
                        if($translation)
                        {
                            $translation->title  = $enquiry_category;
                            $translation->save();    
                        }  
                        else
                        {
                            /* Create New Language Entry  */
                            $translation = $entity->getNewTranslation($lang['locale']);
                            $translation->enquiry_category_id    = $id;
                            $translation->title  = $enquiry_category;
                            $translation->save();
                        } 
                    }   
                }
                
            }
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

            Flash::success(str_singular($this->module_title).' Updated Successfully');

            return redirect()->back(); 
        }
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
}
