<?php


namespace INeop\FileUpload\Classes;

use Exception;
use Illuminate\Database\Eloquent\Model;
use INeop\FileUpload\Traits\ResizeImage;


class FileUploadMediaService implements FileUploadInterface
{

    use ResizeImage;
    /**
     * @var
     */
    private
        $model,
        $media,
        $mediaCollection = [],
        $file,
        $fileName,
        $image,
        $files = [];

/*
 * FileService($file)->store()
 * */

    public function make($file)
    {
        if (is_iterable($file)) {
            foreach ($file as $item){
                $this->files[] = (new FileUploadService())->make($item);
            }
        }else{
            $this->file = (new FileUploadService())->make($file);
        }
        return $this;
    }

    /**
     * @param Model $model
     * @return $this
     */
//    public function setModel($model)
//    {
//        $this->model = $model;
//
//        return $this;
//    }

    public function setModel(Model $model)
    {
        $this->model = $model;

        if ($this->files) {
            $this->setMultiMedia();

            return $this;
        }

        $this->fileName = $this->file->store();

        $this->media = $this->model->addMedia($this->file->getFilePath());

        return $this;
    }

    protected function setMultiMedia()
    {
        foreach ($this->files as $file){
            $file->store();
            $this->mediaCollection[] = $this->model->addMedia($file->getFilePath());
        }
    }

    /**
     * @param string $collection
     * @param string $disk
     */
    public function store($collection = 'default', $disk = '')
    {
        if ($this->mediaCollection){
            $this->storeMany($collection, $disk);
            return;
        }
        $this->media->toMediaCollection($collection, $disk);
    }

    protected function storeMany($collection, $disk)
    {
        foreach ($this->mediaCollection as $media){
            $media->toMediaCollection($collection, $disk);
        }
    }



    /**
     * @return mixed
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @param $name
     * @param $arguments
     * @return $this
     */
    public function __call($name, $arguments)
    {
        try{
            if ($this->mediaCollection){
                foreach ($this->mediaCollection as $media) {
                    $media->$name(...$arguments);
                }
            }else{
                $this->media?->$name(...$arguments);
            }

        }catch(Exception $e){

        }

        try{
            if ($this->files){
                foreach ($this->files as $file) {
                    $file->$name(...$arguments);
                }
            }else{
                $this->file?->$name(...$arguments);
            }
        }catch(Exception $e){

        }

        return $this;
    }

}
