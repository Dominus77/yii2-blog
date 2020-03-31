<?php

use yii\web\View;
use yii\helpers\Html;
use modules\blog\Module;

/* @var $this View */

$this->title = Module::t('module', 'Blog');
?>

<div class="blog-frontend-default-index">
    <h1><?= Html::decode($this->title) ?></h1>
</div>
