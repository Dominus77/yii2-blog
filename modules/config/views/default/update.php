<?php

use yii\helpers\Html;
use modules\config\Module;

/* @var $this yii\web\View */
/* @var $model modules\config\models\Config */

$this->title = Module::t('module', 'Settings');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="config-default-update">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><?= Module::t('module', 'General'); ?></h3>
        </div>
        <div class="box-body">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>

        </div>
        <div class="box-footer">
            <div class="form-group">
                <?= Html::submitButton('<span class="glyphicon glyphicon-ok" aria-hidden="true"></span> ' . Module::t('module', 'Save'), [
                    'class' => 'btn btn-primary',
                    'form' => 'form-config',
                ]) ?>
            </div>
        </div>
    </div>
</div>
