<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use modules\blog\Module;
use modules\blog\models\Category;

/* @var $this yii\web\View */
/* @var $model modules\blog\models\Category */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="category-form-move">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'parentId')->dropDownList(Category::getTree($model->id), [
        'prompt' => Module::t('module', 'No Parent (saved as root)'),
    ])->label(Module::t('module', 'Parent')) ?>

    <?= $form->field($model, 'childrenList')->listBox(Category::getSelectList($model->id)) ?>
    <?= $form->field($model, 'typeMove')->radioList(Category::getMoveTypesArray()) ?>

    <div class="form-group">
        <?= Html::submitButton(Module::t('module', 'Move'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
