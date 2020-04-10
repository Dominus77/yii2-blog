<?php

use yii\web\View;
use yii\widgets\ListView;
use modules\blog\behaviors\CategoryTreeBehavior;
use modules\blog\models\Category;
use modules\blog\widgets\menu\CategoryMenu;
use modules\blog\widgets\tag\TagCloud;
use modules\blog\Module;

/** @var $this View */
/** @var $model Category|CategoryTreeBehavior */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Module::t('module', 'Blog'), 'url' => ['index']];
$this->params['breadcrumbs'] = $model->getBreadcrumbs($this->params['breadcrumbs']);
?>

<div class="blog-frontend-default-category">
    <div class="row">
        <div class="col-md-3">
            <?= CategoryMenu::widget(['status' => true]) ?>
            <noindex>
                <?= TagCloud::widget(['status' => false, 'limit' => 50]) ?>
            </noindex>
        </div>
        <div class="col-md-9">
            <?= ListView::widget([
                'dataProvider' => $model->posts,
                'layout' => "{items}\n{pager}",
                'itemView' => '_list'
            ]) ?>
        </div>
    </div>
</div>
