<?php

use yii\helpers\Url;
use yii\widgets\LinkPager;
use modules\search\assets\SearchAssets;
use modules\search\Module;

/** @var yii\web\View $this */
/** @var ZendSearch\Lucene\Search\QueryHit[] $hits */
/** @var string $query */
/** @var yii\data\Pagination $pagination */

$query = yii\helpers\Html::encode($query);

$this->title = Module::t('module', 'Results for "{:query}"', [':query' => $query]);
$this->params['breadcrumbs'] = [Module::t('module', 'Search'), $this->title];

SearchAssets::register($this);
$this->registerJs("jQuery('.search').highlight('{$query}');");
?>
<div class="search-frontend-default-index">
    <?php
    if (!empty($hits)):
        foreach ($hits as $hit):
            ?>
            <h3><a href="<?= Url::to($hit->url, true) ?>"><?= $hit->title ?></a></h3>
            <p class="search"><?= $hit->content ?></p>
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
