<?php
/**
 * Добавляем свои стили и т.д. из модуля
 */

namespace orders\assets;

use yii\web\AssetBundle;

class DefaultAsset extends AssetBundle
{
    public $sourcePath = '@orders/assets/default';
    public $css = [
        'css/bootstrap.min.css',
        'css/custom.css'
    ];
    public $js = [
//        'js/jquery.min.js',
        'js/bootstrap.min.js'
    ];

    public $depends = [
        'yii\web\YiiAsset',
    ];
}