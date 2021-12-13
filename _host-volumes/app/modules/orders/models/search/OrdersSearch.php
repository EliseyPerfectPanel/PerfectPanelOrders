<?php

namespace app\modules\orders\models\search;


use yii;
use yii\base\Model;
use yii\db\Expression;
use yii\db\Query;
use yii2tech\csvgrid\CsvGrid;

class OrdersSearch extends Model
{
    public $search_type;
    public $search_string;



    public static function searchTypeLabels(){
        return [
            'order_id' => Yii::t('om', 'Order ID'),
            'link'      => Yii::t('om', 'Link'),
            'username'  => Yii::t('om', 'Username')
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['search_type', 'search_string'], 'required'],
            [['search_string'], 'string'],
            [['search_type'], 'in', 'range' => array_keys(self::searchTypeLabels())],
        ];
    }

    /**
     * Return query with filtered result
     * @param $ordersSearchModel
     * @return Query
     */
    public function getFilteredOrders()
    {
        $request = Yii::$app->request;


        $allOrders = (new Query)
            ->select([
                'o.*',
                's.name',
                'username' =>'CONCAT(u.first_name, " ", u.last_name)',
            ])
            //->from(['o' => 'orders']);
            ->from([new Expression('{{%orders}} o FORCE INDEX (PRIMARY)')]);
        $allOrders->leftJoin('users u', 'u.id = o.user_id');
        $allOrders->leftJoin('services s', 's.id = o.service_id');

        $filter = [
            'o.service_id' => $request->get('service_id'),
            'o.mode'       => $request->get('mode'),
            'o.status'     => $request->get('status')
        ];
        $allOrders->andFilterWhere($filter);

        $this->load(Yii::$app->request->get());
        if($this->validate()) {
            switch ($this->search_type) {
                case 'order_id':
                    $allOrders->andWhere(['o.id' => $this->search_string]);
                    break;
                case 'link':
                    $allOrders->andWhere(['like', 'o.link', $this->search_string]);
                    break;
                case 'username':
                    $allOrders->andFilterWhere([
                        'OR',
                        ['like', 'u.first_name', $this->search_string],
                        ['like', 'u.last_name', $this->search_string],
                        ['like', 'CONCAT(u.first_name, u.last_name)', $this->search_string]
                    ]);
                    break;
            }
        }



        $allOrders->orderBy('o.id DESC');

        return $allOrders;
    }

    public function getCsv(){
        $exporter = new CsvGrid([
            'query' => $this->getFilteredOrders(),
            'batchSize' => 10000, // export batch size,
            'columns' => [
                ['attribute' => 'id'],
                ['attribute' => 'username', 'label' => Yii::t('om', 'User')],
                ['attribute' => 'link', 'label' => Yii::t('om', 'Link')],
                ['attribute' => 'quantity', 'label' => Yii::t('om', 'Quantity')],
                ['attribute' => 'name', 'label' => Yii::t('om', 'Service Name')],
                ['attribute' => 'status', 'label' => Yii::t('om', 'Status'),
                    'value' => function($model){
                        $labels = Yii::$app->getView()->params['statusLabels'];
                        return isset($labels[$model['status']])?Yii::t('om', $labels[$model['status']]):'N/A';
                    }
                ],
                ['attribute' => 'mode', 'label' => Yii::t('om', 'Mode'),
                    'value' => function($model){
                        $labels = Yii::$app->getView()->params['modeLabels'];
                        return isset($labels[$model['mode']])?Yii::t('om', $labels[$model['mode']]):'N/A';
                    }
                ],
                ['attribute' => 'created_at', 'label' => Yii::t('om', 'Created At'), 'format' => ['date', 'php:Y.m.d H:i:s']]
            ],
        ]);
        $exporter->export()->send('Orders-'.date('Y.m.d-H:i:s').'.csv');
    }

    /**
     * @return array
     * Get all uniq services
     */
    public function getAllServisesGroupped($ordersQuery)
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
     * @param $keySerch
     */
    public function arrayHelperRemoveByKey(&$array, $keySerch){
        if(!empty($array)) {
            foreach ($array as $key => &$value) {
                if (is_array($value)) {
                    self::arrayHelperRemoveByKey($value, $keySerch);
                }
                if ($key === $keySerch) {
                    unset($array[$key]);
                }
            }
        }
    }
}
