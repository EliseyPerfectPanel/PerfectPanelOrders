<?php

use yii\db\Migration;

/**
 * Class m211202_134750_orders
 */
class m211202_134750_orders extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex(
            'service_id',
            'orders',
            'service_id'
        );
        $this->createIndex(
            'status',
            'orders',
            'status'
        );
        $this->createIndex(
            'mode',
            'orders',
            'mode'
        );
        $this->createIndex(
            'status_mode',
            'orders',
            ['status', 'mode']
        );
        $this->createIndex(
            'mode_serviceId',
            'orders',
            ['mode', 'service_id']
        );
        $this->createIndex(
            'service_id_status',
            'orders',
            ['service_id', 'status']
        );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex(
            'service_id',
            'orders'
        );
        $this->dropIndex(
            'status',
            'orders'
        );
        $this->dropIndex(
            'mode',
            'orders'
        );
        $this->dropIndex(
            'status_mode',
            'orders'
        );
        $this->dropIndex(
            'mode_serviceId',
            'orders'
        );
        $this->dropIndex(
            'service_id_status',
            'orders'
        );


        /*$this->dropTable('orders');
        $this->dropTable('services');
        $this->dropTable('users');*/

        return false;
    }

}
