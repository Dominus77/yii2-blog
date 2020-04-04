<?php

use yii\helpers\Url;
use yii\widgets\ActiveForm;
use modules\blog\Module;
use modules\blog\models\Category;
use modules\blog\assets\CategoryAsset;

/* @var $this yii\web\View */
/* @var $model modules\blog\models\Category */
/* @var $form yii\widgets\ActiveForm */

$childrenList = Category::getChildrenList($model->parentId, $model->id);
$display = empty($childrenList) ? 'style="display:none;"' : '';

$categoryAsset = new CategoryAsset([
    'url' => Url::to(['children-list']),
    'id' => $model->id
]);
$categoryAsset::register($this);
?>

<div class="category-form-move">

    <?php $form = ActiveForm::begin([
        'id' => 'form-move'
    ]); ?>

    <?= $form->field($model, 'parentId')->dropDownList(Category::getCategoriesTree($model->id), [
        'id' => 'input-parent-id',
        'prompt' => Module::t('module', 'No Parent (saved as root)'),
    ])->label(Module::t('module', 'Parent')) ?>

    <div id="children-list-container" <?= $display ?>>
        <?= $form->field($model, 'childrenList')->listBox($childrenList, [
            'id' => 'input-children-list',
        ]) ?>
        <?= $form->field($model, 'typeMove')->radioList(Category::getMoveTypesArray(), [
            'id' => 'input-type-move',
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
