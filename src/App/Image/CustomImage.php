<?php
namespace App\Image;

use Imagine\Imagick\Image as ImagickImage;

/**
 * @author Andrey Mostovoy <stalk.4.me@gmail.com>
 */
class CustomImage extends ImagickImage {
    /**
     * {@inheritdoc}
     */
    public function effects() {
        return new CustomImagickEffects($this->imagick);
    }
}
