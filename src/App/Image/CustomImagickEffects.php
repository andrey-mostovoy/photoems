<?php
namespace App\Image;

use Imagick;
use Imagine\Imagick\Effects;

/**
 * Эффекты применяемые к изображению.
 * @author Andrey Mostovoy <stalk.4.me@gmail.com>
 */
class CustomImagickEffects extends Effects {
    private $Imagick;

    /**
     * @param Imagick $Imagick
     */
    public function __construct(Imagick $Imagick) {
        $this->Imagick = $Imagick;
        parent::__construct($Imagick);
    }

    public function test() {

    }
}
