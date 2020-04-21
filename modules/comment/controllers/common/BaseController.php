<?php


namespace modules\comment\controllers\common;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class BaseController extends Controller
{
    /**
     * Read file storage folder
     * <img src="<?= Url::to(['/main/default/file', 'filename' => '1.png'])?>">
     * @throws NotFoundHttpException
     */
    public function actionFile()
    {
        $path = '@modules/comment/image';
        if ($file = Yii::$app->request->get('filename')) {
            $storagePath = Yii::getAlias($path);
            $response = Yii::$app->getResponse();
            $response->headers->set('Content-Type', 'image/jpg');
            $response->format = Response::FORMAT_RAW;
            $response->stream = fopen("$storagePath/$file", 'rb');
            return $response->send();
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}