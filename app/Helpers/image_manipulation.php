<?php

/*
    Usage:  get_resized_image_path('d3f5be31e24a194366da81e5ad76a8328e1159df.jpeg','/uploads/cities/',250,260);
*/
 /***********  Resizing image ***************/   
function get_resized_image($image_file = FALSE,$dir=FALSE,$height=250,$width=250,$fallback_text="No Image Available")
{
    ini_set('memory_limit', '900M' );//THIS MEMORY LIMIT FOR RESOLOTION 10000 * 10000 Approx

    $CACHE_DIR             = 'resize_cache/';
    $CACHE_DIR_BASE_PATH   = base_path().'/uploads/'.$CACHE_DIR;
    $CACHE_DIR_PUBLIC_PATH = url('/').'/uploads/'.$CACHE_DIR;

    $real_dir = base_path().$dir;
    
    $extension  = get_extension($image_file);

    if($image_file == FALSE || $dir == FALSE)
    {
         return "https://placeholdit.imgix.net/~text?txtsize=33&txt=".$fallback_text."&w=".$width."&h=".$height;
    }

    /* Check if File Exists */
    if(!image_exists($real_dir.$image_file))
    {
        return "https://placeholdit.imgix.net/~text?txtsize=33&txt=".$fallback_text."&w=".$width."&h=".$height;
    }

    /* Check if Given file is image*/
    if(!is_valid_image($real_dir.$image_file))
    {   
        return "https://placeholdit.imgix.net/~text?txtsize=33&txt=No+Image&w=".$width."&h=".$height;
    }
    
    /* Generate Expected Resized Image Name */
    $expected_resize_image_name = generate_resized_image_name($image_file,$width,$height,$extension);

    if(!image_exists($CACHE_DIR_BASE_PATH.$expected_resize_image_name))
    {
        /* Create Cache Dir */
        $parent_dir =  dirname($real_dir.$expected_resize_image_name);
        @mkdir($CACHE_DIR_BASE_PATH,0777);
        $real_path   = $real_dir.$image_file;   
        $status = Image::make( $real_path )->resize( $width, $height )->save( $CACHE_DIR_BASE_PATH.$expected_resize_image_name );
    }
    return $CACHE_DIR_PUBLIC_PATH.$expected_resize_image_name;
}

/********** get image extension *************/
function get_extension($image_file)
{
    $arr_part = array();
    $arr_part = explode('.', $image_file);
    return end($arr_part);
}

/*********** check whether image is valid or not *************/
function is_valid_image($image_real_path)
{
   return @getimagesize($image_real_path);
}

/*********** check whether image exists in system *************/
function image_exists($image_real_path)
{
    if (!is_readable($image_real_path)) 
    {
        return FALSE;
    } 
    return TRUE;
}

/*************** generate resized image name ******************/
function generate_resized_image_name($file_name,$width,$height,$extension)
{
    return substr($file_name, 0, strrpos($file_name, '.')) . '-' . $width . 'x' . $height . '.' . $extension;
}

/**************** Imgage validation message ***********/
function image_validate_note($height,$width,$maxheight,$maxwidth)
{
    return '<span>'.translation('allowed_only_jpg_jpeg_png')."<br>".translation('please_upload_image_with_height_and_width_greater_than_or_equal_to').' '.$height.' X '.$width.' '.translation('and_less_than_or_equal_to').' '.$maxheight.' X '.$maxwidth.' '.translation('for_best_result').'</span>';
} 