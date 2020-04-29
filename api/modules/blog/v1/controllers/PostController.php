<?php

namespace api\modules\blog\v1\controllers;

use api\modules\v1\models\Message;
use yii\filters\Cors;
use yii\rest\ActiveController;
use yii\rest\Controller;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use api\modules\blog\v1\models\Post;

/**
 * Class PostController
 * @package api\modules\blog\v1\controllers
 */
class PostController extends ActiveController
{
    /**
     * @var string
     */
    public $modelClass = Post::class;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // Add CORS filter
        $behaviors['corsFilter'] = [
            'class' => Cors::class
        ];
        return $behaviors;
    }
}
