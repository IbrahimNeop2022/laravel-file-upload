<?php


namespace INeop\FileUpload\Classes;

use Exception;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use INeop\FileUpload\Traits\CreateDirectory;
use INeop\FileUpload\Traits\ResizeImage;
use Intervention\Image\Facades\Image;

class FileUploadService implements FileUploadInterface
{

    use ResizeImage, CreateDirectory;

    /**
     * @var
     */
    private
        $file,
        $fileName,
        $filePath,
        $quality,
        $image;

    /**
     * FileUploadService constructor.
     * @param $file
     * @return FileUploadService
     */
    public function make($file)
    {
        $this->file = is_string($file) ? new File($file) : $file;

        if (Str::contains($file->getMimeType(), 'image')) {
            $this->image = Image::make($file);
        }

        $this->quality = config('file-upload.quality');

        return $this;
    }

    /**
     * @param string $path # Not real path just folder name
     * @param string $disk # ['local', 'public', 's3', ...]
     * @return string      # file name
     */
    public function store($path = '', $disk = '')
    {
        if ($this->image) {

            $this->resizeImage($this->image);

            return $this->storeAsImage($path, $this->getDisk($disk));
        }

        $this->fileName = $this->file->store($path, $this->getDisk($disk));

        // Create folder if not exists, or abort uploading
        if (!$this->createDirectoryIfNotExists($path, $this->getDisk($disk))) {
            return false;
        }

        $this->filePath = Storage::disk($this->getDisk($disk))->path($this->fileName);

        return $this->fileName;
    }

    /**
     * @param $path
     * @param $disk
     * @return bool
     */
    public function storeAsImage ($path, $disk)
    {
        $this->fileName = $this->file->hashName($path);

        // Create folder if not exists, or abort uploading
        if (!$this->createDirectoryIfNotExists($path, $disk)) {
            return false;
        }

        $this->filePath = Storage::disk($disk)->path($this->fileName);

        $this->image->save($this->filePath, $this->quality);

        return $this->fileName;
    }

    /**
     * @param $oldFile
     * @param null $disk
     * @return FileUploadService
     */
    public function delete($oldFile, $disk = null)
    {
        if ($oldFile && Storage::disk($this->getDisk($disk))->exists($oldFile)) {
            Storage::disk($this->getDisk($disk))->delete($oldFile);
        }

        return $this;
    }

    /**
     * @param $oldFile
     * @param null $disk
     * @return void
     */
    public static function deleteFile($oldFile, $disk = 'public')
    {
        if ($oldFile && Storage::disk($disk)->exists($oldFile)) {
            Storage::disk($disk)->delete($oldFile);
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
     * @return mixed
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * @param null $disk
     * @return string
     */
    public function getDisk($disk = null)
    {
        return $disk ?: 'public';
    }

    /**
     *  magic method to call all methods in Intervention\Image package
     * @param $name
     * @param $arguments
     * @return FileUploadService
     */
    public function __call($name, $arguments)
    {
        try{
            $this->image?->$name(...$arguments);
        }catch(Exception $e){

        }

        return $this;
    }

}
