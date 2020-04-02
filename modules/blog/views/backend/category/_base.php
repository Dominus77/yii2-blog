<?php

use modules\blog\Module;

/* @var $this yii\web\View */
/* @var bool $link */

$link = isset($link) ? $link : true;
$categories = Module::t('module', 'Categories');

$this->title = Module::t('module', 'Blog');
$this->params['breadcrumbs'][] = ['label' => Module::t('module', 'Blog'), 'url' => ['default/index']];
$this->params['breadcrumbs'][] = ($link === true) ? ['label' => $categories, 'url' => ['index']] : $categories;
