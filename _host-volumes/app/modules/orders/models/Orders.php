<?php

namespace app\modules\orders\models;

use Yii;
use yii\db\Query;
use yii\db\Expression;
use app\modules\orders\controllers\OrdersSearch;

/**
 * This is the model class for table "orders".
 *
 * @property int $id
 * @property int $user_id
 * @property string $link
 * @property int $quantity
 * @property int $service_id
 * @property int $status 0 - Pending, 1 - In progress, 2 - Completed, 3 - Canceled, 4 - Fail
 * @property int $created_at
 * @property int $mode 0 - Manual, 1 - Auto
 */
class Orders extends \yii\db\ActiveRecord
{
    /**
     * Possible status keys
     * @return array
     */
    public static function statusLabels()
    {
        return [
            0 => Yii::t('app', 'Pending'),
            1 => Yii::t('app', 'In progress'),
            2 => Yii::t('app', 'Completed'),
            3 => Yii::t('app', 'Canceled'),
            4 => Yii::t('app', 'Fail')
        ];
    }

    /**
     * Possible mode keys
     * @return array
     */
    public static function modeLabels()
    {
        return [
            0 => Yii::t('app', 'Manual'),
            1 => Yii::t('app', 'Auto'),
        ];
    }

    /**
     * Join table users
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasOne(Users::class, ['id' => 'user_id']);
    }

    /**
     * Join table services
     * @return \yii\db\ActiveQuery
     */
    public function getServices()
    {
        return $this->hasOne(Services::class, ['id' => 'service_id']);
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'orders';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'link', 'quantity', 'service_id', 'status', 'created_at', 'mode'], 'required'],
            [['user_id', 'quantity', 'service_id', 'status', 'created_at', 'mode'], 'integer'],
            [['link'], 'string', 'max' => 300],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'link' => Yii::t('app', 'Link'),
            'quantity' => Yii::t('app', 'Quantity'),
            'service_id' => Yii::t('app', 'Service ID'),
            'status' => Yii::t('app', '0 - Pending, 1 - In progress, 2 - Completed, 3 - Canceled, 4 - Fail'),
            'created_at' => Yii::t('app', 'Created At'),
            'mode' => Yii::t('app', '0 - Manual, 1 - Auto'),
        ];
    }

    /**
     * Return query with filtered result
     * @param $ordersSearchModel
     * @return Query
     */
    public function getFilteredOrders(&$ordersSearchModel){
        $request = Yii::$app->request;

/*        $allOrders = $this->find()
            ->joinWith('users u')
            ->joinWith('services s');*/
        $allOrders = (new \yii\db\Query)
            ->select([
                'o.*',
                's.name',
                'u.first_name',
                'u.last_name'
            ])
//            ->from(['o' => 'orders']);
            ->from([new Expression('{{%orders}} o FORCE INDEX (PRIMARY)')]);
        $allOrders->leftJoin('users u', 'u.id = o.user_id');
        $allOrders->leftJoin('services s', 's.id = o.service_id');
        

        $filter = [
            'o.service_id' => $request->get('service_id'),
            'o.mode'       => $request->get('mode'),
            'o.status'     => $request->get('status')
        ];

        $ordersSearchModel->load(Yii::$app->request->get());
        if($ordersSearchModel->validate()) {
            switch ($ordersSearchModel->search_type) {
                case 'order_id':
                    $allOrders->andWhere(['o.id' => $ordersSearchModel->search_string]);
                    break;
                case 'link':
                    $allOrders->andWhere(['like', 'o.link', $ordersSearchModel->search_string]);
                    break;
                case 'username':
                    //--TODO: проверить группировку ОР
                    $allOrders->orWhere(['like', 'u.first_name', $ordersSearchModel->search_string]);
                    $allOrders->orWhere(['like', 'u.last_name', $ordersSearchModel->search_string]);
                    $allOrders->orWhere(['like', 'CONCAT(u.first_name, " ", u.last_name)', $ordersSearchModel->search_string]);
                    break;
            }
        }
        $allOrders->andFilterWhere($filter);
        $allOrders->orderBy('o.id DESC');
//        var_dump($allOrders->createCommand()->rawSql);exit;

        return $allOrders;
    }


}
