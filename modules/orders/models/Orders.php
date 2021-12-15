<?php

namespace orders\models;

use Yii;
use yii\db\ActiveRecord;

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
class Orders extends ActiveRecord
{
    /**
     * Possible status keys
     * @return array
     */
    public static function statusLabels() :array
    {
        return [
            0 => Yii::t('orders', 'models.orders.status.label.pending'),
            1 => Yii::t('orders', 'models.orders.status.label.in_progress'),
            2 => Yii::t('orders', 'models.orders.status.label.completed'),
            3 => Yii::t('orders', 'models.orders.status.label.canceled'),
            4 => Yii::t('orders', 'models.orders.status.label.fail')
        ];
    }

    /**
     * Possible mode keys
     * @return array
     */
    public static function modeLabels() :array
    {
        return [
            0 => Yii::t('orders', 'models.orders.label.dropdown.manual'),
            1 => Yii::t('orders', 'models.orders.label.dropdown.auto'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName() :string
    {
        return 'orders';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() :array
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
    public function attributeLabels() :array
    {
        return [
            'id' => Yii::t('orders', 'models.orders.label.id'),
            'user_id' => Yii::t('orders', 'models.orders.label.user_id'),
            'link' => Yii::t('orders', 'models.orders.label.link'),
            'quantity' => Yii::t('orders', 'models.orders.label.quantity'),
            'service_id' => Yii::t('orders', 'models.orders.label.service_id'),
            'status' => Yii::t('orders', 'models.orders.label.status'),
            'mode' => Yii::t('orders', 'models.orders.label.mode'),
            'created_at' => Yii::t('orders', 'models.orders.label.created_at'),
        ];
    }
}
