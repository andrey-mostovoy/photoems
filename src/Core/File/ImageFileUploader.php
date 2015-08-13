<?php
namespace Core\File;

/**
 * Класс для работы с загружаемыми изображениями.
 * @author Andrey Mostovoy <stalk.4.me@gmail.com>
 */
class ImageFileUploader extends FileUploader {
    /**
     * @var array Список доступных типов изображений по умолчанию. Используются IMAGETYPE_XXX.
     */
    private $validTypes = array(
        IMAGETYPE_GIF,
        IMAGETYPE_JPEG,
        IMAGETYPE_PNG,
    );

    /**
     * @inheritdoc
     */
    public function __construct($fileHolderKey) {
        parent::__construct($fileHolderKey);
        $this->addFileUploadDirectory(FileManager::IMAGE_DIR);
    }

    /**
     * @inheritdoc
     */
    protected function isRewriteAllowed() {
        return false;
    }

    /**
     * Устанавливает доступные типы изображений.
     * @param array $validTypes Список из IMAGETYPE_XXX.
     */
    public function setValidTypes(array $validTypes) {
        $this->validTypes = $validTypes;
    }

    /**
     * @inheritdoc
     */
    protected function checkUploadedFile() {
        $imageInfo = getimagesize($this->uploadFileInfo['tmp_name']);

        if (!$imageInfo) {
            return false;
        }

        if (!in_array($imageInfo[2], $this->validTypes)) {
            return false;
        }

        return true;
    }
}
