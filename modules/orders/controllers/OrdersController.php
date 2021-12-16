<?php

namespace orders\controllers;

use Exception;
use yii;
use orders\models\search\OrdersSearch;
use yii\web\Controller;

/**
 * Main controller for Test task
 */
class OrdersController extends Controller
{
    /**
     * Lists all Orders models.
     * @return string
     * @throws Exception
     */
    public function actionIndex(): string
    {
        $ordersSearchModel = new OrdersSearch();
        $ordersSearchModel
            ->setParams(Yii::$app->request->get())
            ->setParams(['pageSize' => 100]);

        return $this->render('index', [
            'orders' => $ordersSearchModel->orders(),
            'statusMenuItems' => $ordersSearchModel->prepareStatusItems(),
            'servicesWidget' => $ordersSearchModel->prepareServicesWidget(),
            'modeWidget' => $ordersSearchModel->prepareModeWidget(),
        ]);
    }

    /**
     * Start download CSV file
     * @return bool
     * @throws yii\base\InvalidConfigException
     */
    public function actionCsv(): bool
    {
        $ordersSearchModel = new OrdersSearch();
        $ordersSearchModel->setParams(Yii::$app->request->get());
        $ordersSearchModel->getCsv();
        return true;
    }
}
