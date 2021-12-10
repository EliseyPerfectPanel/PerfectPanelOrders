<?php

namespace app\modules\orders\models;


use yii;
use yii\base\Model;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class OrdersSearch extends Model
{
    public $search_type;
    public $search_string;
    public $mode;
    public $status;



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

    public function getCsv($ordersQuery){
        $statusLabels   = Orders::statusLabels();
        $modeLabels     = Orders::modeLabels();

        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="orders-'.date('Y.m.d H:i:s').'.csv"');

        $fp = fopen('php://output', 'w');

        //-- insert label to top
        $row = (new Orders)->attributeLabels();
        $row['user_id'] = Yii::t('om', 'User');
        fputcsv($fp, $row, ';');

        foreach ($ordersQuery->all() as $line) {
            $row = [
                $line['id'],
                $line['username'],
                $line['link'],
                $line['quantity'],
                $line['name'],
                $statusLabels[$line['status']],
                $modeLabels[$line['mode']],
                date('Y.m.d H:i:s', $line['created_at'])
            ];
            fputcsv($fp, $row, ';');
        }
        fclose($fp);
        return '';
    }

    /**
     * @return array
     * Get all uniq services
     */
    public function getAllServisesGroupped($ordersQuery)
    {
/*        $ordersQuery->select([
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
            ->orderBy('co DESC');*/


        $ordersQuery->select([
            'co' => 'COUNT(o.service_id)',
            'name'  => 's.name',
            'id'    => 'o.service_id'
        ]);

        $ordersQuery->from(['o' => 'orders']);
        $ordersQuery->groupBy('o.service_id');

        //--remove service_id for correct widget
        //highlight_string(print_r($ordersQuery->where        ,1));
        self::arrayHelperRemoveByKey($ordersQuery->where, 'o.service_id');
        //highlight_string(print_r($ordersQuery->where        ,1)); exit;

        $ordersQuery->orderBy('co DESC');

        return $ordersQuery->all();
    }

    /**
     * Recursive item killer
     * @param $array
     * @param $keySerch
     */
    public function arrayHelperRemoveByKey(&$array, $keySerch){
        foreach($array as $key => &$value){
            if(is_array($value)){
                self::arrayHelperRemoveByKey($value, $keySerch);
            }
            if($key === $keySerch){
                unset($array[$key]);
            }
        }
    }
}
