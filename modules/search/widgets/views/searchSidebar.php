<?php

use yii\web\View;
use yii\helpers\Url;
use modules\search\Module;

/** @var $this View */
?>
<div class="search-sidebar panel panel-default">
    <div class="panel-heading">
        <span class="glyphicon glyphicon-search"></span> <?= Module::t('module', 'Search') ?>
    </div>
    <div class="panel-body">
        <form action="<?= Url::to(['/search/default/index']) ?>" method="get" class="sidebar-form">
            <div class="input-group">
                <label for="q-input"></label>
                <input id="q-input" type="text" name="q" class="form-control"
                       placeholder="<?= Module::t('module', 'Search') . '...' ?>">

                <span class="input-group-btn">
            <button type="submit" id="search-btn" class="btn btn-flat">
                <i class="fa fa-search"></i>
            </button>
        </span>
            </div>
        </form>
    </div>
</div>
