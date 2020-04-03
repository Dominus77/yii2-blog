<?php

use yii\helpers\Url;
use yii\widgets\ActiveForm;
use modules\blog\Module;
use modules\blog\models\Category;

/* @var $this yii\web\View */
/* @var $model modules\blog\models\Category */
/* @var $form yii\widgets\ActiveForm */

$url = Url::to(['children-list']);
$id = 0;
$script = "
    $('#input-parent-id').on('change', function(){
        let parentId = $(this).val(),
            positionContainer = $('#position-container'),
            inputPosition = $('#input-position'),
            childrenListContainer = $('#children-list-container'),
            childrenList = $('#input-children-list'),
            typeMove = $('#input-type-move');            
        
        if(parentId === '') {
            childrenList.html(parentId);
            childrenListContainer.hide();
            positionContainer.show();
        } else {
            inputPosition.val(0);
            positionContainer.hide();
            $.ajax({
                url: '{$url}',
                dataType: 'json',
                type: 'post',
                data: {id: {$id}, parent: parentId}
            }).done(function (response) {
                childrenList.html(response.result);
                if(response.result === '') {                    
                    childrenListContainer.hide();
                } else {                    
                    childrenListContainer.show();
                }                
            });
        }     
    });
";

$this->registerJs($script);
?>

<div class="category-form-create">

    <?php $form = ActiveForm::begin([
        'id' => 'form-create'
    ]); ?>

    <?= $form->field($model, 'parentId')->dropDownList(Category::getTree($model->id), [
        'id' => 'input-parent-id',
        'prompt' => Module::t('module', 'No Parent (saved as root)'),
    ])->label(Module::t('module', 'Parent')) ?>

    <div id="children-list-container" style="display:none;">
        <?= $form->field($model, 'childrenList')->listBox(Category::getChildrenList($model->parentId, $model->id), [
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
        'placeholder' => Module::t('module', 'Automatically filled')
    ]) ?>

    <?= $form->field($model, 'description')->textarea([
        'rows' => 6,
        'placeholder' => true
    ]) ?>

    <?= $form->field($model, 'status')->dropDownList(Category::getStatusesArray()) ?>

    <?php ActiveForm::end(); ?>
</div>
