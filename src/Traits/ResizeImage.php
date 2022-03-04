<?php


namespace INeop\FileUpload\Traits;

use Intervention\Image\Image;

trait ResizeImage
{
    public function resizeImage(Image $image)
    {
        $maxWidth = config('file-upload.max-width');

        $maxHeight = config('file-upload.max-height');

        if ($image->width() > $maxWidth) {
            $image->resize($maxWidth, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }

        if ($image->height() > $maxHeight) {
            $image->resize(null, $maxHeight, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }
    }
}
