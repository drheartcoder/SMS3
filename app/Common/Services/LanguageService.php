<?php
namespace App\Common\Services;

use App\Models\LanguageModel;
class LanguageService
{
    public function get_all_language()
    {
        $arr_lang = array();
        $obj_res = LanguageModel::where('status','1')->get();
        if( $obj_res != FALSE)
        {
            $arr_lang = $obj_res->toArray();

        }
        return $arr_lang;
    }

    public function arrange_locale_wise(array $arr_data)
    {
        if(isset($arr_data) && sizeof($arr_data)>0)
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