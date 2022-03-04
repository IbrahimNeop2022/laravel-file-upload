<?php

namespace INeop\FileUpload\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;
use INeop\FileUpload\Classes\FileUploadMediaService;
use INeop\FileUpload\Classes\FileUploadService;

class FileUploadServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        App::bind('mediaUpload', function() {
            return new FileUploadMediaService();
        });

        App::bind('fileUpload', function() {
            return new FileUploadService();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/file-upload.php' => config_path('file-upload.php'),
        ], 'file-upload-config');
    }
}
