<?php
namespace Core\File;

/**
 * Класс-менеджер работы с файлами.
 * @author Andrey Mostovoy <stalk.4.me@gmail.com>
 */
class FileManager {
    /**
     * Директория хранения изображений.
     */
    const IMAGE_DIR = 'img';

    /**
     * Возвращает веб урл для загруженного изображения.
     * @param string $name
     * @return string
     */
    public static function readUploadedImage($name) {
        $Reader = new UploadFileReader();
        $Reader
            ->setDir(self::IMAGE_DIR)
            ->setName($name)
        ;

        return $Reader->getWebPath();
    }
}
