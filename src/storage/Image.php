<?php

namespace App\Http\Services\Storage;

use Intervention\Image\Facades\Image as InterventionImage;

/**
 *  🔧 Класс Image релизует логику работы с изображениями
 *
 *  ❕  Использует intervation/image для редактирования исходного файла
 *
 */
class Image extends Core
{
    /**
     * Устанваливает директорию загрузки файлов
     */
    public $directory = /** upload/ **/"images/";

    public function __construct($file = null)
    {
        if ($file) {
            $file = InterventionImage::make($file);
        }

        parent::__construct($file);
    }

    /**
     * Загрузка изображения на сервер
     *
     * @return string|null
     */
    public function uploadImage(): ?string
    {
        $this->file->encode($this->fileType);
        $this->upload();

        return $this->directory . $this->name . "." . $this->fileType;
    }

    //todo
    public function updateImage()
    {

        return;
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

}
