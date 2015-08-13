<?php
namespace App\Image;

/**
 * Класс описания изображений приложения.
 * @author Andrey Mostovoy <stalk.4.me@gmail.com>
 */
class Image {
    const EXTENSION = '.jpg';

    public static function getName($id) {
        return $id . self::EXTENSION;
    }

    public static function getPreviewName($id) {
        return $id . '_preview' . self::EXTENSION;
    }

    public static function getWorkName($id) {
        return $id . '_work' . self::EXTENSION;
    }

    public static function getOriginalFile($id) {
        return IMG_DOWNLOAD . self::getName($id);
    }

    public static function getPreviewFile($id) {
        return IMG_DOWNLOAD . self::getPreviewName($id);
    }

    public static function getWorkFile($id) {
        return IMG_DOWNLOAD . self::getWorkName($id);
    }

    public static function getPreviewWebPath($id) {
        return IMAGE_DOWNLOAD_PATH . self::getPreviewName($id);
    }

    public static function getWorkWebPath($id) {
        return IMAGE_DOWNLOAD_PATH . self::getWorkName($id);
    }
}
