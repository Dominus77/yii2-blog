<?php

use yii\widgets\ActiveForm;
use modules\config\params\ConfigParams;
use modules\config\Module;

/* @var $this yii\web\View */
/* @var $model modules\config\models\Config */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="config-default-form">

    <?php $form = ActiveForm::begin([
        'id' => 'form-config'
    ]); ?>

    <?php foreach ($model as $index => $value) : ?>
        <?php if ($value->type === ConfigParams::FIELD_TYPE_CHECKBOX) {
            echo $form->field($value, "[$index]value")
                ->checkbox()
                ->label(Module::t('module', $value->label));
        } elseif ($value->type === ConfigParams::FIELD_TYPE_TEXT) {
            echo $form->field($value, "[$index]value")->textarea([
                'rows' => 6,
                'placeholder' => Module::t('module', $value->default),

            ])->label(Module::t('module', $value->label));
        } elseif ($value->param === 'SITE_LANGUAGE') {
            echo $form->field($value, "[$index]value")->dropDownList([
                'ru' => 'Русский',
                'en' => 'English',
            ], [
                'prompt' => Module::t('module', 'Select language ...'),
            ])->label(Module::t('module', $value->label));
        } else {
            echo $form->field($value, "[$index]value")->textInput([
                'placeholder' => Module::t('module', $value->default)
            ])->label(Module::t('module', $value->label));
        } ?>
    <?php endforeach ?>

    <?php ActiveForm::end(); ?>

</div>
