<?php

namespace orders\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "services".
 *
 * @property int $id
 * @property string $name
 */
class Services extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName() :string
    {
        return 'services';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() :array
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 300],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() :array
    {
        return [
            'id' => Yii::t('orders', 'models.services.label.id'),
            'name' => Yii::t('orders', 'models.services.label.name'),
        ];
    }
}
