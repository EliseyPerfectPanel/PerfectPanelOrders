<?php

namespace orders\models\search;

use Exception;
use orders\models\Orders;
use orders\widgets\DropdownWidget;
use yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii2tech\csvgrid\CsvGrid;

class OrdersSearch extends Model
{
    /**
     * @var array Params. Example yii::$app->request->get()
     */
    private $params = [];

    /**
     * Set default params
     * @param array $params
     * @return OrdersSearch
     */
    public function setParams(array $params): OrdersSearch
    {
        $this->params = ArrayHelper::merge($this->params, $params);
        return $this;
    }

    /**
     * Return query with filtered result. Used Yii::$app->request for filtering
     * @return Query
     * @throws Exception
     */
    public function getFilteredOrders(): Query
    {
        $allOrders = (new Query())
            ->select([
                'o.*',
                's.name',
                'username' =>'CONCAT(u.first_name, " ", u.last_name)',
            ])
            ->from([new Expression('{{%orders}} o FORCE INDEX (PRIMARY)')]);
        $allOrders->leftJoin('users u', 'u.id = o.user_id');
        $allOrders->leftJoin('services s', 's.id = o.service_id');

        $filter = [
            'o.service_id' => ArrayHelper::getValue($this->params, 'service_id'),
            'o.mode' => ArrayHelper::getValue($this->params, 'mode'),
            'o.status' => ArrayHelper::getValue($this->params, 'status')
        ];

        $allOrders->andFilterWhere($filter);

        $searchForm = new SearchForm();
        $searchForm->load($this->params);
        if($searchForm->validate()) {
            switch ($searchForm->search_type) {
                case 'order_id':
                    $allOrders->andWhere(['o.id' => $searchForm->search_string]);
                    break;
                case 'link':
                    $allOrders->andWhere(['like', 'o.link', $searchForm->search_string]);
                    break;
                case 'username':
                    $allOrders->andFilterWhere([
                        'OR',
                        ['like', 'u.first_name', $searchForm->search_string],
                        ['like', 'u.last_name', $searchForm->search_string],
                        ['like', 'CONCAT(u.first_name, u.last_name)', $searchForm->search_string]
                    ]);
                    break;
            }
        }

        $allOrders->orderBy('o.id DESC');
        return $allOrders;
    }


    /**
     *
     * @return ActiveDataProvider
     * @throws Exception
     */
    public function orders() :ActiveDataProvider
    {
        return new ActiveDataProvider([
            'query' => $this->getFilteredOrders(),
            'pagination' => [
                'pageSize' => ArrayHelper::getValue($this->params, 'pageSize', 100)
            ],
        ]);
    }

    /**
     * Generate items for Menu::widget. Add one item with searchForm
     * @return array
     */
    public function prepareStatusItems():array
    {

        $links = [];
        $links['all'] = [
            'label' => Yii::t('orders', 'models.search.orderssearch.label.all'),
            'url'   => ['/orders/orders/index'],
            //-- remove active trail from first link
            'active' => function () {
                return !(ArrayHelper::getValue($this->params, 'status') !== null);
            }
        ];

        foreach (Orders::statusLabels() as $key => $val){
            $links[$key] = [
                'label'     => $val,
                'url'       => ['/orders/orders/index', 'status' => $key],
                'template'  => '<a href="{url}" class="ico ico-about">{label}</a>'
            ];
        }

        //-- add Search Form in <li>
        $form = new SearchForm();
        $form->load($this->params);
        $links['form'] = [
            'template'  => yii::$app->view->render('@orders/views/orders/_search', ['model' => $form]),
            'options'   => [
                'class' => 'pull-right custom-search'
            ]
        ];

        return $links;
    }

    /**
     * Return Services widget HTML
     * @return string
     * @throws Exception
     */
    public function prepareServicesWidget() :string
    {
        $services = $this->getAllServicesGrouped($this->getFilteredOrders());

        $links = [];
        $total = 0;
        foreach ($services as $val){
            $total+= $val['co'];
            $links[$val['id']] = '<span class="label-id">'.$val['co'].'</span> '.$val['name'];
        }

        return DropdownWidget::widget([
            'label' => Yii::t('orders', 'models.search.orderssearch.label.service'),
            'items' => $links,
            'url' => ArrayHelper::merge(['/orders/orders/index'], $this->params),
            'addGetParam' => 'service_id',
            'allTitle' => Yii::t('orders', 'models.search.orderssearch.all').' ('.$total.')'
        ]);
    }

    /**
     * Return Mode widget HTML
     * @return string
     * @throws Exception
     */
    public function prepareModeWidget() :string
    {
        $items = Orders::modeLabels();
        return DropdownWidget::widget([
            'label' => Yii::t('orders', 'models.search.orderssearch.label.mode'),
            'items' => $items,
            'url' => ArrayHelper::merge(['/orders/orders/index'], $this->params),
            'addGetParam' => 'mode',
            'allTitle' => Yii::t('orders', 'models.search.orderssearch.mode.all')
        ]);
    }

    /**
     * Filtered results export to CSV file and starts download in browser
     * @throws yii\base\InvalidConfigException
     * @throws Exception
     */
    public function getCsv()
    {
        $orders = new Orders();
        $exporter = new CsvGrid([
            'query' => $this->getFilteredOrders(),
            'batchSize' => 10000,
            'columns' => [
                ['attribute' => 'id'],
                ['attribute' => 'username', 'label' => Yii::t('orders', 'csv.username')],
                ['attribute' => 'link', 'label' => Yii::t('orders', 'csv.link')],
                ['attribute' => 'quantity', 'label' => Yii::t('orders', 'csv.quantity')],
                ['attribute' => 'name', 'label' => Yii::t('orders', 'csv.service_name')],
                ['attribute' => 'status', 'label' => Yii::t('orders', 'csv.status'),
                    'value' => function($model) use ($orders){
                        $labels = $orders::statusLabels();
                        return $labels[$model['status']] ?? 'N/A';
                    }
                ],
                ['attribute' => 'mode', 'label' => Yii::t('orders', 'csv.mode'),
                    'value' => function($model) use ($orders){
                        $labels = $orders::modeLabels();
                        return $labels[$model['mode']] ?? 'N/A';
                    }
                ],
                [
                    'attribute' => 'created_at',
                    'label' => Yii::t('orders', 'csv.created_at'),
                    'format' => ['date', 'php:Y.m.d H:i:s']
                ]
            ],
        ]);
        $exporter->export()->send('Orders-'.date('Y.m.d-H:i:s').'.csv');
    }

    /**
     * Get all uniq services based on filtering query
     * @param $ordersQuery
     * @return array
     */
    public function getAllServicesGrouped($ordersQuery) :array
    {
        $ordersQuery->select([
            'co' => 'COUNT(o.service_id)',
            'name'  => 's.name',
            'id'    => 'o.service_id'
        ]);

        $ordersQuery->from(['o' => 'orders']);
        $ordersQuery->groupBy('o.service_id');

        //--remove service_id for correct widget
        self::arrayHelperRemoveByKey($ordersQuery->where, 'o.service_id');
        $ordersQuery->orderBy('co DESC');

        return $ordersQuery->all();
    }

    /**
     * Recursive item killer
     * @param $array
     * @param $keySearch
     */
    public function arrayHelperRemoveByKey(&$array, $keySearch)
    {
        if(!empty($array)) {
            foreach ($array as $key => &$value) {
                if (is_array($value)) {
                    self::arrayHelperRemoveByKey($value, $keySearch);
                }
                if ($key === $keySearch) {
                    unset($array[$key]);
                }
            }
        }
    }
}
