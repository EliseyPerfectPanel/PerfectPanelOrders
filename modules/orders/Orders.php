<?php

namespace orders;

use Yii;
use yii\base\Module;

/**
 * Orders module definition class
 */

class Orders extends Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'orders\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        yii::$app->i18n->translations['orders*'] = [
            'class'          => 'yii\i18n\PhpMessageSource',
            'basePath'       => '@orders/messages',
        ];
        
        yii::configure($this, require __DIR__ . '/config/config.php');
        yii::$app->language = $this->params['language'];
        yii::$app->formatter->datetimeFormat = $this->params['datetimeFormat'];

        //-- Set another version of jQuery for bootstrap.js from test
        yii::$app->assetManager->bundles = [
            'yii\web\JqueryAsset' => [
                'sourcePath' => null,
                'js' => ['//ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js']
             ]
        ];

    }
}
