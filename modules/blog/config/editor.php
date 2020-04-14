<?php

use modules\blog\Module;

$plugins = [
    'typograf paste advlist autolink lists link image charmap preview hr anchor pagebreak placeholder',
    'searchreplace wordcount visualblocks visualchars code fullscreen',
    'insertdatetime media nonbreaking save table contextmenu directionality',
    'template paste textcolor colorpicker textpattern imagetools codesample toc noneditable help',
];

$toolbar = [
    'anons' => [
        1 => 'undo redo | pastetext | styleselect | bold italic forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | insert link image media  codesample | template | typograf | code preview help'
    ],
    'content' => [
        1 => 'undo redo |  pastetext | styleselect | bold italic forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | insert link image media  codesample | template | typograf | code preview help'
    ]
];

$templates = [
    'anons' => [
        [
            'title' => Module::t('module', 'Anons'),
            'content' => $this->renderFile('@modules/blog/views/templates/anons.php'),
        ]
    ],
    'content' => [
        [
            'title' => Module::t('module', 'Content'),
            'content' => $this->renderFile('@modules/blog/views/templates/content.php'),
        ],
    ]
];

$content_css = [
    '//fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
    '//www.tinymce.com/css/codepen.min.css',
];


$anons = [
    'menubar' => false,
    'statusbar' => true,
    'theme' => 'modern',
    'skin' => 'lightgray', //charcoal, tundora, lightgray-gradient, lightgray
    'contextmenu' => 'typograf | link image inserttable | cell row column deletetable',
    'plugins' => $plugins,
    'noneditable_noneditable_class' => 'fa',
    'extended_valid_elements' => 'span[class|style]',
    'toolbar1' => $toolbar['anons'][1],
    'image_advtab' => true,
    'templates' => $templates['anons'],
    'content_css' => $content_css,
    'relative_urls' => false, // отключение относительных путей
    'remove_script_host' => false, // отключение обрезки хоста
    'convert_urls' => false,
];

$content = [
    'menubar' => false,
    'statusbar' => true,
    'theme' => 'modern',
    'skin' => 'lightgray', //charcoal, tundora, lightgray-gradient, lightgray
    'contextmenu' => 'typograf | link image inserttable | cell row column deletetable',
    'plugins' => $plugins,
    'noneditable_noneditable_class' => 'fa',
    'extended_valid_elements' => 'span[class|style]',
    'toolbar1' => $toolbar['content'][1],
    'image_advtab' => true,
    'templates' => $templates['content'],
    'content_css' => $content_css,
    'relative_urls' => false, // отключение относительных путей
    'remove_script_host' => false, // отключение обрезки хоста
    'convert_urls' => false,
];

return [
    'anons' => $anons,
    'content' => $content
];
