<?php

namespace orders\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 */
class Users extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName() :string
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() :array
    {
        return [
            [['first_name', 'last_name'], 'required'],
            [['first_name', 'last_name'], 'string', 'max' => 300],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() :array
    {
        return [
            'id' => Yii::t('orders', 'models.users.label.id'),
            'first_name' => Yii::t('orders', 'models.users.label.firstname'),
            'last_name' => Yii::t('orders', 'models.users.label.lastname'),
        ];
    }
}
