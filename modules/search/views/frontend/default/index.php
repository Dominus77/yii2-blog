<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\LinkPager;
use yii\data\Pagination;
use ZendSearch\Lucene\Search\QueryHit;
use modules\search\models\SearchForm;
use modules\search\assets\SearchAssets;
use modules\search\Module;

/** @var View $this */
/** @var QueryHit[] $hits */
/** @var string $query */
/** @var Pagination $pagination */
/** @var SearchForm $model */
/** @var string $score Search score */

$query = Html::encode($model->q);

$this->title = Module::t('module', 'Results for "{:query}"', [':query' => $query]);
$this->params['breadcrumbs'] = [Module::t('module', 'Search'), $this->title];

SearchAssets::register($this);
$this->registerJs("jQuery('.search').highlight('{$query}');");
?>
<div class="search-frontend-default-index">
    <?php
    if (!empty($hits)):
        echo Module::t('module', 'The search took about {:score} seconds.', [':score' => $score]);
        foreach ($hits as $hit):
            ?>
            <h3><a href="<?= Url::to($hit->url, true) ?>"><?= $hit->title ?></a></h3>
            <p class="search">
                <?= $hit->anons ?>
                <br>
                <br>
                <?= $hit->content ?>
            </p>
            <hr/>
        <?php
        endforeach;
    else:
        ?>
        <h3><?= Module::t('module', 'The "{:query}" isn\'t found!', [':query' => $query]) ?></h3>
    <?php
    endif;

    echo LinkPager::widget([
        'pagination' => $pagination,
    ]);
    ?>
</div>
