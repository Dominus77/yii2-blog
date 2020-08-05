<?php

use yii\helpers\Html;
use modules\search\Module;

$this->title = Module::t('module', 'Search');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="search-backend-default-index">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><?= Html::encode($this->title) ?></h3>

            <div class="box-tools pull-right"></div>
        </div>
        <div class="box-body">
            <div class="pull-left"></div>
            <div class="pull-right"></div>
            <p>This is the module search backend page. You may modify the following file to customize its content:</p>
            <code><?= __FILE__ ?></code>
        </div>
        <div class="box-footer"></div>
    </div>
</div>
