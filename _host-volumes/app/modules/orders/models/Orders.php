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
            0 => Yii::t('om', 'Pending'),
            1 => Yii::t('om', 'In progress'),
            2 => Yii::t('om', 'Completed'),
            3 => Yii::t('om', 'Canceled'),
            4 => Yii::t('om', 'Fail')
        ];
    }

    /**
     * Possible mode keys
     * @return array
     */
    public static function modeLabels()
    {
        return [
            0 => Yii::t('om', 'Manual'),
            1 => Yii::t('om', 'Auto'),
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
            'id' => Yii::t('om', 'ID'),
            'user_id' => Yii::t('om', 'User ID'),
            'link' => Yii::t('om', 'Link'),
            'quantity' => Yii::t('om', 'Quantity'),
            'service_id' => Yii::t('om', 'Service ID'),
            'status' => Yii::t('om', '0 - Pending, 1 - In progress, 2 - Completed, 3 - Canceled, 4 - Fail'),
            'created_at' => Yii::t('om', 'Created At'),
            'mode' => Yii::t('om', '0 - Manual, 1 - Auto'),
        ];
    }
}
