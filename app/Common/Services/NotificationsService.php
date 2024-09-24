<?php

namespace App\Common\Services;

use App\Models\NotificationsModel;

class NotificationsService
{
    public function __construct(NotificationsModel $notifications)
    {
        $this->NotificationsModel = $notifications;
    }

    /******* store notification in database ********/
    public function store_notification($arr_data)
    {
        if(isset($arr_data) && sizeof($arr_data)>0)
        {
        	$this->NotificationsModel->create($arr_data);
        	return true;
        }
        return false;
    }
    /*********** update_notification status from unread to read **********/
    public function update_notification_status($notification_id)
    {
        $this->NotificationsModel->where('id',$notification_id)->update(['is_read'=>1]);
        return true;
    }
}