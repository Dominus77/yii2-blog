<?php

namespace modules\blog\behaviors;

use paulzi\autotree\AutoTreeTrait;
use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\helpers\VarDumper;

/**
 * Class CategoryTreeBehavior
 * @package modules\blog\behaviors
 *
 * @property ActiveRecord[] $owner
 * @property string $path
 */
class CategoryTreeBehavior extends Behavior
{

    public $titleAttribute = 'title';
    public $slugAttribute = 'slug';
    public $urlAttribute = 'url';
    public $linkActiveAttribute = 'active';
    public $requestPathAttribute = 'path';
    public $defaultCriteria = [];
    public $treeAttribute = 'tree';
    public $lftAttribute = 'lft';
    public $rgtAttribute = 'rgt';
    public $depthAttribute = 'depth';
    public $parentRelation = 'parents';
    public $statusAttribute = 'status';
    public $status = 1;

    /**
     * Finds model by path
     * @param string $path
     * @return array|ActiveRecord|null
     */
    public function findByPath($path = '')
    {
        $domains = explode('/', trim($path, '/'));
        $model = null;
        /** @var  ActiveRecord $owner */
        $owner = $this->owner;
        if (count($domains) === 1) {
            $model = $owner::find()->where([$this->slugAttribute => $domains[0], $this->depthAttribute => 0])->one();
        } else {
            $parent = $owner::find()->where([$this->slugAttribute => $domains[0]])->one();
            if ($parent) {
                $domains = array_slice($domains, 1);
                foreach ($domains as $alias) {
                    $model = $parent::find()->where([$this->slugAttribute => $alias, $this->treeAttribute => $parent->{$this->treeAttribute}])->one();
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
     */
    public function getPath($separator = '/')
    {
        $uri = [];
        $parents = $this->owner->{$this->parentRelation};
        foreach ($parents as $item) {
            $uri[] = $item->{$this->slugAttribute};
        }
        $uri[] = $this->owner->{$this->slugAttribute};
        return implode($uri, $separator);
    }

    /**
     * Constructs breadcrumbs for yii\widgets\Breadcrumbs widget
     * @param array $parentBreadcrumbs parent breadcrumbs
     * @param bool $lastLink if you can have link in last element
     * @return array
     */
    public function getBreadcrumbs($parentBreadcrumbs = [], $lastLink = false)
    {
        if (!empty($parentBreadcrumbs)) {
            foreach ($parentBreadcrumbs as $item) {
                $breadcrumbs[] = $item;
            }
        }
        $parents = $this->owner->{$this->parentRelation};
        foreach ($parents as $item) {
            $breadcrumbs[] = ['label' => $item->{$this->titleAttribute}, 'url' => $item->{$this->urlAttribute}];
        }
        if ($lastLink) {
            $breadcrumbs[] = ['label' => $this->owner->{$this->titleAttribute}, 'url' => $this->owner->{$this->urlAttribute}];
        } else {
            $breadcrumbs[] = $this->owner->{$this->titleAttribute};
        }
        return $breadcrumbs;
    }

    /**
     * Returns items for yii\widgets\Menu widget
     * @return array
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
                $item = $node->toArray($exportedAttributes);
                $item['label'] = $item['title'];
                $item['url'] = [$node->{$this->urlAttribute}];
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
        return $trees;
    }

    /**
     * @param int $depthStart
     * @param bool $tree
     * @return array|AutoTreeTrait|ActiveRecord[]
     */
    protected function getNodes($depthStart = 0, $tree = true)
    {
        /** @var  ActiveRecord $owner */
        $owner = $this->owner;
        $query = $owner::find()->where([$this->statusAttribute => $this->status])
            ->andWhere($this->depthAttribute . ' >=' . $depthStart);
        if ($tree === true) {
            $query->orderBy([$this->treeAttribute => SORT_ASC, $this->lftAttribute => SORT_ASC]);
        } else {
            $query->orderBy([$this->lftAttribute => SORT_ASC]);
        }
        return $query->all();
    }

    /**
     * Export NestedSets tree into JsTree nested format data
     * @return array
     */
    public function asJsTree()
    {
        $rVal = [];
        /** @var  ActiveRecord|AutoTreeTrait $owner */
        $owner = $this->owner;
        $roots = $owner::find()->roots()->all();
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
}
