<?php
namespace Core\File;

use Core\File\Exception\FileUploaderException;

/**
 * Класс для работы с загружаемыми файлами.
 * @author Andrey Mostovoy <stalk.4.me@gmail.com>
 */
class FileUploader {
    /**
     * @var string Ключ, в котором находится информация по загружнному файлу.
     */
    private $fileHolderKey;

    /**
     * @var string Директория для загруженных файлов.
     */
    private $uploadDir;

    /**
     * @var array Информация про загруженный файл.
     */
    protected $uploadFileInfo = array();

    /**
     * @var string Имя загруженного файла.
     */
    protected $fileName;

    /**
     * Конструктор.
     * @param string $fileHolderKey Ключ, в котором находится информация по загружнному файлу.
     */
    public function __construct($fileHolderKey) {
        $this->fileHolderKey = $fileHolderKey;
        $this->setFileUploadDirectory(UPLOAD_DIR);

        if (isset($_FILES[$this->fileHolderKey])) {
            $this->uploadFileInfo = $_FILES[$this->fileHolderKey];
        }
    }

    /**
     * Устанавливает директорию для сохранения загруженных файлов.
     * @param string $dir
     */
    public function setFileUploadDirectory($dir) {
        $this->uploadDir = $dir;

        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    /**
     * Добавляет директорию для сохранения загруженных файлов к уже существующему пути.
     * @param string $dir
     */
    public function addFileUploadDirectory($dir) {
        $this->setFileUploadDirectory($this->uploadDir . $dir . '/');
    }

    /**
     * Сохраняет загруженный файл.
     * @param string $newName
     * @return bool
     * @throws FileUploaderException
     */
    public function saveUploadedFile($newName = '') {
        if (!$this->uploadFileInfo || !is_uploaded_file($this->uploadFileInfo['tmp_name'])) {
            throw new FileUploaderException('File in ' . $this->fileHolderKey . ' is not uploaded!');
        }

        $this->fileName = $newName ? $newName : basename($this->uploadFileInfo["name"]);
        $filePath = $this->uploadDir . $this->fileName;

        if (!$this->isRewriteAllowed() && file_exists($filePath)) {
            throw new FileUploaderException('File ' . $filePath . ' has exists already!');
        }

        if (!$this->checkUploadedFile()) {
            throw new FileUploaderException('File in ' . $this->fileHolderKey . ' does not pass check!');
        }

        return move_uploaded_file($this->uploadFileInfo['tmp_name'], $filePath);
    }

    /**
     * Возращает признает перезаписи существующих файлов.
     * @return bool
     */
    protected function isRewriteAllowed() {
        return true;
    }

    /**
     * Возвращает результат проверки загруженного файла.
     * @return bool
     */
    protected function checkUploadedFile() {
        return true;
    }

    /**
     * Возвращает имя загруженного файла.
     * @return string
     */
    public function getFileName() {
        return $this->fileName;
    }
}
