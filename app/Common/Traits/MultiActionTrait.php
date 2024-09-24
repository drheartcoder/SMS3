<?php 

namespace App\Common\Traits;

use Illuminate\Http\Request;
use App\Events\ActivityLogEvent;
use App\Http\Controllers\Controller;
use Flash;
use Validator;


trait MultiActionTrait
{
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
               $this->perform_delete(base64_decode($record_id));    
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

    public function activate($enc_id = FALSE)
    {
        if(!$enc_id)
        {
            return redirect()->back();
        }

        if($this->perform_activate(base64_decode($enc_id)))
        {
            Flash::success($this->module_title.' '.translation('activated_successfully'));
        }
        else
        {
            Flash::error(translation('problem_occured_while_doing').' '.translation('activation'));
        }

        return redirect()->back();
    }

    public function deactivate($enc_id = FALSE)
    {   
        
        if(!$enc_id)
        {
            return redirect()->back();
        }

        if($this->perform_deactivate(base64_decode($enc_id)))
        {
            Flash::success($this->module_title.' '.translation('deactivated_successfully'));
        }
        else
        {
            Flash::error(translation('problem_occured_while_doing').' '.translation('deactivation'));
        }

        return redirect()->back();
    }

    public function delete($enc_id = FALSE)
    {
        if(!$enc_id)
        {
            return redirect()->back();
        }

        if($this->perform_delete(base64_decode($enc_id)))
        {
            Flash::success($this->module_title.' '.translation('deleted_succesfully'));
        }
        else
        {
            Flash::error(translation('problem_occured_while_doing').' '.translation('multiaction'));
        }

        return redirect()->back();
    }


    public function perform_activate($id)
    {
        $static_page = $this->BaseModel->where('id',$id)->first();
        
        if($static_page)
        {

            return $static_page->update(['is_active'=>1]);
        }

        return FALSE;
    }

    public function perform_deactivate($id)
    { 
        $static_page = $this->BaseModel->where('id',$id)->first();
        if($static_page)
        {
            return $static_page->update(['is_active'=>0]);
        }

        return FALSE;
    }

    public function perform_delete($id)
    {
        $delete= $this->BaseModel->where('id',$id)->delete();

        
        if($delete)
        {  
            return TRUE;
        }

        return FALSE;
    }
   
   
}