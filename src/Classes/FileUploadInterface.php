<?php

namespace INeop\FileUpload\Classes;

interface FileUploadInterface
{

    /**
     * @param null $path   # Not real path just folder name
     * @param null $disk   # ['local', 'public', 's3', ...]
     */
    public function store($path, $disk);
}
