<?php

namespace modules\blog\behaviors;

use Yii;
use yii\base\Behavior;
use yii\caching\TagDependency;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use paulzi\autotree\AutoTreeTrait;
use paulzi\nestedsets\NestedSetsQueryTrait;
use Throwable;
use yii\helpers\VarDumper;

/**
 * Class CategoryTreeBehavior
 * @package modules\blog\behaviors
 *
 * @property ActiveRecord[] $owner
 * @property string $path
 * @property string $linkActive
 */
class CategoryTreeBehavior extends Behavior
{

    public $titleAttribute = 'title';
    public $slugAttribute = 'slug';
    public $urlAttribute = 'url';
    public $linkActiveAttribute = 'active';
    public $requestPathAttribute = 'path';
    public $treeAttribute = 'tree';
    public $lftAttribute = 'lft';
    public $rgtAttribute = 'rgt';
    public $depthAttribute = 'depth';
    public $parentRelation = 'parents';
    public $iconAttribute;
    public $linkTemplate = '<a href="{url}">{label}</a>';
    public $linkTemplateActive = '<a rel="nofollow" href="{url}" class="active">{label}</a>';
    public $statusAttribute = 'status';
    public $positionAttribute = 'position';
    public $status;

    const CACHE_DURATION = 0;//3600; // 1 час
    const CACHE_TAG_BLOG = 'blog';
    const CACHE_TAG_CATEGORY = 'category';
    const CACHE_TAG_PATH = 'path';
    const CACHE_TAG_BREADCRUMBS = 'breadcrumbs';
    const CACHE_TAG_NODES = 'nodes';
    const CACHE_TAG_JS_TREE = 'js-tree';

    /**
     * Finds model by path
     * @param string $path
     * @return array|mixed|ActiveRecord|null
     * @throws Throwable
     */
    public function findByPath($path = '')
    {
        $domains = explode('/', trim($path, '/'));
        $model = null;
        /** @var  ActiveRecord $owner */
        $owner = $this->owner;
        $dependency = new TagDependency(['tags' => [self::CACHE_TAG_BLOG, self::CACHE_TAG_CATEGORY, self::CACHE_TAG_PATH]]);

        if (count($domains) === 1) {
            $query = $owner::find()->where([$this->slugAttribute => $domains[0], $this->depthAttribute => 0]);
            $model = $owner::getDb()->cache(static function () use ($query) {
                return $query->one();
            }, self::CACHE_DURATION, $dependency);
        } else {
            $query = $owner::find()->where([$this->slugAttribute => $domains[0]]);
            $parent = $owner::getDb()->cache(static function () use ($query) {
                return $query->one();
            }, self::CACHE_DURATION, $dependency);
            if ($parent) {
                $domains = array_slice($domains, 1);
                foreach ($domains as $alias) {
                    /** @var ActiveRecord $parent */
                    $query = $parent::find()->where([$this->slugAttribute => $alias, $this->treeAttribute => $parent->{$this->treeAttribute}]);
                    $model = $parent::getDb()->cache(static function () use ($query) {
                        return $query->one();
                    }, self::CACHE_DURATION, $dependency);

                    if (!$model) {
                        return null;
                    }
                    $parent = $model;
                }
            }
        }
        return $model;
    }

    /**
     * Constructs full path for current model
     * @param string $separator
     * @return string
     * @throws Throwable
     */
    public function getPath($separator = '/')
    {
        /** @var ActiveRecord $owner */
        $owner = $this->owner;
        $dependency = new TagDependency(['tags' => [self::CACHE_TAG_BLOG, self::CACHE_TAG_CATEGORY, self::CACHE_TAG_PATH]]);
        $parents = $owner::getDb()->cache(function () {
            return $this->owner->{$this->parentRelation};
        }, self::CACHE_DURATION, $dependency);

        $uri = [];
        foreach ($parents as $item) {
            $uri[] = $item->{$this->slugAttribute};
        }
        $uri[] = $owner->{$this->slugAttribute};
        return implode($uri, $separator);
    }

    /**
     * Constructs breadcrumbs for yii\widgets\Breadcrumbs widget
     * @param array $parentBreadcrumbs parent breadcrumbs
     * @param bool $lastLink if you can have link in last element
     * @return array
     * @throws Throwable
     */
    public function getBreadcrumbs($parentBreadcrumbs = [], $lastLink = false)
    {
        if (!empty($parentBreadcrumbs)) {
            foreach ($parentBreadcrumbs as $item) {
                $breadcrumbs[] = $item;
            }
        }

        /** @var ActiveRecord $owner */
        $owner = $this->owner;
        $dependency = new TagDependency(['tags' => [self::CACHE_TAG_BLOG, self::CACHE_TAG_CATEGORY, self::CACHE_TAG_BREADCRUMBS]]);
        $parents = $owner::getDb()->cache(function () {
            return $this->owner->{$this->parentRelation};
        }, self::CACHE_DURATION, $dependency);

        foreach ($parents as $item) {
            $breadcrumbs[] = ['label' => $item->{$this->titleAttribute}, 'url' => $item->{$this->urlAttribute}];
        }
        if ($lastLink) {
            $breadcrumbs[] = ['label' => $owner->{$this->titleAttribute}, 'url' => $owner->{$this->urlAttribute}];
        } else {
            $breadcrumbs[] = $owner->{$this->titleAttribute};
        }
        return $breadcrumbs;
    }

    /**
     * Returns items for yii\widgets\Menu widget
     * @return array
     * @throws Throwable
     */
    public function getMenuItems()
    {
        return $this->toNestedArray();
    }

    /**
     * Convert a tree into nested arrays. If you use the default function parameters you get
     * a set compatible with Yii2 Menu widget.
     * @param null $depth
     * @param string $itemsKey
     * @param string $getDataCallback
     * @return array
     * @throws Throwable
     */
    protected function toNestedArray($depth = null, $itemsKey = 'items', $getDataCallback = '')
    {
        $nodes = $this->getNodes();
        /** @var  ActiveRecord|AutoTreeTrait $owner */
        $owner = $this->owner;
        $exportedAttributes = array_diff(array_keys($owner->attributes), [$this->lftAttribute, $this->rgtAttribute]);

        $trees = [];
        $stack = [];

        foreach ($nodes as $node) {
            if ($getDataCallback) {
                $item = $getDataCallback($node);
            } else {
                $active = $node->{$this->linkActiveAttribute};
                $item = $node->toArray($exportedAttributes);
                $item['label'] = $item[$this->titleAttribute];
                $item['url'] = [$node->{$this->urlAttribute}];
                $item['icon'] = $this->iconAttribute !== null ? $node->{$this->iconAttribute} : '';
                $item['options'] = ['class' => 'item_' . $node->getPrimaryKey()];
                $item['active'] = $active;
                $item['template'] = $active ? $this->linkTemplateActive : $this->linkTemplate;
            }

            $item[$itemsKey] = [];
            $l = count($stack);

            while ($l > 0 && $stack[$l - 1][$this->depthAttribute] >= $item[$this->depthAttribute]) {
                array_pop($stack);
                $l--;
            }
            if ($l === 0) {
                // Assign root node
                $i = count($trees);
                $trees[$i] = $item;
                $stack[] = &$trees[$i];
            } else {
                // Add node to parent
                $i = count($stack[$l - 1][$itemsKey]);
                $stack[$l - 1][$itemsKey][$i] = $item;
                $stack[] = &$stack[$l - 1][$itemsKey][$i];
            }
        }
        ArrayHelper::multisort($trees, $this->positionAttribute, SORT_ASC);
        return $trees;
    }

    /**
     * Get request data
     * @param int $depthStart
     * @param bool $tree
     * @return mixed
     * @throws Throwable
     */
    protected function getNodes($depthStart = 0, $tree = true)
    {
        /** @var  ActiveRecord $owner */
        $owner = $this->owner;
        $query = $owner::find()->where($this->depthAttribute . ' >=' . $depthStart);
        if ($this->status !== null && $this->statusAttribute) {
            $query->andWhere([$this->statusAttribute => $this->status]);
        }
        if ($tree === true) {
            $query->orderBy([$this->treeAttribute => SORT_ASC, $this->lftAttribute => SORT_ASC]);
        } else {
            $query->orderBy([$this->lftAttribute => SORT_ASC]);
        }

        $dependency = new TagDependency(['tags' => [self::CACHE_TAG_BLOG, self::CACHE_TAG_CATEGORY, self::CACHE_TAG_NODES]]);
        return $owner::getDb()->cache(static function () use ($query) {
            return $query->all();
        }, self::CACHE_DURATION, $dependency);
    }

    /**
     * Export NestedSets tree into JsTree nested format data
     * @return array
     * @throws Throwable
     */
    public function asJsTree()
    {
        $rVal = [];
        /** @var  ActiveRecord $owner */
        $owner = $this->owner;
        /** @var NestedSetsQueryTrait $query */
        $query = $owner::find();
        $dependency = new TagDependency(['tags' => [self::CACHE_TAG_BLOG, self::CACHE_TAG_CATEGORY, self::CACHE_TAG_JS_TREE]]);
        $roots = $owner::getDb()->cache(static function () use ($query) {
            return $query->roots()->all();
        }, self::CACHE_DURATION, $dependency);

        $attributes = ['titleAttribute' => $this->titleAttribute, 'depthAttribute' => $this->depthAttribute];
        foreach ($roots as $root) {
            $rVal[] = [
                'id' => $root->id,
                'text' => $root->{$this->titleAttribute},
                'children' => $this->toNestedArray(null, 'children', static function ($node) use ($attributes) {
                    return [
                        'id' => $node->id,
                        'text' => $node->{$attributes['titleAttribute']},
                        'depth' => $node->{$attributes['depthAttribute']}
                    ];
                }),
            ];
        }
        return $rVal;
    }

    /**
     * @return bool
     */
    public function getActive()
    {
        return $this->linkActive;
    }

    /**
     * Check is active link
     * @return bool
     */
    public function getLinkActive()
    {
        $request = Yii::$app->request;
        return ArrayHelper::isIn($this->owner->{$this->requestPathAttribute}, $request->get());
    }
}
