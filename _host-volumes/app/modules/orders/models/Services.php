<?php

namespace app\modules\orders\models;

use yii\helpers\ArrayHelper;
use Yii;

/**
 * This is the model class for table "services".
 *
 * @property int $id
 * @property string $name
 */
class Services extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'services';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 300],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
        ];
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
        //var_dump($ordersQuery->createCommand()->rawSql);exit;
        

        $query = (new \yii\db\Query)
            ->select([
                'name'  => 'sw.name',
                'id'    => 'sw.id',
                'co'    => $ordersQuery,
            ])
            ->from(['sw' => 'services'])
            ->having('co > 0');
        $query->orderBy('co DESC');
        //var_dump($query->createCommand()->rawSql);exit;

        return $query->all();
    }
}
