<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\captcha\Captcha;
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
    ])->hint(Module::t('module', 'No one will see')) ?>

    <?= $form->field($model, 'comment')->textarea([
        'rows' => 6,
        'class' => 'form-control',
        'placeholder' => true
    ]) ?>

    <?php if ($model->scenario === $model::SCENARIO_GUEST) { ?>
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'verifyCode')->widget(Captcha::class, [
                    'captchaAction' => Url::to('/comment/default/captcha'),
                    'imageOptions' => [
                        'style' => 'display:block; border:none; cursor: pointer',
                        'alt' => Module::t('module', 'Code'),
                        'title' => Module::t('module', 'Click on the picture to change the code.')
                    ],
                ])->label(false) ?>
            </div>
        </div>
    <?php } ?>

    <?= $form->field($model, 'entity')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'entity_id')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'rootId')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'parentId')->hiddenInput()->label(false) ?>

    <div class="form-group">
        <?= Html::submitButton('<span class="glyphicon glyphicon-send"></span> ' . Module::t('module', 'Submit comment'), [
            'class' => 'btn btn-primary',
            'name' => 'comment-button'
        ]) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>