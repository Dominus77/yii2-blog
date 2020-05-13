<?php

use modules\config\params\ConfigParams;

return [
    [
        'param' => 'SITE_NAME',
        'label' => 'Site Name',
        'value' => '',
        'type' => ConfigParams::FIELD_TYPE_STRING,
        'default' => 'Site Name',
    ],
    [
        'param' => 'SITE_TIME_ZONE',
        'label' => 'Timezone',
        'value' => '',
        'type' => ConfigParams::FIELD_TYPE_STRING,
        'default' => 'Europe/Moscow',
    ],
    [
        'param' => 'SITE_LANGUAGE',
        'label' => 'Language',
        'value' => '',
        'type' => ConfigParams::FIELD_TYPE_STRING,
        'default' => 'ru',
    ]
];
