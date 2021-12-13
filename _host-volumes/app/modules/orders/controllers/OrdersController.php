<?php

namespace app\modules\orders\controllers;

use yii;
use app\modules\orders\models\Orders;
use app\modules\orders\models\search\OrdersSearch;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;


/**
 * OrdersController implements the CRUD actions for Orders model.
 */
class OrdersController extends Controller
{
    public function init(){
        parent::init();
        $this->layout = "index-layout";
        $this->viewPath = '@moduleOrders/views/orders';
    }
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
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



        //-- data for widget with services
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

    /**
     * Displays a single Orders model.
     * @param int $id ID
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Orders model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Orders();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Orders model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Orders model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Orders model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Orders the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Orders::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('om', 'The requested page does not exist.'));
    }
}
