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
            'service_id_index',
            'orders',
            'service_id'
        );
        $this->createIndex(
            'order_status',
            'orders',
            'status'
        );
        $this->createIndex(
            'order_mode',
            'orders',
            'mode'
        );
        $this->createIndex(
            'status_mode',
            'orders',
            ['status', 'mode']
        );
        $this->alterColumn('orders', 'id', $this->primaryKey()->unsigned()->notNull());

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex(
            'service_id_index',
            'orders'
        );
        $this->dropIndex(
            'order_status',
            'orders'
        );
        $this->dropIndex(
            'order_mode',
            'orders'
        );
        $this->dropIndex(
            'status_mode',
            'orders'
        );

        //--TODO: add index

        /*$this->dropTable('orders');
        $this->dropTable('services');
        $this->dropTable('users');*/

        return false;
    }

}
