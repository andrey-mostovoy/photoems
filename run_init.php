<?php
/**
 * Запуск инициализации.
 * @author Andrey Mostovoy <stalk.4.me@gmail.com>
 */

use Core\TemplateBuilder\Nginx\NginxTemplateBuilder;

include_once 'boot.php';

// собирает конфиг для nginx
$NginxTemplateBuilder = new NginxTemplateBuilder();
$NginxTemplateBuilder->compile();
