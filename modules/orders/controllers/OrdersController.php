<?php

namespace orders\controllers;

use Exception;
use orders\models\search\SearchForm;
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

        $searchForm = new SearchForm();
        $searchForm->load(Yii::$app->request->get());

        return $this->render('index', [
            'orders' => $ordersSearchModel->orders(),
            'url' => $ordersSearchModel->getWidgetUrl(),
            'downloadLink' => $ordersSearchModel->getDownloadLink(),
            'searchForm' => $searchForm,
            'statusMenuItems' => $ordersSearchModel->prepareStatusItems(),
            'servicesItems' => $ordersSearchModel->prepareServicesWidget(),
        ]);
    }

    /**
     * Start download CSV file
     * @return bool
     * @throws yii\base\InvalidConfigException
     */
    public function actionCsv()
    {
        $ordersSearchModel = new OrdersSearch();
        $ordersSearchModel->setParams(Yii::$app->request->get());
        return $ordersSearchModel->getCsv();
        return true;
    }
}
