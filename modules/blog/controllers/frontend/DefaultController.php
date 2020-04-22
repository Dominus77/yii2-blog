<?php

namespace modules\blog\controllers\frontend;

use Yii;
use yii\caching\TagDependency;
use yii\db\ActiveRecord;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use modules\blog\behaviors\CategoryTreeBehavior;
use modules\blog\models\Category;
use modules\blog\models\Post;
use modules\blog\models\Tag;
use Throwable;

/**
 * Class DefaultController
 * @package modules\blog\controllers\frontend
 */
class DefaultController extends Controller
{
    /**
     * Displays homepage.
     * @return mixed|Response
     */
    public function actionIndex()
    {
        $dataProvider = (new Post())->posts;
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param mixed $category
     * @return string
     * @throws NotFoundHttpException
     * @throws Throwable
     */
    public function actionCategory($category)
    {
        $model = $this->findCategoryModel($category);
        return $this->render('category', [
            'model' => $model,
            'dataProvider' => $model->getPostsDataProvider()
        ]);
    }

    /**
     * @param mixed $post
     * @param null $category
     * @return string
     * @throws NotFoundHttpException
     * @throws Throwable
     */
    public function actionPost($post, $category = null)
    {
        $post = $this->findPostModel($post, $category);
        return $this->render('post', [
            'model' => $post
        ]);
    }

    /**
     * @param mixed $tag
     * @return string
     * @throws NotFoundHttpException
     * @throws Throwable
     */
    public function actionTag($tag)
    {
        $dataProvider = $this->findPostsModelsByTag($tag);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Finds the Category model based on its path value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $path
     * @return array|mixed|ActiveRecord|null|Category
     * @throws NotFoundHttpException
     * @throws Throwable
     */
    protected function findCategoryModel($path)
    {
        /** @var Category|CategoryTreeBehavior $model */
        $model = new Category();
        if (($category = $model->findByPath($path)) && $category !== null) {
            return $category;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * @param string $slug
     * @param mixed|null $category
     * @return mixed
     * @throws NotFoundHttpException
     * @throws Throwable
     */
    protected function findPostModel($slug, $category = null)
    {
        if ($category !== null) {
            /** @var Category|CategoryTreeBehavior $categoryModel */
            $categoryModel = new Category();
            /** @var Category $model */
            if (($model = $categoryModel->findByPath($category)) && $model !== null) {
                $category = $model->id;
            }
        }

        $query = Post::find()
            ->where(['slug' => $slug])
            ->andWhere(['category_id' => $category, 'status' => Post::STATUS_PUBLISH]);

        $dependency = new TagDependency(['tags' => [Post::CACHE_TAG_POST]]);
        $model = Post::getDb()->cache(static function () use ($query) {
            return $query->one();
        }, Post::CACHE_DURATION, $dependency);

        if ($model !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * @param string $tag
     * @return mixed
     * @throws NotFoundHttpException
     * @throws Throwable
     */
    protected function findPostsModelsByTag($tag)
    {
        $cache = Yii::$app->cache;
        $key = [__METHOD__, __CLASS__, $tag];
        $dependency = new TagDependency(['tags' => [Tag::CACHE_TAG_TAGS]]);
        /** @var Tag $model */
        $model = $cache->getOrSet($key, static function () use ($tag) {
            return Tag::findOne(['title' => $tag]);
        }, Tag::CACHE_DURATION, $dependency);

        if ($model !== null) {
            return $model->getPostsDataProvider();
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
