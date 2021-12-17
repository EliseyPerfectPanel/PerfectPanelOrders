<?php

namespace orders\models\search;

use Exception;
use orders\models\Orders;
use orders\models\Services;
use orders\models\Users;
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
     * @var int
     */
    public $service_id;
    /**
     * @var int
     */
    public $mode;
    /**
     * @var int
     */
    public $status;


    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['mode'], 'in', 'range' => array_keys(Orders::modeLabels())],
            [['status'], 'in', 'range' => array_keys(Orders::statusLabels())],
            [['status', 'mode', 'service_id'], 'integer'],
        ];
    }

    /**
     * Return available params for generating url in widgets or anywere
     * @return array
     */
    public static function availableGetParams(): array
    {
        $searchFormVars = get_class_vars('orders\models\search\SearchForm');
        return ArrayHelper::merge(
            array_keys($searchFormVars),
            [
                'service_id',
                'mode',
                'status'
            ]
        );
    }

    /**
     * Link for widgets that remove all not available params in $_GET
     * @return array
     */
    public function getWidgetUrl(): array
    {
        return ArrayHelper::merge(
            ['/orders/orders/index'],
            array_intersect_key($this->params, array_flip(self::availableGetParams()))
        );
    }

    public function getDownloadLink(): array
    {
        return ArrayHelper::merge(
            ['/orders/orders/csv'],
            array_intersect_key($this->params, array_flip(self::availableGetParams()))
        );
    }

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
        if (!$this->load($this->params, '') || !$this->validate()) {
            return (new Query());
        }

        $allOrders = (new Query())
            ->select([
                'o.*',
                's.name',
                'username' => 'CONCAT(u.first_name, " ", u.last_name)',
            ])
            ->from([new Expression(Orders::tableName() . ' o FORCE INDEX (PRIMARY)')]);
        $allOrders->leftJoin(Users::tableName() . ' u', 'u.id = o.user_id');
        $allOrders->leftJoin(Services::tableName() . ' s', 's.id = o.service_id');

        $filter = [
            'o.service_id' => $this->service_id,
            'o.mode' => $this->mode,
            'o.status' => $this->status
        ];
        $allOrders->andFilterWhere($filter);

        $searchForm = new SearchForm();
        $searchForm->load($this->params);
        if ($searchForm->validate()) {
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
        } else {
            $searchForm->search_type = '';
            $searchForm->search_string = '';
        }

        $allOrders->orderBy('o.id DESC');
        return $allOrders;
    }


    /**
     *
     * @return ActiveDataProvider
     * @throws Exception
     */
    public function orders(): ActiveDataProvider
    {
        $orders = $this->getFilteredOrders();
        if (!empty($orders->select)) {
            return new ActiveDataProvider([
                'query' => $orders,
                'pagination' => [
                    'pageSize' => ArrayHelper::getValue($this->params, 'pageSize', 100)
                ],
            ]);
        } else {
            return (new ActiveDataProvider());
        }
    }

    /**
     * Generate items for Menu::widget. Add one item with searchForm
     * @return array
     */
    public function prepareStatusItems(): array
    {
        return Orders::statusLabels();
    }

    /**
     * Services' widget HTML
     * @return array
     * @throws Exception
     */
    public function prepareServicesWidget(): array
    {
        $services = $this->getAllServicesGrouped($this->getFilteredOrders());
        if (!empty($services)) {
            $links = [];
            $total = 0;
            foreach ($services as $val) {
                $total += $val['co'];
                $links[$val['id']] = '<span class="label-id">' . $val['co'] . '</span> ' . $val['name'];
            }

            return [
                'items' => $links,
                'total' => $total
            ];
        }
        return [];
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
                [
                    'attribute' => 'status',
                    'label' => Yii::t('orders', 'csv.status'),
                    'value' => function ($model) use ($orders) {
                        $labels = $orders::statusLabels();
                        return $labels[$model['status']] ?? 'N/A';
                    }
                ],
                [
                    'attribute' => 'mode',
                    'label' => Yii::t('orders', 'csv.mode'),
                    'value' => function ($model) use ($orders) {
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
        $exporter->export()->send('Orders-' . date('Y.m.d-H:i:s') . '.csv');
    }

    /**
     * Get all uniq services based on filtering query
     * @param $ordersQuery
     * @return array
     */
    public function getAllServicesGrouped($ordersQuery): array
    {
        if (empty($ordersQuery->select)) {
            return [];
        }
        $ordersQuery->select([
            'co' => 'COUNT(o.service_id)',
            'name' => 's.name',
            'id' => 'o.service_id'
        ]);

        $ordersQuery->from(['o' => Orders::tableName()]);
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
        if (!empty($array)) {
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
