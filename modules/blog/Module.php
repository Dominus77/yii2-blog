<?php

namespace modules\blog;

use Yii;
use yii\console\Application as ConsoleApplication;
use mihaildev\elfinder\Controller;
use modules\rbac\models\Permission;

/**
 * Class Module
 * @package modules\blog
 */
class Module extends \yii\base\Module
{
    /** @var string */
    public static $name = 'blog';
    /** @var array */
    public $sizes = [20 => 20, 25 => 25, 50 => 50, 100 => 100, 200 => 200, 500 => 500, 1000 => 1000];
    /** @var int */
    public $defaultPageSize = 100;

    /**
     * @var string
     */
    public $controllerNamespace = 'modules\blog\controllers\frontend';

    /**
     * @var bool Если модуль используется для админ-панели.
     */
    public $isBackend;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if ($this->isBackend === true) {
            $this->controllerNamespace = 'modules\blog\controllers\backend';
            $this->setViewPath('@modules/blog/views/backend');

            $this->controllerMap = [
                'elfinder' => [
                    'class' => Controller::class,
                    'access' => [Permission::PERMISSION_MANAGER_POST],
                    'disabledCommands' => ['netmount'],
                    'roots' => [
                        [
                            'baseUrl' => Yii::$app->urlManagerFrontend->baseUrl,//Yii::$app->params['domainFrontend'],
                            'basePath' => '@upload',
                            'path' => 'uploads/blog',
                            'name' => self::t('module', 'uploads'),
                            'access' => ['read' => '*', 'write' => Permission::PERMISSION_MANAGER_POST]
                        ],
                    ],
                    'watermark' => [
                        'source'         => Yii::getAlias('@frontend/web/images/watermark.png'),//__DIR__.'/logo.png', // Path to Water mark image
                        'marginRight'    => 5,          // Margin right pixel
                        'marginBottom'   => 5,          // Margin bottom pixel
                        'quality'        => 95,         // JPEG image save quality
                        'transparency'   => 70,         // Water mark image transparency ( other than PNG )
                        'targetType'     => IMG_GIF|IMG_JPG|IMG_PNG|IMG_WBMP, // Target image formats ( bit-field )
                        'targetMinPixel' => 200         // Target image minimum pixel size
                    ],
                ]
            ];
        } else {
            $this->setViewPath('@modules/blog/views/frontend');
        }
        if (Yii::$app instanceof ConsoleApplication) {
            $this->controllerNamespace = 'modules\blog\commands';
        }
    }

    /**
     * @param string $category
     * @param string $message
     * @param array $params
     * @param null|string $language
     * @return string
     */
    public static function t($category, $message, $params = [], $language = null)
    {
        return Yii::t('modules/blog/' . $category, $message, $params, $language);
    }
}
