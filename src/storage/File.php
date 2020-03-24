<?php

namespace Setrest\Storage;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * 🔧 Абстрактный класс, реализует основную логику работы с файлами
 */
class File
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
     * Название laravel диска для загрузки
     */
    public $storageDisc;

    /**
     * Исходный файл
     */
    public $file;

    public function __construct($file = null)
    {
        $this->isUploaded = false;
        $this->storageDisc = 'customPublic';
        
        $this->setInformation($file);
    }

    /**
     * Сохраняет файл на сервере
     *
     * @param [type] $file
     * @return object
     */
    public function upload($file = null): object
    {
        if ($file) {
            $this->setInformation($file);
        }

        if ($this->file != null) {
            $path = $this->directory . $this->hashing($this->file) . '.' . $this->fileType;
            $this->isUploaded = Storage::disk($this->storageDisc)->put($path, $this->file);
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
    public function directoryPostfix(string $postfix): object
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
        return Storage::disk($this->storageDisc)->delete($path);
    }

    /**
     * Сбор информации о файле
     *
     * @param [type] $file
     * @return void
     */
    private function setInformation($file): void
    {
        $this->file = $file;
        
        if ($this->file) {
            $this->fileType = substr($file->mime, strpos($file->mime, "/") + 1);
        }
    }
}
