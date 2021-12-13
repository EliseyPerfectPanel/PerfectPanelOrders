<?php

namespace app\modules\orders;
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
    public $controllerNamespace = 'app\modules\orders\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        Yii::$app->i18n->translations['om*'] = [
            'class'          => 'yii\i18n\PhpMessageSource',
            'basePath'       => '@moduleOrders/messages',
        ];
        //-- Language from config of module
        Yii::configure($this, require __DIR__ . '/config/config.php');
        yii::$app->language = $this->params['language'];

        //-- Set another version of jQuery for bootstrap.js from test
        Yii::$app->assetManager->bundles = [
            'yii\web\JqueryAsset' => [
                'sourcePath' => null,
                'js' => ['//ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js']
             ]
        ];


    }
}
