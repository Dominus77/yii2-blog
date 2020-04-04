<?php

use yii\widgets\ActiveForm;
use modules\blog\models\Tag;

/* @var $this yii\web\View */
/* @var $model modules\blog\models\Tag */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tag-form">

    <?php $form = ActiveForm::begin([
        'id' => 'tag-form'
    ]); ?>

    <?= $form->field($model, 'title')->textInput([
        'maxlength' => true,
        'placeholder' => true
    ]) ?>

    <?= $form->field($model, 'status')->dropDownList(Tag::getStatusesArray()) ?>

    <?php ActiveForm::end(); ?>

</div>
