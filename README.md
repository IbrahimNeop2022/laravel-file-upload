
# File Upload Service

#### Installation

```bach
composer require i-neop/laravel-file-upload
```

open your Laravel config file config/app.php and add the following lines.

In the _$providers_ array add the service providers for this package.

```
Intervention\Image\ImageServiceProvider::class
```

Add the facade of this package to the _$aliases_ array.

```
'Image' => Intervention\Image\Facades\Image::class
```

publish media library migrations

```
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="migrations"
php artisan migrate
```

publish config 

```
php artisan vendor:publish --provider="INeop\FileUpload\Providers\FileUploadServiceProvider" --tag="file-upload-config"
```

1. [Intervention Image documentation](https://image.intervention.io/v2).
2. [Laravel-media library documentation](https://spatie.be/docs/laravel-medialibrary).

### 1- FileUploadService class
*this class store file and return file name to store it in your model*.
* it provide hash name for file
* it provide auto resize image 
* it provide all methods in intervention image package.
* it provide config file to set max width, max height and quality.

Here are a few short examples of what you can do:

```php
$post = new Post();
//...
$post->image = FileUpload::make(request('image'))->store();
$post->save();
```
You can add path and disk, by default disk is public.
```php
$post = new Post();
//...
$post->image = FileUpload::make(request('image'))->store('posts', 's3');
$post->save();
```
You can use ***all methods in intervention image package***.

```php
$post = new Post();
//...
$post->image = FileUpload::make(request('image'))
                    ->resize(400, 400)
                    ->crop(100, 100, 25, 25)
                    ->store('posts', 's3');
$post->save();
```

You can use ***delete old file***, for example in update.
```php
$post = Post::find(1);
//...
$post->image = FileUpload::make(request('image'))
                    ->delete($post->image)
                    ->store('posts');
$post->save();
```
You can get path.
```php
$post = new Post();
//...
$fileUpload = FileUpload::make(request('image')); 
$post->image = $fileUpload->store('posts');
$filePath = $fileUpload->getFilePath();

$post->save();
```
****Note:**** you can clean code by Accessors & Mutators
```php
$post = Post::create([
        //...
]);

//In Post Model
public function setImageAttribute($image)
{
    $this->attributes['image'] = FileUpload::make($image)->store('posts');
}
``` 
To get image.
```php
//In Post Model
public function getImgAttribute()
{
    return $this->image ? asset('storage/'. $this->image) : asset('images/post.jpg');
}
``` 
***Dont forget*** run this command.
```bach
php artisan storage:link
```
****

### 2- MediaUploadService class
*this class use media-library package to store media files for your model*.
* it provide all methods in intervention image package.
* it provide all methods in media-library package.
* it provide upload multiple files.
* it provide config file to set max width, max height and quality.

To associate media with a model, the model must implement the following interface and trait:

```php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
class YourModel extends Model implements HasMedia
{
    use InteractsWithMedia;
}
```

Here are a few short examples of what you can do:
```php
$post = new Post();
//...
$post->save();
MediUpload::make(request('image'))->setModel($post)->store();

```
You can add collection and disk, by default disk is public.
```php
$post = new Post();
//...
$post->save();
MediUpload::make(request('image'))->setModel($post)->store('image', 's3');

```
You can upload multiple files.
```php
$post = new Post();
//...
$post->save();

MediUpload::make(request('files'))->setModel($post)->store('images');

```
You can use ***all methods in intervention image package***.
```php
$post = new Post();
//...
$post->save();
MediUpload::make(request('image'))
    ->resize(500, 200)
    ->crop(100, 100, 25, 25)
    ->setModel($post)
    ->store('image', 's3');
```
You can use ***all methods in media library package***.
```php
$post = new Post();
//...
$post->save();
MediUpload::make(request('image')) 
    ->resize(500, 200)
    ->setModel($post)
    ->usingName('my-image-name')
    ->withCustomProperties([
        'primaryColor' => 'red',
        'image-code'  => '12458558',
    ])
    ->store('image');
```
To retrieve files you can use the getMedia-method:

```php
    $mediaItems = $yourModel->getMedia();
```

the getFirstMedia and getFirstMediaUrl convenience-methods are also provided:

```php
    $media = $yourModel->getFirstMedia();
    $url = $yourModel->getFirstMediaUrl();
```

If you want to remove all associated media in a specific collection you can use the clearMediaCollection method. It also accepts the collection name as an optional parameter:

```php
    $yourModel->clearMediaCollection(); // all media will be deleted
    $yourModel->clearMediaCollection('images'); // all media in the images collection will be deleted
```

***recommend:*** read [Laravel-media library documentation](https://spatie.be/docs/laravel-medialibrary).
