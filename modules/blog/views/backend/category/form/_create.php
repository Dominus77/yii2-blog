<?php

use yii\helpers\Url;
use yii\widgets\ActiveForm;
use modules\blog\Module;
use modules\blog\models\Category;
use modules\blog\assets\CategoryAsset;

/* @var $this yii\web\View */
/* @var $model modules\blog\models\Category */
/* @var $form yii\widgets\ActiveForm */

$categoryAsset = new CategoryAsset([
    'url' => Url::to(['children-list'])
]);
$categoryAsset::register($this);
?>

<div class="category-form-create">

    <?php $form = ActiveForm::begin([
        'id' => 'form-create'
    ]); ?>

    <?= $form->field($model, 'parentId')->dropDownList(Category::getTree(), [
        'id' => 'input-parent-id',
        'prompt' => Module::t('module', 'No Parent (saved as root)'),
    ])->label(Module::t('module', 'Parent')) ?>

    <div id="children-list-container" style="display:none;">
        <?= $form->field($model, 'childrenList')->listBox(Category::getChildrenList(), [
            'id' => 'input-children-list',
        ]) ?>
        <?= $form->field($model, 'typeMove')->radioList(Category::getMoveTypesArray(), [
            'id' => 'input-type-move',
        ]) ?>
    </div>

    <div id="position-container">
        <?= $form->field($model, 'position')->textInput([
            'id' => 'input-position',
            'maxlength' => true,
            'placeholder' => true
        ]) ?>
    </div>

    <?= $form->field($model, 'title')->textInput([
        'maxlength' => true,
        'placeholder' => true
    ]) ?>

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
