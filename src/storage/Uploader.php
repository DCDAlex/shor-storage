<?php

namespace Setrest\Storage;

use Illuminate\Http\UploadedFile;

class Uploader
{
    protected static $imageExtension = [
        'jpg',
        'png',
        'bmp',
        'tif',
        'gif',
    ];

    public static function image(UploadedFile $file): ?Image
    {
        return static::file($file);
    }

    public static function file(UploadedFile $file): ?File
    {
        if (!$file) {
            return null;
        }

        $concreteUploader = static::detect($file);
        $concreteUploader->init($file);
        return $concreteUploader;
    }

    public static function upload(UploadedFile $file): ?File
    {
        if (!$file) {
            return null;
        }

        $concreteUploader = static::detect($file);
        $concreteUploader->upload($file);
        return $concreteUploader;
    }

    protected static function detect(UploadedFile $file)
    {
        $extension = $file->getClientOriginalExtension();
        if (in_array(strtolower($extension), static::$imageExtension)) {
            return new Image;
        } else {
            return new File;
        }
    }
}
