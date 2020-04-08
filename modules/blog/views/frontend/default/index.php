<?php

use yii\web\View;
use yii\helpers\Html;
use modules\blog\behaviors\CategoryTreeBehavior;
use modules\blog\models\Category;
use yii\widgets\Menu;
use modules\blog\Module;

/* @var $this View */
/** @var $category Category|CategoryTreeBehavior  */

$this->title = Module::t('module', 'Blog');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="blog-frontend-default-index">
    <h1><?= Html::decode($this->title) ?></h1>
    <?= Menu::widget([
        'items' => $category->getMenuItems()
    ]) ?>
    <?php /*foreach ($category->getRenderTree() as $item) {  echo $item; }*/ ?>
</div>
