<?php


namespace INeop\FileUpload\Facades;


use Illuminate\Support\Facades\Facade;

class MediaUpload extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'mediaUpload';
    }

}
