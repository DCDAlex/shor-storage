<?php

namespace App\Http\Services\Storage;

use App\Api\Interfaces\Storage as InterfacesStorage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * 🔧 Абстрактный класс, реализует основную логику работы с файлами
 */
abstract class Core
{
    public $isUploaded;

    /**
     * Директория для загрузки файлов
     */
    public $directory;

    /**
     * Сгенерированное название файла
     */
    public $name;

    /**
     * Расширение загружаемого файла
     */
    public $fileType;

    /**
     * Исходный файл
     */
    public $file;

    public function __construct($file = null)
    {
        $this->file = $file;
        $this->isUploaded = false;

        if ($this->file) {
            $this->fileType = substr($file->mime, strpos($file->mime, "/") + 1);
        }
    }

    /**
     * Сохраняет файл на сервере
     *
     * @return self
     */
    public function upload(): self
    {
        if ($this->file != null) {
            $path = $this->directory . $this->hashing($this->file) . '.' . $this->fileType;
            $this->isUploaded = Storage::disk('customPublic')->put($path, $this->file);
        }

        return $this;
    }

    /**
     * Генерирует хэш для названия файла
     *
     * @param file $file файл 
     * 
     * @return string
     */
    protected function hashing($file): string
    {
        $now = Carbon::now()->toDateTimeString();
        return $this->name = md5($file->__toString() . rand() . $now);
    }

    /**
     * Метод дял добавления вложенных директорий 
     *
     * @param string $postfix
     * @return object
     */
    public function direcotryPostfix(string $postfix): object
    {
        $this->directory .= $postfix;
        return $this;
    }

    /**
     * Удаляет файл с сервера
     *
     * @param string $path относительный путь до файла
     * 
     * @return boolean
     */
    public function delete(string $path = null): bool
    {
        return Storage::disk('customPublic')->delete($path);
    }
}
