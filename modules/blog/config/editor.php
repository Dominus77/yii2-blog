<?php

use yii\web\JsExpression;
use modules\blog\Module;

$plugins = [
    'typograf advlist autolink lists link image charmap preview hr anchor pagebreak placeholder',
    'searchreplace wordcount visualblocks visualchars code fullscreen',
    'insertdatetime media nonbreaking save table contextmenu directionality',
    'template paste textcolor colorpicker textpattern imagetools codesample toc noneditable help',
];

$toolbar = [
    'anons' => [
        1 => 'undo redo | styleselect | bold italic forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | insert link image media  codesample | template | typograf | code preview help'
    ],
    'content' => [
        1 => 'undo redo | styleselect | bold italic forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | insert link image media  codesample | template | typograf | code preview'
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

$paste_postprocess = new JsExpression("
function (plugin, args) { 
    var headerRule = {
        'br': {
                process: function (node) {
                    var parent = node.parentNode,
                        space = document.createTextNode(' ');
                
                    parent.replaceChild(space, node);
                }
            }
        },
        valid_elements = { 
            'h1': {
                convert_to: 'h2',
                valid_styles: '',
                valid_classes: '',
                no_empty: true,
                valid_elements: headerRule
            },
            'h2,h3,h4': {
                valid_styles: '',
                valid_classes: '',
                no_empty: true,
                valid_elements: headerRule
            },
            'p': {
                valid_styles: 'text-align',
                valid_classes: '',
                no_empty: true
            },
            a: {
                valid_styles: '',
                valid_classes: '',
                no_empty: true,
        
                process: function (node) {
                    var host = 'http://' + window.location.host + '/';
                    if (node.href.indexOf(host) !== 0) {
                        node.target = '_blank';
                    }
                }
            },
            'br': {
                valid_styles: '',
                valid_classes: ''
            },
            'blockquote,b,strong,i,em,s,strike,sub,sup,kbd,ul,ol,li,dl,dt,dd,time,address,thead,tbody,tfoot': {
                valid_styles: '',
                valid_classes: '',
                no_empty: true
            },
            'table,tr,th,td': {
                valid_styles: 'text-align,vertical-align',
                valid_classes: '',
                no_empty: true
            },
            'embed,iframe': {
                valid_classes: ''
            }  
        }; 
    htmlFormatting(args.node, valid_elements); 
}");

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
    'paste_postprocess' => $paste_postprocess,
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
    'paste_postprocess' => $paste_postprocess,
    'relative_urls' => false, // отключение относительных путей
    'remove_script_host' => false, // отключение обрезки хоста
    'convert_urls' => false,
];

return [
    'anons' => $anons,
    'content' => $content
];
