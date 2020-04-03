<?php

namespace Setrest\Storage;

use Illuminate\Support\Facades\Storage;

class Archive extends File
{
    /**
     * Сохраняет архив на сервере
     *
     * @param [type] $file
     * @return object
     */
    public function upload($file = null): object
    {
        if ($file) {
            parent::setInformation($file);
        }

        if ($this->file != null) {
            $this->isUploaded = Storage::disk($this->storageDisc)->put(parent::directoryChecking($this->directory), $this->file);
        }

        return $this;
    }
}