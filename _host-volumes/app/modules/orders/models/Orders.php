<?php

namespace app\modules\orders\models;

use Yii;

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
}
