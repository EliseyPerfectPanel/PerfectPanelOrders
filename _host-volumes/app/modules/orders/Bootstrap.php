<?php

namespace app\modules\orders;

use yii;
use yii\base\BootstrapInterface;



class Bootstrap implements BootstrapInterface
{
    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        Yii::setAlias('@moduleOrders', __DIR__);
        $app->getUrlManager()->addRules(
            [
                'GET /orders'  => 'orders/orders/index',
            ]
        );
    }
}