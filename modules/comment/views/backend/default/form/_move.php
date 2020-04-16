<?php

use yii\helpers\Url;
use yii\widgets\ActiveForm;
use modules\comment\models\Comment;
use modules\comment\assets\CommentAsset;
use modules\comment\Module;

/* @var $this yii\web\View */
/* @var $model modules\blog\models\Category */
/* @var $form yii\widgets\ActiveForm */

$childrenList = Comment::getChildrenList($model->parentId, $model->id);
$display = empty($childrenList) ? 'style="display:none;"' : '';

$categoryAsset = new CommentAsset([
    'url' => Url::to(['children-list']),
    'id' => $model->id
]);
$categoryAsset::register($this);
?>

<div class="comment-form-move">

    <?php $form = ActiveForm::begin([
        'id' => 'form-move'
    ]); ?>

    <?= $form->field($model, 'parentId')->dropDownList(Comment::getCommentTree($model->id), [
        'id' => 'input-parent-id',
        //'prompt' => Module::t('module', 'No Parent (saved as root)'),
    ])->label(Module::t('module', 'Parent')) ?>

    <div id="children-list-container" <?= $display ?>>
        <?= $form->field($model, 'childrenList')->listBox($childrenList, [
            'id' => 'input-children-list',
        ]) ?>
        <?= $form->field($model, 'typeMove')->radioList(Comment::getMoveTypesArray(), [
            'id' => 'input-type-move',
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
