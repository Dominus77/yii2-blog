# yii2-config-db

[![GitHub issues](https://img.shields.io/github/issues/Dominus77/yii2-config-db.svg)](https://github.com/Dominus77/yii2-config-db/issues)
[![GitHub forks](https://img.shields.io/github/forks/Dominus77/yii2-config-db.svg)](https://github.com/Dominus77/yii2-config-db/network)
[![GitHub stars](https://img.shields.io/github/stars/Dominus77/yii2-config-db.svg)](https://github.com/Dominus77/yii2-config-db/stargazers)
[![GitHub license](https://img.shields.io/github/license/Dominus77/yii2-config-db.svg)](https://github.com/Dominus77/yii2-config-db/blob/master/LICENSE.md)
[![Twitter](https://img.shields.io/twitter/url/https/github.com/Dominus77/yii2-config-db.svg?style=social)](https://twitter.com/intent/tweet?text=Wow:&url=https%3A%2F%2Fgithub.com%2FDominus77%2Fyii2-config-db)

Модуль для хранения, вывода и редактирования настроек приложения Yii2 в базе данных.

> Примечание: Развертывание модуля описано на базе [yii2-advanced-start](https://github.com/Dominus77/yii2-advanced-start). 
> Подключение модуля не ограничивается данным шаблоном, разница подключения описаная ниже, не существенная.

### Установка
Выполнить в корне приложения
```
git clone https://github.com/Dominus77/yii2-config-db.git modules/config
```
Применить миграцию
```
php yii migrate/up -p=@modules/config/migrations

```

### Подключение для advanced
Подключаем компонент модуля в common части, что бы компонент был доступен во всём приложении
```
// common\config\main.php

$config = [
    //...
    'components' => [
        'config' => [
            'class' => 'modules\config\components\DConfig',
            'duration' => 3600, // Время для кэширования   
        ],
        // Определяем место для кэша
        'cache' => [
            'class' => 'yii\caching\FileCache',
            'cachePath' => '@frontend/runtime/cache',
        ],
        //...
    ],
    //...
];
```
Подключаем модуль в backend части для возможности изменять значения в админке
```
// backend\config\main.php

$config = [
    'bootstrap' => [
        //...
        'modules\config\Bootstrap',
    ],
    'modules' => [
        'config' => [
            'class' => 'modules\config\Module',
            'params' => [
                'accessRoles' => ['@'], // Уровень доступа к форме изменения параметров
            ],
        ],
        //...
    ],
    // Подключаем поведение для замены параметров конфигурации нашими параметрами
    'as beforeConfig' => [
        'class' => '\modules\config\components\behaviors\ConfigBehavior',        
    ],
    //...    
];

```
Подключаем модуль в console части для консольных команд
```
// console\config\main.php

$config = [
    'bootstrap' => [
        //...
        'modules\config\Bootstrap',
    ],
    'modules' => [
        'config' => [
            'class' => 'modules\config\Module',
        ],
        //...
    ], 
    //...       
];

```
В frontend части подключаем только поведение для применения наших параметров
```
// frontend\config\main.php

$config = [    
    // Подключаем поведение для замены параметров конфигурации нашими параметрами
    'as beforeConfig' => [
        'class' => '\modules\config\components\behaviors\ConfigBehavior',        
    ],
    //...    
];

```
Подключение закончено.
### Настройка

Далее следует задать параметры которые будем хранить и изменять.

Все параметры задаются в классе [[modules\config\params\Params]]. Данный класс наследуется от 
[[modules\config\params\ConfigParams]] который реализует интерфейс [[modules\config\components\interfaces\ConfigInterface]].

Пример класса Params
```
<?php

namespace backend\models;

use Yii;
use modules\config\params\ConfigParams;

class Params extends ConfigParams
{
    /**
     * @return array
     */
    public static function findParams()
    {
        return [
            [
                'param' => 'SITE_NAME',
                'label' => 'Site Name',
                'value' => '',
                'type' => self::FIELD_TYPE_STRING,
                'default' => 'My Site',
            ],
            [
                'param' => 'SITE_TIME_ZONE',
                'label' => 'Timezone',
                'value' => '',
                'type' => self::FIELD_TYPE_STRING,
                'default' => 'Europe/Moscow',
            ],
            [
                'param' => 'SITE_LANGUAGE',
                'label' => 'Language',
                'value' => '',
                'type' => self::FIELD_TYPE_STRING,
                'default' => 'ru',
            ]
        ];
    }
}
```
Что бы подключить данный класс, следует указать его в конфигурации при подключении модуля.
Если не указывать свой класс, то параметры будут браться из класса модуля [[modules\config\params\Params]].
```
$config = [
    //...
    'modules' => [
        'config' => [
            'class' => 'modules\config\Module',
            'params' => [
                'paramsClass' => 'backend\models\Params'
            ],
        ],
        //...
    ], 
    //...       
];

```
После этого необходимо сохранить данные в базу данных с помощью консольноых команд:
```
php yii config/init/down
php yii config/init/up
```
или одной командой
```
php yii config/init/update
```
Подробнее о консольных командах написано ниже.

### Компонент
Присваиваем значение параметру:
```
\Yii::$app->config->set('SITE_NAME', 'Мой сайт');
```
Получаем значение параметра:
```
\Yii::$app->config->get('SITE_NAME');
```
Удаляем значение параметра:
```
\Yii::$app->config->delete('SITE_NAME');
```
### Поведение
В поведении [[\modules\config\components\behaviors\ConfigBehavior]] присваиваются наши значения тем что прописаны в конфигурации,
поэтому если требуется изменить какое либо значение из конфигурации нашим, то необходимо так же прописать его и в поведении.
В текущем исполнении заданы базовые параметры, такие как name, language, timeZone.
Для добавления своих параметров можно создать своё поведение и подключить его вместо [[\modules\config\components\behaviors\ConfigBehavior]].

Для того что бы не писать всё время присваивание значений в поведении, можно поступить следующим образом,
в класс Params, в котором мы задаём параметры, добавить метод getReplace()
```
<?php

namespace backend\models;

use Yii;
use modules\config\params\ConfigParams;

class Params extends ConfigParams
{
    /**
     * @return array
     */
    public static function findParams()
    {
        return [
            [
                'param' => 'SITE_NAME',
                'label' => 'Site Name',
                'value' => '',
                'type' => self::FIELD_TYPE_STRING,
                'default' => 'My Site',
            ],
            [
                'param' => 'SITE_TIME_ZONE',
                'label' => 'Timezone',
                'value' => '',
                'type' => self::FIELD_TYPE_STRING,
                'default' => 'Europe/Moscow',
            ],
            [
                'param' => 'SITE_LANGUAGE',
                'label' => 'Language',
                'value' => '',
                'type' => self::FIELD_TYPE_STRING,
                'default' => 'ru',
            ],
            [
                'param' => 'ADMIN_EMAIL',
                'label' => 'Email administrator',
                'value' => '',
                'type' => self::FIELD_TYPE_STRING,
                'default' => 'admin@example.loc',
            ],
            [
                'param' => 'SUPPORT_EMAIL',
                'label' => 'Email support',
                'value' => '',
                'type' => self::FIELD_TYPE_STRING,
                'default' => 'support@example.loc',
            ],
        ];
    }
    
    /**
     * Ассоциируем ключи конфига с нашими параметрами для замены
     * @return array
     */
    public static function getReplace()
    {
        return [
            'name' => 'SITE_NAME',
            'timeZone' => 'SITE_TIME_ZONE',
            'language' => 'SITE_LANGUAGE',
            'adminEmail' => 'ADMIN_EMAIL',
            'supportEmail' => 'SUPPORT_EMAIL',
        ];
    }
}

```
и модифицировать наше поведение следующим образом:
```
<?php

namespace common\components\behaviors;

use Yii;
use yii\base\Behavior;
use yii\base\Controller;
use yii\helpers\ArrayHelper;
use yii\web\Application;
use backend\models\Params;

/**
 * Class ConfigBehavior
 * @package common\components\behaviors
 */
class ConfigBehavior extends Behavior
{
    /**
     * @var \modules\config\params\Params
     */
    public $paramsClass = '\modules\config\params\Params';
        
    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            Controller::EVENT_BEFORE_ACTION => 'beforeAction'
        ];
    }

    /**
     * Set config
     */
    public function beforeAction()
    {
        /** @var Application $app */
        $app = $this->owner;
        $this->setParams($app);
    }

    /**
     * Set params
     * @param Application $app
     */
    private function setParams(Application $app)
    {
        $array = Yii::$app->config->getAll();
        $replace = $this->paramsClass::getReplace();
        foreach ($replace as $key => $value) {
            if (isset($app->{$key})) {
                if ($key == 'language' && YII_ENV_TEST) {
                    $app->{$key} = $app->language;
                } else {
                    $app->{$key} = ArrayHelper::getValue($array, $value);
                }
            }
            if (isset($app->params[$key])) {
                $app->params[$key] = ArrayHelper::getValue($array, $value);
            }
        }
    }
}

```
Теперь параметры и ассоциации можно определять только в одном классе Params, всё остальное автоматически выполнит наше поведение.
> Примечание: Данное поведение было добавлено [[modules\config\components\behaviors\ConfigAdvancedBehavior]]
```
$config = [    
    // Подключаем поведение для замены параметров конфигурации нашими параметрами
    'as beforeConfig' => [
        'class' => '\modules\config\components\behaviors\ConfigAdvancedBehavior',
        //'paramsClass'  => '\backend\models\Params',       
    ],
    //...    
];
```

### Консольные команды
Для заполнения базы данных параметрами определенными в классе Params
```
php yii config/init/up
```
Для удаления параметров
```
php yii config/init/down
```
Для обновления параметров
```
php yii config/init/update

```
В этой команде совмещены две предыдущие, down и up
> Примечание: Для вступления в силу заданных параметров, данные команды следует запускать каждый раз когда изменяется класс Params. 

### Ссылка на редактирование в backend
```
<?= \yii\helpers\Url::to(['/config/default/update']) ?>
```
### Свой вид
Для изменения вида формы редактирования идущей в комплекте с модулем, можно воспользоваться темизацией.

Настроим компонент view приложения для темизации
```
// backend\config\main.php

$current_theme = 'default'; // тема
$config = [
    //...
    'components' => [            
        'view' => [
            'theme' => [
                'basePath' => '@app/themes/' . $current_theme,
                'baseUrl' => '@app/themes/' . $current_theme,
                'pathMap' => [
                    '@app/views' => '@app/themes/' . $current_theme . '/views',
                    '@modules' => '@app/themes/' . $current_theme . '/modules',
                ],
            ],
        ],
        //...
     ],
    //...       
];

```
В backend части создаём папки следующей структуры и два файла.
```
\backend
    themes
        default
            modules
                config
                    views
                        default
                            _form.php
                            update.php

```
Файлы можно скопировать из модуля и изменить по своему желанию.

После проделаных манипуляций, файлы вида модуля теперь будут браться из установленной нами темы, default.
### Тестирование
При выполнении тестов приложения, выполните миграцию в тестовую базу
```
php yii_test migrate/up -p=@modules/config/migrations
```
Что бы включить модуль в процесс тестирования всего приложения, отредактируйте файл codeception.yml в корне приложения, добавив туда модуль.
```
# global codeception file to run tests from all apps
include:
    - common
    - frontend
    - backend
    - api
    - modules/config
paths:
    log: console/runtime/logs
settings:
    colors: true
```
и выполните в корне приложения следующие консольные команды
 
для Windows:
```
vendor\bin\codecept build
vendor\bin\codecept run
```
для остальных систем:
```
vendor/bin/codecept build
vendor/bin/codecept run
``` 

## Лицензия
The MIT License (MIT). Please see [License File](https://github.com/Dominus77/yii2-config-db/blob/master/LICENSE.md) for more information.
