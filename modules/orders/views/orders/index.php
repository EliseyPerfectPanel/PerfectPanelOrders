<?php

use orders\widgets\DropdownWidget;
use orders\widgets\StatusMenu;
use yii\grid\GridView;
use orders\models\Orders;

/**
 * @var array $statusMenuItems Items for submenu
 * @var array $orders DataProvider with filtered orders
 * @var string $servicesWidget HTML of services column widget
 * @var string $modeWidget HTML of mode column widget
 * @var \yii\base\Model $searchForm
 * @var array $url Base url for all Items widgets with params
 * @var array $downloadLink Download Link
 */
?>
<?= StatusMenu::widget([
    'options' => ['class' => 'nav nav-tabs p-b'],
    'items' => $statusMenuItems,
    'allTitle' => 'orders.widgets.status.label.all',
    'form' => $searchForm,
    'addGetParam' => 'status',
    'url' => ['/orders/orders/index'],
    'downloadLink' => $downloadLink
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
                'label' => !empty($servicesItems) ? DropdownWidget::widget([
                    'label' => Yii::t('orders', 'models.search.orderssearch.label.service'),
                    'items' => $servicesItems['items'],
                    'url' => $url,
                    'addGetParam' => 'service_id',
                    'allTitle' => Yii::t(
                            'orders',
                            'models.search.orderssearch.all'
                        ) . ' (' . $servicesItems['total'] . ')'
                ]) : '',
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
                    return $statusMenuItems[$model['status']] ?? 'N/A';
                }
            ],
            [
                'attribute' => 'mode',
                'label' => DropdownWidget::widget([
                    'label' => Yii::t('orders', 'models.search.orderssearch.label.mode'),
                    'items' => Orders::modeLabels(),
                    'url' => $url,
                    'addGetParam' => 'mode',
                    'allTitle' => Yii::t('orders', 'models.search.orderssearch.mode.all')
                ])
                ,
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
