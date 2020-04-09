<?php

use yii\web\View;
use yii\widgets\ListView;
use modules\blog\behaviors\CategoryTreeBehavior;
use modules\blog\models\Category;
use modules\blog\models\Post;
use modules\blog\models\Tag;
use modules\blog\widgets\tag\TagCloud;
use yii\widgets\Menu;
use modules\blog\Module;

/* @var $this View */
/** @var $category Category|CategoryTreeBehavior */
/** @var $dataProvider Post */
/** @var $tags Tag */

$this->title = Module::t('module', 'Blog');
if ($tag = Yii::$app->request->get('tag')) {
    $this->title = Module::t('module', 'Entries tagged "{:tag}"', [':tag' => $tag]);
    $this->params['breadcrumbs'][] = ['label' => Module::t('module', 'Blog'), 'url' => ['index']];
}
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="blog-frontend-default-index">
    <div class="row">
        <div class="col-md-3">
            <?php if (($items = $category->getMenuItems()) && !empty($items)) { ?>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <?= Module::t('module', 'Menu') ?>
                    </div>
                    <div class="panel-body">
                        <?= Menu::widget([
                            'options' => ['class' => 'menu'],
                            'encodeLabels' => false,
                            'activateParents' => true,
                            'items' => array_filter($items)
                        ]) ?>
                    </div>
                </div>
            <?php } ?>

            <noindex>
                <div class="tag-cloud panel panel-default">
                    <div class="panel-heading">
                        <?= Module::t('module', 'Tags') ?>
                    </div>
                    <div class="panel-body">
                        <?= TagCloud::widget(['limit' => 50]) ?>
                    </div>
                </div>
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
