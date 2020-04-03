<?php

use yii\widgets\ActiveForm;
use modules\blog\Module;
use modules\blog\models\Category;

/* @var $this yii\web\View */
/* @var $model modules\blog\models\Category */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="category-form-update">

    <?php $form = ActiveForm::begin([
        'id' => 'form-update'
    ]); ?>

    <?php if ($model->isRoot()) { ?>
        <?= $form->field($model, 'position')->textInput([
            'maxlength' => true,
            'placeholder' => true
        ]) ?>
    <?php } ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'slug')->textInput([
        'maxlength' => true,
        'placeholder' => true
    ])->hint(Module::t('module', 'If left blank, filled automatically based on the title')) ?>

    <?= $form->field($model, 'description')->textarea([
        'rows' => 6,
        'placeholder' => true
    ]) ?>

    <?= $form->field($model, 'status')->dropDownList(Category::getStatusesArray()) ?>

    <?php ActiveForm::end(); ?>

</div>
