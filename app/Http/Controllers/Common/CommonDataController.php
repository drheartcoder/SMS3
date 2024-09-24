<?php 
namespace App\Http\Controllers\Common;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\CategoryModel; 
use App\Common\Services\NotificationsService; 

use Validator;
use Session;
use Input;
use Auth;
 
class CommonDataController extends Controller
{
    public function __construct(
                                    CategoryModel $category_model,
                                    NotificationsService $notifications_service
                                )
    {   
        $this->CategoryModel        = $category_model;
        $this->NotificationsService = $notifications_service;
    }

    public function get_subcategories(Request $request)
    {
        
        $id = base64_decode($request->input('enc_id'));

        $res = $this->CategoryModel
                            ->where('is_active',1)
                            ->where('parent',$id)
                            ->get();
        if($res)
        {
            $res = $res->toArray();
            if(sizeof($res))
            {
                return response()->json(['status'=>'success','arr_subcategories'=> $res]);
            }
        }
        return response()->json(['status'=>'error']);
    }
    public function change_notification_status(Request $request)
    {
        $notification_id = $request->input('notification_id');
        $notification_id = base64_decode($notification_id);

        $this->NotificationsService->update_notification_status($notification_id);
        return response()->json(['status'=>'success']);
    }

}