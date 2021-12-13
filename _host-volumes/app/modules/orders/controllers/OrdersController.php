<?php

namespace app\modules\orders\controllers;

use yii;
use app\modules\orders\models\Orders;
use app\modules\orders\models\search\OrdersSearch;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\filters\VerbFilter;

/**
 * Main controler for Test task
 */
class OrdersController extends Controller
{
    public function init(){
        parent::init();
        $this->layout = "index-layout";
        $this->viewPath = '@moduleOrders/views/orders';
    }

    /**
     * Lists all Orders models.
     * @return mixed
     */
    public function actionIndex()
    {

        $ordersSearchModel = new OrdersSearch();
        $orders = new Orders();
        //-- передача параметров во вью в глобальную видимость
        Yii::$app->getView()->params['statusLabels'] = $orders::statusLabels();
        Yii::$app->getView()->params['modeLabels'] = $orders::modeLabels();

        $allOrders = $ordersSearchModel->getFilteredOrders();
        //-- Download  -------------------------------------
        if(yii::$app->request->get('download')){
            $ordersSearchModel->getCsv();
        }

        //-- Data for widget with services
        $servicesLabels = $ordersSearchModel->getAllServisesGroupped(clone $allOrders);

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
}
