<?php
namespace App\Image;

use Exception;
use Imagine\Exception\RuntimeException;
use Imagine\Imagick\Imagine;

/**
 * @author Andrey Mostovoy <stalk.4.me@gmail.com>
 */
class CustomImagine extends Imagine {
    /**
     * {@inheritdoc}
     */
    public function open($path) {
        $path = $this->checkPath($path);

        try {
            $imagick = new \Imagick($path);
            $image = new CustomImage($imagick, $this->createPalette($imagick), $this->getMetadataReader()->readFile($path));
        } catch (Exception $e) {
            throw new RuntimeException(sprintf('Unable to open image %s', $path), $e->getCode(), $e);
        }

        return $image;
    }
}
