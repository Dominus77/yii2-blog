<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use modules\blog\Module;
use modules\blog\models\Category;

/* @var $this yii\web\View */
/* @var $model modules\blog\models\Category */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="category-form-update">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'slug')->textInput([
        'maxlength' => true,
        'placeholder' => Module::t('module', 'Automatically filled')
    ]) ?>

    <?= $form->field($model, 'description')->textarea([
        'rows' => 6,
        'placeholder' => true
    ]) ?>

    <?= $form->field($model, 'status')->dropDownList(Category::getStatusesArray()) ?>

    <div class="form-group">
        <?= Html::submitButton(Module::t('module', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
