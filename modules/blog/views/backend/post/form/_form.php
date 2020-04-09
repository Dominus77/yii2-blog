<?php

use dosamigos\selectize\SelectizeTextInput;
use yii\widgets\ActiveForm;
use modules\blog\models\Post;
use modules\blog\Module;

/* @var $this yii\web\View */
/* @var $model modules\blog\models\Post */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="post-form">
    <?php $form = ActiveForm::begin([
        'id' => 'post-form'
    ]); ?>

    <?= $form->field($model, 'category_id')->dropDownList(Post::getCategoriesTree(), [
        'id' => 'input-category-id',
        'prompt' => Module::t('module', '- No Category -'),
    ]) ?>

    <?= $form->field($model, 'sort')->textInput([
        'maxlength' => true,
        'placeholder' => true
    ])->hint(Module::t('module', 'If left blank, filled automatically')) ?>

    <?= $form->field($model, 'title')->textInput([
        'maxlength' => true,
        'placeholder' => true
    ]) ?>

    <?= $form->field($model, 'slug')->textInput([
        'maxlength' => true,
        'placeholder' => true
    ])->hint(Module::t('module', 'If left blank, filled automatically based on the title')) ?>

    <?= $form->field($model, 'anons')->textarea([
        'rows' => 6,
        'placeholder' => true
    ]) ?>

    <?= $form->field($model, 'content')->textarea([
        'rows' => 8,
        'placeholder' => true
    ]) ?>

    <?= $form->field($model, 'tagNames')->widget(SelectizeTextInput::class, [
        'loadUrl' => ['tag/list'],
        'options' => ['class' => 'form-control'],
        'clientOptions' => [
            'plugins' => ['remove_button'],
            'valueField' => 'name',
            'labelField' => 'name',
            'searchField' => ['name'],
            'create' => true,
        ],
    ])->hint(Module::t('module', 'Use commas to separate tags')) ?>

    <?= $form->field($model, 'status')->dropDownList(Post::getStatusesArray()) ?>

    <?php ActiveForm::end(); ?>
</div>
