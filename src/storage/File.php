<?php

namespace Setrest\Storage;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * 🔧 Реализует основную логику работы с файлами
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
     * Оригинальное название файла
     */
    public $originalName;

    /**
     * Расширение загружаемого файла
     */
    public $fileExtension;

    /**
     * Название laravel диска для загрузки
     */
    public $driver;

    /**
     * Исходный файл
     */
    public $file;

    public function __construct($file = null)
    {
        $this->uploadPath = null;
        $this->driver = $this->driver ?? config('filesystems.default');

        if ($file) {
            $this->setInformation($file);
        }
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
            $uploadPath = Storage::disk($this->driver)->put($this->directoryChecking($this->directory), $this->file);
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
        return Storage::disk($this->driver)->delete($path);
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

    public function getDriver(): string
    {
        return $this->driver;
    }

    public function setDriver(string $driver): object
    {
        $this->driver = $driver;
        return $this;
    }

    public function getDirecory(): string
    {
        return $this->directory;
    }

    public function setDirecory(string $directory): object
    {
        $this->directory = $directory;
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
     * Сбор информации о файле
     *
     * @param [type] $file
     * @return void
     */
    protected function setInformation($file): void
    {
        $this->file = $file;

        if ($this->file) {
            $this->fileExtension = $file->getClientOriginalExtension();
            $this->originalName = explode('.', $file->getClientOriginalName())[0];
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
