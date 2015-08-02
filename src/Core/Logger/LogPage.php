<?php
namespace Core\Logger;

use Core\Page\AbstractWebPage;

/**
 * Страница для просмотра логов
 * @author Andrey Mostovoy <stalk.4.me@gmail.com>
 */
class LogPage extends AbstractWebPage {
    const FILE_EXT = '.log';

    public function run() {
        $fileName = $this->getArg('file');
        $fileName = LOG_DIR . $fileName . self::FILE_EXT;

        if (!file_exists($fileName)) {
            echo 'no log file!';
        } else {
            echo '<pre>';
            echo file_get_contents($fileName, null, null, filesize($fileName)-32768);
            echo '</pre>';
        }

        return false;
    }
}
