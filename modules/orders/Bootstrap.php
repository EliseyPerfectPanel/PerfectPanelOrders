<?php

namespace orders;

use yii;
use yii\base\BootstrapInterface;



class Bootstrap implements BootstrapInterface
{
    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        Yii::$app->urlManager->enablePrettyUrl = true;
        Yii::$app->urlManager->showScriptName = false;

        $app->getUrlManager()->addRules(
            [
                'GET /orders' => 'orders/orders/index',
                'GET /orders/csv' => 'orders/orders/csv',
            ]
        );
    }
}