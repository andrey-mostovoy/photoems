<?php
// charsets
use App\Image\Size;
use stalk\Config\Config;

define('DEFAULT_LANG', 'ru_RU');
define('DEFAULT_CHARSET', 'UTF-8');
define('DEFAULT_LOCALE', 'ru_RU.UTF-8');
mb_internal_encoding(DEFAULT_CHARSET);
ini_set('intl.default_locale', DEFAULT_LOCALE);

// common const
define('FILE_EXT', '.php');
define('TPL_FILE_EXT', '.phtml');
define('PLATFORM_DEV', 'dev');
define('PLATFORM_PROD', 'prod');

// USER есть в дев окружении
if (!defined('USER') && isset($_ENV['USER'])) {
    define('USER', $_ENV['USER']);
}

if (!defined('MESOS_HOST') && isset($_SERVER['HOST'])) {
    define('MESOS_HOST', $_SERVER['HOST']);
} else {
    define('MESOS_HOST', null);
}

if (isset($_ENV['APPLICATION_ROOT'])) {
    define('APPLICATION_ROOT', $_ENV['APPLICATION_ROOT']);
}

if (!defined('APPLICATION_SERVER') && isset($_SERVER['APPLICATION_SERVER'])) {
    define('APPLICATION_SERVER', $_SERVER['APPLICATION_SERVER']);
} else {
    define('APPLICATION_SERVER', trim(`hostname`));
}

if (!defined('APPLICATION_PROJECT') && isset($_ENV['APPLICATION_PROJECT'])) {
    define('APPLICATION_PROJECT', $_ENV['APPLICATION_PROJECT']);
}

if (!defined('APPLICATION_REVISION') && isset($_ENV['APPLICATION_REVISION'])) {
    define('APPLICATION_REVISION', $_ENV['APPLICATION_REVISION']);
}

if (!defined('APPLICATION_PLATFORM') && isset($_ENV['APPLICATION_PLATFORM'])) {
    define('APPLICATION_PLATFORM', $_ENV['APPLICATION_PLATFORM']);
}

// paths
define('ROOT_DIR', __DIR__ . '/');
define('CONFIG_DIR', ROOT_DIR . 'config/');
define('SRC_DIR', ROOT_DIR . 'src/');
define('CORE_DIR', SRC_DIR . 'Core/');
define('APP_DIR', SRC_DIR . 'App/');
define('LOG_DIR', ROOT_DIR . 'log/');
define('TPL_DIR', ROOT_DIR . 'tpl/');
define('VENDOR_DIR', ROOT_DIR . 'vendor/');
define('TEMPLATE_BUILDER_DIR', TPL_DIR . 'template/');
define('LAYOUT_DIR', TPL_DIR . 'layout/');
define('HEADER_TPL_DIR', TPL_DIR . 'header/');
define('PAGE_TPL_DIR', TPL_DIR . 'page/');
define('STATIC_DIR', ROOT_DIR . 'static/');
define('JS_DIR', STATIC_DIR . 'js/');
define('CSS_DIR', STATIC_DIR . 'css/');
define('IMAGE_DIR', STATIC_DIR . 'img/');
define('FONT_DIR', STATIC_DIR . 'font/');
define('HTML_DIR', STATIC_DIR . 'html/');
define('UPLOAD_DIR', ROOT_DIR . '../uploads/');
define('LIB_DIR', ROOT_DIR . 'lib/');
define('PEAR_DIR', LIB_DIR . 'Pear/');

define('IMG_DOWNLOAD', IMAGE_DIR . 'download/');

/**
 * Автозагрузчики подмодулей
 */
define('CLASS_COMPOSER_AUTOLOADER', VENDOR_DIR . 'autoload.php');

if (!isset($_ENV['SHELL_MODE']) || $_ENV['SHELL_MODE'] != 'script') {
    //web path
    define('WEB_PATH', '//' . $_SERVER['SERVER_NAME'] . '/');
    define('STATIC_PATH', WEB_PATH . 'static/');
    define('IMAGE_PATH', STATIC_PATH . 'img/');
    define('JS_PATH', STATIC_PATH . 'js/');
    define('JS_CORE_PATH', JS_PATH . 'core/');
    define('JS_APP_PATH', JS_PATH . 'app/');
    define('CSS_PATH', STATIC_PATH . 'css/');
    define('UPLOAD_PATH', '//' . $_SERVER['SERVER_NAME'] . '/uploads/');
}

// Регистрация загрузки классов проекта.
spl_autoload_register(function($className) {
    try {
        include_once (SRC_DIR . str_replace('\\', '/', $className) . FILE_EXT);
    } catch (Exception $Ex) {}
});

// Регистрация автозагрузчиков внешних библиотек.
require CLASS_COMPOSER_AUTOLOADER;


// определение путей конфига
Config::getInstance()->setConfigDir(CONFIG_DIR . APPLICATION_PLATFORM . '/');


if (!isset($_ENV['SHELL_MODE']) || $_ENV['SHELL_MODE'] != 'script') {
    //web path
    define('IMAGE_DOWNLOAD_PATH', IMAGE_PATH . 'download/');
}

session_start();
