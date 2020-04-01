<?php

use yii\web\View;
use yii\helpers\Html;
use modules\test\Module;

/* @var $this View */

$this->title = Module::t('module', 'test');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="test-backend-default-index">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><?= Html::encode($this->title) ?></h3>

            <div class="box-tools pull-right"></div>
        </div>
        <div class="box-body">
            <div class="pull-left"></div>
            <div class="pull-right"></div>
            <p>This is the module test backend page. You may modify the following file to customize its content:</p>
            <code><?= __FILE__ ?></code>
        </div>
        <div class="box-footer"></div>
    </div>
</div>
