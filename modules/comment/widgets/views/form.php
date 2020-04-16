<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use modules\comment\models\Comment;
use modules\comment\Module;

/** @var $this yii\web\View */
/** @var $model Comment */

?>

<div class="comment-widget-form">
    <?php $form = ActiveForm::begin([
        'id' => 'comment-form',
        'enableClientValidation' => true,
        'action' => Url::to(['/comment/default/add'])
    ]); ?>

    <?= $form->field($model, 'author')->textInput([
        'class' => 'form-control',
        'placeholder' => true
    ]) ?>

    <?= $form->field($model, 'email')->textInput([
        'class' => 'form-control',
        'placeholder' => true
    ]) ?>

    <?= $form->field($model, 'comment')->textarea([
        'rows' => 6,
        'class' => 'form-control',
        'placeholder' => true
    ]) ?>

    <?= $form->field($model, 'entity')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'entity_id')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'rootId')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'parentId')->hiddenInput()->label(false) ?>

    <div class="form-group">
        <?= Html::submitButton('<span class="glyphicon glyphicon-send"></span> ' . Module::t('module', 'Submit comment'), [
            'class' => 'btn btn-primary',
            'name' => 'contact-button'
        ]) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>