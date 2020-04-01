<?php

use yii\web\View;
use yii\helpers\Html;
use modules\test\Module;

/* @var $this View */

$this->title = Module::t('module', 'test');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="test-frontend-default-index">
    <h1><?= Html::decode($this->title) ?></h1>

    <p>This is the module test frontend page. You may modify the following file to customize its content:</p>

    <code><?= __FILE__ ?></code>
</div>
