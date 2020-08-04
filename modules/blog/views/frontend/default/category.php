<?php

use yii\web\View;
use yii\widgets\ListView;
use modules\blog\behaviors\CategoryTreeBehavior;
use modules\blog\models\Category;
use modules\blog\widgets\menu\CategoryMenu;
use modules\blog\widgets\tag\TagCloud;
use modules\blog\widgets\other\LastPost;
use modules\comment\widgets\other\LastComment;
use modules\blog\Module;
use modules\blog\assets\BlogAsset;
use modules\blog\widgets\search\SearchSidebar;

/** @var $this View */
/** @var $model Category|CategoryTreeBehavior */
/** @var $dataProvider Category */

BlogAsset::register($this);

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Module::t('module', 'Blog'), 'url' => ['index']];
$this->params['breadcrumbs'] = $model->getBreadcrumbs($this->params['breadcrumbs']);
?>

<div class="blog-frontend-default-category">
    <div class="row">
        <div class="col-md-3">
            <?= SearchSidebar::widget(['status' => true]) ?>
            <?= CategoryMenu::widget(['status' => true]) ?>
            <noindex>
                <?= LastPost::widget(['status' => true, 'limit' => 5]) ?>
            </noindex>
            <noindex>
                <?= TagCloud::widget(['status' => true, 'limit' => 50]) ?>
            </noindex>
            <noindex>
                <?= LastComment::widget(['status' => true, 'limit' => 5]) ?>
            </noindex>
        </div>
        <div class="col-md-9">
            <?= ListView::widget([
                'dataProvider' => $dataProvider,
                'layout' => "{items}\n{pager}",
                'itemView' => '_list'
            ]) ?>
        </div>
    </div>
</div>
