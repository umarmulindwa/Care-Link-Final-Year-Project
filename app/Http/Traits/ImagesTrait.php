<?php 

namespace App\Http\Traits;

trait ImagesTrait
{
    static function storeImage($photo, $path, $suffix = 'user')
    {
        $ext = $photo->extension();
        $photoImageName = $suffix . '_' . self::generateUUID() . '.' . $ext;
        $photPath = $photo->storeAs($path, $photoImageName, 'public');
        return $photPath;
    }

    static function deleteImage($path)
    {
        $imagePath = 'storage/' . $path;
        if(File::exists($imagePath)) {
            File::delete($imagePath);
        }
    }

    static function generateUUID()
    {
        return implode('-', str_split(substr(strtolower(md5(microtime().rand(1000, 9999))), 0, 30), 6));
    }
}
