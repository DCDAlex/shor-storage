<?php

namespace Setrest\Storage;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Storage;

/**
 * 🔧 Абстрактный класс, реализует основную логику работы с файлами
 */
class File
{
    public $uploadPath;

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
        $this->uploadPath = null;
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
            $uploadPath = Storage::disk($this->storageDisc)->put($this->directoryChecking($this->directory), $this->file);
            $this->uploadPath = preg_replace('/(\/){2,}/', '$1', $uploadPath);
        }

        return $this;
    }

    /**
     * Метод дял добавления вложенных директорий
     *
     * @param string $postfix
     * @return object
     */
    public function directoryPostfix(string $postfix): object
    {
        if (substr($postfix, 0) != '/') {
            $postfix = '/' . $postfix;
        }
        
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
     * Получение пути только что созданного иозображения
     * относительно директории указанной в конфиге файловой системы
     *
     * @return string
     */
    public function path(): ?string
    {
        if ($this->uploadPath) {
            return $this->uploadPath;
        }

        return null;
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
     * Сбор информации о файле
     *
     * @param [type] $file
     * @return void
     */
    protected function setInformation($file): void
    {
        $this->file = $file;

        if ($this->file) {
            try {
                $this->fileType = substr($file->mime, strpos($file->mime, "/") + 1);
            } catch (Exception $e) {
                $this->fileType = substr($file->getClientOriginalName(), strpos($file->getClientOriginalName(), '.') + 1);
            }
        }
    }

    protected function directoryChecking(string $directory): string
    {
        $directory = preg_replace('/(\/){2,}/', '$1', $directory);
        if (substr($directory, -1) != '/') {
            $directory .= '/';
        }

        return $this->directory = $directory;
    }
}
