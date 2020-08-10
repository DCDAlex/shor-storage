<?php

namespace Setrest\Storage;

use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * 🔧 Реализует основную логику работы с файлами
 */
class File
{
    public $uploadPath = null;

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

    /**
     * Размер входного файла
     */
    public $size;

    public function __call($name, $arguments)
    {
        if ($name === "directoryPostfix") {
            return call_user_func_array([self::class, 'dirpostfix'], $arguments);
        }
    }

    public function __construct($file = null)
    {
        $this->driver = $this->driver();
        $this->directory = $this->directory();

        if ($file) {
            $this->setup($file);
        }
    }

    protected function setup(UploadedFile $file)
    {
        if ($file) {
            $this->file = $file;
            $this->preSetupHook($file);
            $this->setInformation();
        }

    }

    protected function driver(): string
    {
        return config('filesystems.default');
    }

    public function getDriver(): string
    {
        return $this->driver;
    }

    public function setDriver(string $driver): self
    {
        $this->driver = $driver;
        return $this;
    }

    protected function directory(): string
    {
        return '/';
    }

    public function getDirectory(): string
    {
        return $this->directory;
    }

    public function setDirectory(string $directory): self
    {
        $this->directory = $directory;
        return $this;
    }

    /**
     * Сохраняет файл на сервере
     *
     * @param [type] $file
     * @return object
     */
    public function upload(UploadedFile $file = null): object
    {
        if ($file) {
            $this->setup($file);
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
    public function dirpostfix(string $postfix): self
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
    protected function setInformation(): void
    {
        if ($this->file) {
            $this->fileExtension = $this->file->getClientOriginalExtension();
            $this->originalName = explode('.', $this->file->getClientOriginalName())[0];
            $this->size = $this->file->getSize();
        }
    }

    /**
     * Объявление файла
     *
     * @param use Illuminate\Http\UploadedFile $file
     * @return object
     */
    public function init(UploadedFile $file): self
    {
        $this->setup($file);
        return $this;
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
