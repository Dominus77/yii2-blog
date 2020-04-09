<?php

namespace modules\blog\controllers\frontend;

use modules\blog\models\Tag;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use modules\blog\behaviors\CategoryTreeBehavior;
use modules\blog\models\Category;
use modules\blog\models\Post;

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
        $category = new Category();
        $model = new Post();
        $tags = new Tag();
        return $this->render('index', [
            'category' => $category,
            'dataProvider' => $model->posts,
            'tags' => $tags
        ]);
    }

    /**
     * @param $category
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionCategory($category)
    {
        $model = $this->findCategoryModel($category);
        $tags = new Tag();
        return $this->render('category', [
            'model' => $model,
            'tags' => $tags
        ]);
    }

    /**
     * @param $post
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionPost($post)
    {
        $post = $this->findPostModel($post);
        $tags = new Tag();
        return $this->render('post', [
            'model' => $post,
            'tags' => $tags
        ]);
    }

    /**
     * @param $tag
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionTag($tag)
    {
        $category = new Category();
        $dataProvider = $this->findPostsModelsByTag($tag);
        $tags = new Tag();
        return $this->render('index', [
            'category' => $category,
            'dataProvider' => $dataProvider,
            'tags' => $tags
        ]);
    }

    /**
     * Finds the Category model based on its path value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param $path string
     * @return array|ActiveRecord|null
     * @throws NotFoundHttpException
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
     * @param $slug string
     * @return Post
     * @throws NotFoundHttpException
     */
    protected function findPostModel($slug)
    {
        /** @var Post $model */
        $model = Post::find()
            ->where(['slug' => $slug])
            ->andWhere(['status' => Post::STATUS_PUBLISH])
            ->one();
        if ($model !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * @param $tag
     * @return ActiveDataProvider
     * @throws NotFoundHttpException
     */
    protected function findPostsModelsByTag($tag)
    {
        /** @var Tag $model */
        if (($model = Tag::findOne(['title' => $tag])) && $model !== null) {
            return new ActiveDataProvider([
                'query' => $model->getPosts(),
                'pagination' => [
                    'pageSize' => Post::PAGE_SIZE,
                ],
                'sort' => [
                    'defaultOrder' => [
                        'created_at' => SORT_DESC,
                        'sort' => SORT_ASC,
                    ]
                ]
            ]);
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
