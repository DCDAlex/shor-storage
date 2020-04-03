<?php

namespace Setrest\Storage;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image as InterventionImage;

/**
 *  🔧 Класс Image релизует логику работы с изображениями
 *
 *  ❕  Использует intervation/image для редактирования исходного файла
 *
 */
class Image extends File
{
    /**
     * Устанваливает директорию загрузки файлов
     */
    public $directory = /** upload/ **/"images"/** / */;

    public function __construct($file = null)
    {
        if ($file) {
            $file = InterventionImage::make($file);
            parent::__construct($file);
        }
    }

    /**
     * Загрузка изображения на сервер
     *
     * @return string|null
     */
    public function uploadImage($image = null): object
    {

        if ($image) {
            $this->__construct($image);
        }

        $this->file->encode($this->fileType);

        if ($this->file != null) {
            $path = parent::directoryChecking($this->directory) . parent::hashing($this->file) . '.' . $this->fileType;
            $this->uploadPath = Storage::disk($this->storageDisc)->put($path, $this->file);
        }

        return $this;
    }

    /**
     * Процесс удаления старого файла и загрзуи нового
     *
     * @param string $path Путь до файла который будет удален
     * @return void
     */
    public function updateImage(string $path, $image = null): object
    {
        $this->delete($path);
        return $this->uploadImage($image);
    }

    /**
     * Изменение размеров изображения
     *
     * @param integer $weight Изменение по высоте
     * @param integer $height Изменение по ширине
     * @return self
     */
    public function resize(int $weight, int $height): object
    {
        $this->file->resize($weight, $height, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        return $this;
    }

    /**
     * Объявление файла 
     *
     * @param [type] $file
     * @return object
     */
    public function init($image): object
    {
        $this->__construct($image);
        return $this;
    }
}
