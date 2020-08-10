<?php

namespace Setrest\Storage;

use Illuminate\Http\UploadedFile;
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

    protected $isGif = false;

    protected function directory(): string
    {
        return 'images';
    }

    /**
     * Загрузка изображения на сервер
     *
     * @return string|null
     */
    public function upload(UploadedFile $image = null): object
    {
        if ($image) {
            $this->setup($image);
        }

        if ($this->isGif) {
            parent::upload($image);
            return $this;
        }

        $this->file->encode($this->fileExtension);

        if ($this->file != null) {
            $path = parent::directoryChecking($this->directory) . parent::hashing($this->file) . '.' . $this->fileExtension;
            if (Storage::disk($this->driver)->put($path, $this->file)) {
                $this->uploadPath = $this->directory . $this->name . "." . $this->fileExtension;
            }
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
        return $this->upload($image);
    }

    /**
     * Изменение размеров изображения
     *
     * @param integer $weight Изменение по высоте
     * @param integer $height Изменение по ширине
     * @return self
     */
    public function resize(int $width, int $height): self
    {
        if ($this->isGif) {
            return $this;
        }

        $this->file->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        return $this;
    }

    protected function preSetupHook($image)
    {
        if ($image->getClientOriginalExtension() == 'gif') {
            $this->isGif = true;
        } else {
            $this->file = InterventionImage::make($image);
        }

    }

    protected function setInformation(): void
    {
        if ($this->isGif) {
            parent::setInformation();
        }
        if ($this->file && !$this->isGif) {
            $this->fileExtension = substr($this->file->mime, strpos($this->file->mime, "/") + 1);
        }
    }
}
