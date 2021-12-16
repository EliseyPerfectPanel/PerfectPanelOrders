<?php

use yii\grid\GridView;
use yii\widgets\Menu;
use orders\models\Orders;

/**
 * @var array $statusMenuItems Items for submenu
 * @var array $orders DataProvider with filtered orders
 * @var string $servicesWidget HTML of services column widget
 * @var string $modeWidget HTML of mode column widget
 */
?>
<?= Menu::widget([
    'options' => ['class' => 'nav nav-tabs p-b'],
    'items' => $statusMenuItems,
]);
?>
<?php
if (!empty($orders->query)) {
    echo GridView::widget([
        'dataProvider' => $orders,
        'tableOptions' => ['class' => 'table order-table'],
        'showFooter' => true,
        'layout' => '
            {items}
            <div class="row">
                <div class="col-sm-8">
                    <nav>{pager}</nav>
                </div>
                <div class="col-sm-4 pagination-counters">
                {summary}
                </div>
            </div>
        ',
        'summary' => Yii::t('orders', "{begin} to {end} of {totalCount}"),
        'columns' => [
            'id',
            [
                'attribute' => 'username',
                'label' => Yii::t('orders', 'views.orders.index.label.user'),
            ],
            ['attribute' => 'link', 'label' => Yii::t('orders', 'views.orders.index.label.link')],
            [
                'attribute' => 'quantity',
                'label' => Yii::t('orders', 'views.orders.index.label.quantity')
            ],
            [
                'attribute' => 'services.name',
                'format' => 'raw',
                'label' => $servicesWidget,
                'headerOptions' => ['class' => 'dropdown-th'],
                'encodeLabel' => false,
                'value' => function ($model) {
                    return '<span class="label-id">id:' . $model['service_id'] . '</span>' . $model['name'];
                }
            ],
            [
                'attribute' => 'status',
                'label' => Yii::t('orders', 'views.orders.index.label.status'),
                'value' => function ($model) use ($statusMenuItems) {
                    return isset($statusMenuItems[$model['status']]) ? $statusMenuItems[$model['status']]['label'] : 'N/A';
                }
            ],
            [
                'attribute' => 'mode',
                'label' => $modeWidget,
                'enableSorting' => false,
                'headerOptions' => ['class' => 'dropdown-th'],
                'encodeLabel' => false,
                'value' => function ($model) {
                    $labels = Orders::modeLabels();
                    return $labels[$model['mode']] ?? 'N/A';
                }
            ],
            [
                'attribute' => 'created_at',
                'label' => Yii::t('orders', 'models.orders.label.created_at'),
                'format' => 'datetime'
            ]


        ],
    ]);
} else {
    echo 'Some Error';
}
?>
