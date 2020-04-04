<?php

use modules\blog\Module;

/* @var $this yii\web\View */
/* @var bool $link */

$link = isset($link) ? $link : true;
$tags = Module::t('module', 'Tags');

$this->title = Module::t('module', 'Blog');
$this->params['breadcrumbs'][] = ['label' => Module::t('module', 'Blog'), 'url' => ['default/index']];
$this->params['breadcrumbs'][] = ($link === true) ? ['label' => $tags, 'url' => ['index']] : $tags;
