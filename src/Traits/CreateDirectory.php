<?php


namespace INeop\FileUpload\Traits;

use Exception;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Image;

trait CreateDirectory
{

    /**
     * Check and create directory if not exists.
     *
     * @access protected
     *
     * @param string $folder
     *
     * @param $disk
     * @return bool
     */
    public function createDirectoryIfNotExists($folder, $disk): bool
    {
        // Check if dri exists
        if (Storage::disk($disk)->exists($folder)) {
            return true;
        }

        // Create new dir
        try {
            Storage::disk($disk)->makeDirectory($folder);
            return true;
        } catch (Exception $e) {
            return $e;
        }
    }

}
