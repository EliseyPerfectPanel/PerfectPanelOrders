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
        Yii::configure($this, require __DIR__ . '/config/config.php');
        Yii::$app->i18n->translations['om*'] = [
            'class'          => 'yii\i18n\PhpMessageSource',
            'basePath'       => '@moduleOrders/messages',
        ];
        //-- Включаем язык из конфига
        yii::$app->language = $this->params['language'];
    }
}
