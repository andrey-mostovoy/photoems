<?php
namespace Core\File;

/**
 * Класс для чтения уже загруженных файлов.
 * @author Andrey Mostovoy <stalk.4.me@gmail.com>
 */
class UploadFileReader {
    /**
     * @var string Имя файла.
     */
    private $fileName;

    /**
     * @var string Вложенная директория сохранения файла.
     */
    private $dir;

    /**
     * Устанавливает имя файла.
     * @param string $name
     * @return $this
     */
    public function setName($name) {
        $this->fileName = $name;
        return $this;
    }

    /**
     * Устанавливает вложенную директорию сохранения файла.
     * @param string $dir
     * @return $this
     */
    public function setDir($dir) {
        $this->dir = $dir;
        return $this;
    }

    /**
     * Возвращает веб урл для загруженного файла.
     * @return string
     */
    public function getWebPath() {
        return UPLOAD_PATH . ($this->dir ? ($this->dir . '/') : '') . $this->fileName;
    }
}
