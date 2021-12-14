<?php

namespace orders\controllers;

use yii;
use orders\models\Orders;
use orders\models\search\OrdersSearch;
use yii\data\ActiveDataProvider;
use yii\web\Controller;

/**
 * Main controller for Test task
 */
class OrdersController extends Controller
{
    /**
     * Lists all Orders models.
     * @return string
     * @throws yii\base\InvalidConfigException
     */
    public function actionIndex(): string
    {

        $ordersSearchModel = new OrdersSearch();
        $orders = new Orders();
        //-- передача параметров во вью в глобальную видимость
        Yii::$app->getView()->params['statusLabels'] = $orders::statusLabels();
        Yii::$app->getView()->params['modeLabels'] = $orders::modeLabels();

        $allOrders = $ordersSearchModel->getFilteredOrders();

        //-- Data for widget with services
        $servicesLabels = $ordersSearchModel->getAllServicesGrouped(clone $allOrders);

        $dataProvider = new ActiveDataProvider([
            'query' => $allOrders,
            'pagination' => [
                'pageSize' => 100
            ],
        ]);

        return $this->render('index', [
            'dataProvider'      => $dataProvider,
            'servicesLabels'    => $servicesLabels,
            'ordersSearchModel' => $ordersSearchModel
        ]);
    }

    public function actionCsv(): bool
    {
        $ordersSearchModel = new OrdersSearch();
        $orders = new Orders();
        //-- передача параметров во вью в глобальную видимость
        Yii::$app->getView()->params['statusLabels'] = $orders::statusLabels();
        Yii::$app->getView()->params['modeLabels'] = $orders::modeLabels();

        $ordersSearchModel->getCsv();
        return true;
    }
}
