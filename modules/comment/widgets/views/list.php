<?php

use modules\comment\models\Comment;

/** @var $this yii\web\View */
/** @var $model Comment */

$nestedArray = $model->toNestedArray();
\yii\helpers\VarDumper::dump($nestedArray, 10, 1);


