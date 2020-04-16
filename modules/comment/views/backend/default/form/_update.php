<?php

use yii\widgets\ActiveForm;
use modules\comment\models\Comment;

/* @var $this yii\web\View */
/* @var $model modules\comment\models\Comment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="comment-form">

    <?php $form = ActiveForm::begin([
        'id' => 'form-update'
    ]); ?>

    <?= $form->field($model, 'entity')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'entity_id')->textInput() ?>

    <?= $form->field($model, 'author')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'comment')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'status')->dropDownList(Comment::getStatusesArray()) ?>

    <?php ActiveForm::end(); ?>

</div>
