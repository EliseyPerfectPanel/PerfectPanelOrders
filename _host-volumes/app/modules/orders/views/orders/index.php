<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Menu;
use app\modules\orders\components\DropdownWidget;

/**
 * @var $servicesLabels array
 * @var $ordersSearchModel Model
 */
/* @var $this yii\web\View */
/* @var $searchModel app\modules\orders\controllers\OrdersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<?php

    //-- StatusWidget
    $links = [];
    $links[] = ['label' => 'All orders', 'url'   => ['/orders/orders/index']];
    foreach (Yii::$app->getView()->params['statusLabels'] as $key => $val){
        $links[] = [
            'label' => $val,
            'url'   => ['/orders/orders/index', 'status' => $key],
            'template' => '<a href="{url}" class="ico ico-about">{label}</a>'
        ];
    }
    //--добавляем форму поиска
    $links[] = [
        'template' => $this->render('_search', ['model' => $ordersSearchModel]),
        'options' => [
            'class' => 'pull-right custom-search'
        ]
    ];

    echo Menu::widget([
        'options' => ['class' => 'nav nav-tabs p-b'],
        'items' => $links,
    ]);

    //-- ServicesWidget
    $servicesLinks = [];
    $total = 0;
    foreach ($servicesLabels as $key => $val){
        $total+=$val['co'];
        $servicesLinks[$val['id']] = '<span class="label-id">'.$val['co'].'</span> '.$val['name'];
    }

?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions'  => ['class' => 'table order-table'],
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
        'summary' => "{begin} to {end} of {totalCount}",

        'columns' => [
            'id',
            [
                'attribute' => 'first_name',
                'label' => Yii::t('app', 'User'),
                'value' => function($model){
                    return HTML::encode($model['first_name'].' '.$model['last_name']);
                }
            ],
            'link',
            'quantity',
            [
                'attribute' => 'services.name',
                'format' => 'raw',
                'label' => DropdownWidget::widget([
                    'label' => Yii::t('app', 'Service'),
                    'items' => $servicesLinks,
                    'url' => ArrayHelper::merge(['/orders/orders/index'], yii::$app->request->get()),
                    'addGetParam' => 'service_id',
                    'allTitle' => 'All ('.$total.')'
                ]),
                'headerOptions' => ['class'=>'dropdown-th'],
                'encodeLabel' => false,
                'value' => function($model){
                    return '<span class="label-id">id:'.$model['service_id'].'</span>'.Yii::t('app', $model['name']);
                }
            ],
            [
                'attribute' => 'status',
                'label' => Yii::t('app', 'Status'),
                'value' => function($model){
                    $labels = Yii::$app->getView()->params['statusLabels'];
                    return isset($labels[$model['status']])?Yii::t('app', $labels[$model['status']]):'N/A';
                }
            ],
            [
                'attribute' => 'mode',
                'label' => DropdownWidget::widget([
                    'items' => Yii::$app->getView()->params['modeLabels'],
                    'label' => Yii::t('app', 'Mode'),
                    'url' => ArrayHelper::merge(['/orders/orders/index'], yii::$app->request->get()),
                    'addGetParam' => 'mode'
                ]),
                'enableSorting' => false,
                'headerOptions' => ['class'=>'dropdown-th'],
                'encodeLabel' => false,
                'value' => function($model){
                    $labels = Yii::$app->getView()->params['modeLabels'];
                    return isset($labels[$model['mode']])?Yii::t('app', $labels[$model['mode']]):'N/A';
                }
            ],
            [
                'attribute' => 'created_at',
                'format' => ['date', 'php:Y.m.d H:i:s']
            ]


        ],
    ]); ?>
