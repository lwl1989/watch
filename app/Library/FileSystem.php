<?php

namespace App\Library;


use Illuminate\Support\Facades\Storage;

class FileSystem
{
    /**
     * 獲取靜態文件地址
     * @param string $path
     * @return string
     */
    public static function getFileShowUrl(string $path)
    {
    	if(strpos($path,'/images/banner_')!==false){
    		return env('APP_URL').$path;
	    }
	    $path = ltrim($path, '/');
        $sfsHost = env('SFS_URL', null);
        if(empty($sfsHost)) {
            $url = request()->getSchemeAndHttpHost() . Storage::url($path);
        }else{
            $url = rtrim($sfsHost, '/'). Storage::url($path);
        }

        return $url;
    }
}