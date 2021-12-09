<?php

namespace app\modules\orders\models;


use yii;
use yii\db\Expression;
use yii\db\Query;

class OrdersSearch extends \yii\base\Model
{
    public $search_type;
    public $search_string;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['search_type', 'search_string'], 'string'],
            [['search_type', 'search_string'], 'required'],
        ];
    }

    /**
     * Return query with filtered result
     * @param $ordersSearchModel
     * @return Query
     */
    public function getFilteredOrders(){
        $request = Yii::$app->request;

        $allOrders = (new \yii\db\Query)
            ->select([
                'o.*',
                's.name',
                'u.first_name',
                'u.last_name'
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
                    $allOrders->orWhere(['like', 'u.first_name', $this->search_string]);
                    $allOrders->orWhere(['like', 'u.last_name', $this->search_string]);
                    $allOrders->orWhere(['like', 'CONCAT(u.first_name, " ", u.last_name)', $this->search_string]);
                    break;
            }
        }
        $allOrders->andFilterWhere($filter);
        $allOrders->orderBy('o.id DESC');

        return $allOrders;
    }

    /**
     * @return array
     * Get all uniq services
     */
    public function getAllServisesGroupped(yii\db\Query $ordersQuery)
    {
        $ordersQuery->select([
            'COUNT(*)',
        ]);
        $ordersQuery->from(['o' => 'orders']);
        $ordersQuery->andWhere('o.service_id = sw.id');

//        var_dump($ordersQuery->createCommand()->rawSql);exit;
        

        $query = (new \yii\db\Query)
            ->select([
                'name'  => 'sw.name',
                'id'    => 'sw.id',
                'co'    => $ordersQuery,
            ])
            ->from(['sw' => 'services'])
            ->having('co > 0')
            ->orderBy('co DESC');

        return $query->all();
    }
}
