<?php

use yii\helpers\Url;
use yii\widgets\ActiveForm;
use modules\comment\Module;
use modules\comment\models\Comment;
use modules\comment\assets\CommentAsset;

/* @var $this yii\web\View */
/* @var $model modules\comment\models\Comment */
/* @var $form yii\widgets\ActiveForm */

$categoryAsset = new CommentAsset([
    'url' => Url::to(['children-list'])
]);
$categoryAsset::register($this);
?>

<div class="comment-form-create">

    <?php $form = ActiveForm::begin([
        'id' => 'form-create'
    ]); ?>

    <?= $form->field($model, 'parentId')->dropDownList(Comment::getCommentTree(), [
        'id' => 'input-parent-id',
        'prompt' => Module::t('module', 'No Parent (saved as root)'),
    ])->label(Module::t('module', 'Parent')) ?>

    <div id="children-list-container" style="display:none;">
        <?= $form->field($model, 'childrenList')->listBox(Comment::getChildrenList(), [
            'id' => 'input-children-list',
        ]) ?>
        <?= $form->field($model, 'typeMove')->radioList(Comment::getMoveTypesArray(), [
            'id' => 'input-type-move',
        ]) ?>
    </div>

    <div id="entity-container">
        <?= $form->field($model, 'entity')->textInput([
            'id' => 'input-entity',
            'maxlength' => true
        ]) ?>

        <?= $form->field($model, 'entity_id')->textInput([
            'id' => 'input-entity-id',
        ]) ?>
    </div>

    <?= $form->field($model, 'author')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'comment')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'status')->dropDownList(Comment::getStatusesArray()) ?>

    <?php ActiveForm::end(); ?>

</div>
