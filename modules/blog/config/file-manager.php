<?php

use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use dominus77\tinymce\components\MihaildevElFinder;
use modules\blog\Module;

$config = [
    'class' => MihaildevElFinder::class,
    'controller' => Url::to('blog/elfinder'),
    'filter' => 'image',
    'title' => Module::t('module', 'My File Manager'),
    'width' => 900,
    'height' => 600,
    'resizable' => 'yes',
];

$anons = ArrayHelper::merge($config, []);
$content = ArrayHelper::merge($config, []);

return [
    'anons' => $anons,
    'content' => $content
];
