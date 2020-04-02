<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use modules\blog\Module;
use modules\blog\models\Category;

/* @var $this yii\web\View */
/* @var $model modules\blog\models\Category */
/* @var $form yii\widgets\ActiveForm */

$url = Url::to(['children-list']);
$id = $model->id;
$script = "
    $('#input-parent-id').on('change', function(){
        let parentId = $(this).val(),
            childrenList = $('#input-children-list'),
            typeMove = $('#input-type-move');
        
        $.ajax({
            url: '{$url}',
            dataType: 'json',
            type: 'post',
            data: {id: {$id}, parent: parentId}
        }).done(function (response) {           
            childrenList.html(response.result);
        });       
    });
";

$this->registerJs($script);
?>

<div class="category-form-move">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'parentId')->dropDownList(Category::getTree($model->id), [
        'id' => 'input-parent-id',
        'prompt' => Module::t('module', 'No Parent (saved as root)'),
    ])->label(Module::t('module', 'Parent')) ?>

    <?= $form->field($model, 'childrenList')->listBox(Category::getChildrenList($model->parentId, $model->id), [
        'id' => 'input-children-list',
    ]) ?>
    <?= $form->field($model, 'typeMove')->radioList(Category::getMoveTypesArray(), [
        'id' => 'input-type-move',
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton(Module::t('module', 'Move'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
