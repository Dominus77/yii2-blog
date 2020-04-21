<?php

use yii\widgets\ActiveForm;
use modules\blog\models\Post;
use modules\blog\Module;
use modules\blog\editor\TinyMce;
use modules\blog\assets\HtmlFormattingAsset;
use dosamigos\selectize\SelectizeTextInput;

/* @var $this yii\web\View */
/* @var $model modules\blog\models\Post */
/* @var $form yii\widgets\ActiveForm */

HtmlFormattingAsset::register($this);
$tinyMceClientOptions = require dirname(dirname(dirname(dirname(__DIR__)))) . '/config/editor.php';
$tinyMceFileManager = require dirname(dirname(dirname(dirname(__DIR__)))) . '/config/file-manager.php';
?>

<div class="post-form">
    <?php $form = ActiveForm::begin([
        'id' => 'post-form'
    ]); ?>

    <?= $form->field($model, 'title')->textInput([
        'maxlength' => true,
        'placeholder' => true
    ]) ?>

    <?= $form->field($model, 'slug')->textInput([
        'maxlength' => true,
        'placeholder' => true
    ])->hint(Module::t('module', 'If left blank, filled automatically based on the title')) ?>

    <?= $form->field($model, 'anons')->widget(TinyMce::class, [
        'options' => [
            'rows' => 14,
            'placeholder' => true,
        ],
        'language' => Yii::$app->language,
        'clientOptions' => $tinyMceClientOptions['anons'],
        'fileManager' => $tinyMceFileManager['anons'],
    ]) ?>

    <?= $form->field($model, 'content')->widget(TinyMce::class, [
        'options' => [
            'rows' => 20,
            'placeholder' => true,
        ],
        'language' => Yii::$app->language,
        'clientOptions' => $tinyMceClientOptions['content'],
        'fileManager' => $tinyMceFileManager['content'],
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

    <?= $form->field($model, 'category_id')->dropDownList(Post::getCategoriesTree(), [
        'id' => 'input-category-id',
        'prompt' => Module::t('module', '- No Category -'),
    ]) ?>

    <?= $form->field($model, 'sort')->textInput([
        'maxlength' => true,
        'placeholder' => true
    ])->hint(Module::t('module', 'If left blank, filled automatically')) ?>

    <?= $form->field($model, 'status')->dropDownList(Post::getStatusesArray()) ?>

    <?= $form->field($model, 'is_comment')->dropDownList(Post::getCommentsArray()) ?>

    <?php ActiveForm::end(); ?>
</div>
