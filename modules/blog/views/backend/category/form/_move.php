<?php

use yii\helpers\Url;
use yii\widgets\ActiveForm;
use modules\blog\Module;
use modules\blog\models\Category;

/* @var $this yii\web\View */
/* @var $model modules\blog\models\Category */
/* @var $form yii\widgets\ActiveForm */

$childrenList = Category::getChildrenList($model->parentId, $model->id);
$display = empty($childrenList) ? 'style="display:none;"' : '';

$url = Url::to(['children-list']);
$id = $model->id;
$script = "
    $('#input-parent-id').on('change', function(){
        let parentId = $(this).val(),
            childrenListContainer = $('#children-list-container'),
            childrenList = $('#input-children-list'),
            typeMove = $('#input-type-move');            
        
        if(parentId === '') {
            childrenList.html(parentId);
            childrenListContainer.hide();
        } else {
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

<div class="category-form-move">

    <?php $form = ActiveForm::begin([
        'id' => 'form-move'
    ]); ?>

    <?= $form->field($model, 'parentId')->dropDownList(Category::getTree($model->id), [
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
