<?php
namespace App\MainLanding;

use App\Image\CustomImage;
use App\Image\CustomImagine;
use App\Image\Image;
use App\Image\Size;
use Core\Page\AbstractWebPage;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\Point;

/**
 * Класс описания главной страницы.
 * @author Andrey Mostovoy <stalk.4.me@gmail.com>
 */
class MainLandingPage extends AbstractWebPage {
    /**
     * {@inheritdoc}
     */
    public function run() {
        if (isset($_FILES['upl']) && $_FILES['upl']['error'] == 0) {
            $this->handleSaveImage();
        }

        if (isset($_POST['apply'])) {
            $this->handleApplyImageEffect();
        }

        if (isset($_GET['change'])) {
            $this->handleChangeImage();
        }

        if (isset($_GET['download'])) {
            $this->handleDownloadImage();
        }

        if (isset($_GET['id'])) {
            $image = $_GET['id'];
            $this->bind('workUrl', Image::getPreviewWebPath($image));
            $this->addToJs('image', $image);
            $this->bind('effects', [
                'origin'    => 'origin',
                'negative'  => 'negative',
                'sharpen'   => 'sharpen',
                'grayscale' => 'grayscale',
                'blur'      => 'blur',
                'colorize'  => 'colorize',
                'gamma'     => 'gamma',
            ]);
        }

        return true;
    }

    /**
     * Обработаем создание нового изображения.
     */
    private function handleSaveImage() {
        header('Content-Type: application/json; charset=utf8');

        // A list of permitted file extensions
        $allowed = ['png', 'jpg', 'jpeg'];

        $extension = pathinfo($_FILES['upl']['name'], PATHINFO_EXTENSION);

        if (!in_array(strtolower($extension), $allowed)) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'wrong extension',
            ]);
            exit();
        }

        list($width, $height, $type, $attr) = getimagesize($_FILES['upl']['tmp_name']);

        if ($width < Size::PREVIEW_MIN_WIDTH) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'too small size',
            ]);
            exit();
        }

        $id = uniqid();
        $fileName = Image::getName($id);
        $previewFileName = Image::getPreviewName($id);
        $workFileName = Image::getWorkName($id);
        $originFilePath = IMG_DOWNLOAD . $fileName;
        if (move_uploaded_file($_FILES['upl']['tmp_name'], $originFilePath)) {
            $Imagine = new CustomImagine();
            $Image = $Imagine->open($originFilePath)->copy();

            $coefficient = $Image->getSize()->getWidth() / Size::PREVIEW_MIN_WIDTH;
            $previewHeight = $Image->getSize()->getHeight() / $coefficient;

            $Image->resize(new Box(Size::PREVIEW_MIN_WIDTH, $previewHeight))
                ->save(IMG_DOWNLOAD . $previewFileName)
                ->save(IMG_DOWNLOAD . $workFileName);

            echo json_encode([
                'status' => 'success',
                'id'     => $id,
            ]);
            exit();
        }

        echo json_encode([
            'status'  => 'error',
            'message' => 'Unknown error',
        ]);
        exit();
    }

    /**
     * Возвращает пути к оригинальному файлу, файлу превью.
     * @param string $id
     * @return array
     */
    private function getImagePath($id = null) {
        if (!$id) {
            $id = $_GET['id'];
        }
        return [
            IMG_DOWNLOAD . Image::getName($id),
            IMG_DOWNLOAD . Image::getPreviewName($id),
        ];
    }

    /**
     * Обработка смены фотографии.
     */
    private function handleChangeImage() {
        if (!isset($_GET['id'])) {
            return;
        }
        $id = $_GET['id'];

        @unlink(Image::getOriginalFile($id));
        @unlink(Image::getPreviewFile($id));
        @unlink(Image::getWorkFile($id));

        header('Location: http://' . $_SERVER['HTTP_HOST']);
        exit();
    }

    /**
     * Отдаем юзеру готовое изображение.
     */
    private function handleDownloadImage() {
        $id = $_GET['download'];
        $effect = $_GET['effect'];
        $additional = $_GET['additional'];

        $imagePath = Image::getOriginalFile($id);
        $previewPath = Image::getPreviewFile($id);
        $workPath = Image::getWorkFile($id);

        $Imagine = new CustomImagine();
        $Image = $Imagine->open($imagePath);

        $this->applyEffect($Image, $effect, $additional);

        $Image->save(Image::getOriginalFile($id));

        header('Cache-Control: public');
        header('Content-Description: File Transfer');
        header('Content-Disposition: attachment; filename=' . basename($imagePath));
        header('Content-Type: ' . mime_content_type($imagePath));
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($imagePath));

        readfile($imagePath);

        @unlink($imagePath);
        @unlink($previewPath);
        @unlink($workPath);

        exit();
    }

    /**
     * Применяет указанный фильтр к изображению.
     */
    private function handleApplyImageEffect() {
        header('Content-Type: application/json; charset=utf8');

        $image = $_POST['img'];
        $effect = $_POST['effect'];

        $previewPath = Image::getPreviewFile($image);

        $Imagine = new CustomImagine();
        $Image = $Imagine->open($previewPath);

        if (!$this->applyEffect($Image, $effect, $_POST['additional'])) {
            echo json_encode([
                'error' => 'Unknown filter effect',
            ]);
            exit();
        }

        $Image->save(Image::getWorkFile($image));

        echo json_encode([
            'url' => Image::getWorkWebPath($image) . '?r=' . rand(1, 119),
        ]);
        exit();
    }

    /**
     * Применяет эффект к изображению.
     * @param ImageInterface $Image
     * @param string $effect
     * @param string $additional
     * @return bool
     */
    private function applyEffect(ImageInterface $Image, $effect, $additional = null) {
        switch ($effect) {
            case 'origin':
                break;
            case 'negative':
                $Image->effects()->negative();
                break;
            case 'grayscale':
                $Image->effects()->grayscale();
                break;
            case 'sharpen':
                $Image->effects()->sharpen();
                break;
            case 'colorize':
                $Image->effects()->colorize($Image->palette()->color($additional));
                break;
            case 'gamma':
                $Image->effects()->gamma($additional);
                break;
            case 'blur':
                $Image->effects()->blur($additional);
                break;
            default:
                return false;
        }

        return true;
    }
}
