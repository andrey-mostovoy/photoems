<?php
namespace App\Image;

use Imagine\Imagick\Image;

/**
 * @author Andrey Mostovoy <stalk.4.me@gmail.com>
 */
class CustomImage extends Image {
    /**
     * {@inheritdoc}
     */
    public function effects() {
        return new CustomImagickEffects($this->imagick);
    }
}
