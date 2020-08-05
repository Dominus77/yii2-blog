<?php

use yii\web\View;
use yii\bootstrap\ActiveForm;
use modules\search\models\SearchForm;
use modules\search\Module;

/** @var $this View */
/** @var $model SearchForm */
/** @var $form ActiveForm */
?>
<div class="search-sidebar panel panel-default">
    <div class="panel-heading">
        <span class="glyphicon glyphicon-search"></span> <?= Module::t('module', 'Search') ?>
    </div>
    <div class="panel-body">
        <?php $form = ActiveForm::begin([
            'method' => 'get',
            'action' => ['/search/default/index'],
            'options' => [
                'class' => 'sidebar-form',
            ]
        ]); ?>

        <?= $form->field($model, 'q', [
            'template' => "{label}\n<div class=\"input-group\">{input}\n<span class=\"input-group-btn\"><button class=\"btn btn-default\" type=\"submit\" id=\"search-btn\"><span class=\"glyphicon glyphicon-search\" aria-hidden=\"true\"></span></button></span></div>\n{hint}"
        ])->textInput([
            'maxlength' => true,
            'placeholder' => Module::t('module', 'Search') . '...',
        ])->hint(Module::t('module', 'Enter your request'))
            ->label(false) ?>

        <?php ActiveForm::end(); ?>
    </div>
</div>
