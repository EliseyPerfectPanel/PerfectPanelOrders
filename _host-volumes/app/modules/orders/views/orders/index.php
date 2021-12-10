<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Menu;
use app\modules\orders\components\DropdownWidget;

/**
 * @var $servicesLabels array
 * @var $ordersSearchModel \app\modules\orders\models\OrdersSearch
 */
/* @var $this yii\web\View */
/* @var $searchModel app\modules\orders\models\OrdersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<?php

    //-- StatusWidget
    $links = [];
    $links[] = [
        'label' => Yii::t('om', 'All orders'),
        'url'   => ['/orders/orders/index'],
        //-- remove active trail from first link
        'active' => function ($item, $hasActiveChild, $isItemActive, $widget){
            return yii::$app->request->get('status')!==NULL?false:true;
        }
    ];
    foreach (Yii::$app->getView()->params['statusLabels'] as $key => $val){
        $links[] = [
            'label'     => $val,
            'url'       => ['/orders/orders/index', 'status' => $key],
            'template'  => '<a href="{url}" class="ico ico-about">{label}</a>'
        ];
    }
    //-- add Search Form in <li>
    $links[] = [
        'template'  => $this->render('_search', ['model' => $ordersSearchModel]),
        'options'   => [
            'class' => 'pull-right custom-search'
        ]
    ];

    echo Menu::widget([
        'options'   => ['class' => 'nav nav-tabs p-b'],
        'items'     => $links,
    ]);

    //-- ServicesWidget. Not translated
    $servicesLinks = [];
    $total = 0;
    foreach ($servicesLabels as $key => $val){
        $total+= $val['co'];
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
        'summary' => Yii::t('om', "{begin} to {end} of {totalCount}"),

        'columns' => [
            'id',
            [
                'attribute' => 'first_name',
                'label' => Yii::t('om', 'User'),
                'value' => function($model){
                    return HTML::encode($model['first_name'].' '.$model['last_name']);
                }
            ],
            ['attribute' => 'link', 'label' => Yii::t('om', 'Link')],
            [
                'attribute' => 'quantity',
                'label' => Yii::t('om', 'Quantity')
            ],
            [
                'attribute' => 'services.name',
                'format' => 'raw',
                'label' => DropdownWidget::widget([
                    'label' => Yii::t('om', 'Service'),
                    'items' => $servicesLinks,
                    'url' => ArrayHelper::merge(['/orders/orders/index'], yii::$app->request->get()),
                    'addGetParam' => 'service_id',
                    'allTitle' => Yii::t('om', 'All').' ('.$total.')'
                ]),
                'headerOptions' => ['class'=>'dropdown-th'],
                'encodeLabel' => false,
                'value' => function($model){
                    return '<span class="label-id">id:'.$model['service_id'].'</span>'.Yii::t('om', $model['name']);
                }
            ],
            [
                'attribute' => 'status',
                'label' => Yii::t('om', 'Status'),
                'value' => function($model){
                    $labels = Yii::$app->getView()->params['statusLabels'];
                    return isset($labels[$model['status']])?Yii::t('om', $labels[$model['status']]):'N/A';
                }
            ],
            [
                'attribute' => 'mode',
                'label' => DropdownWidget::widget([
                    'items' => Yii::$app->getView()->params['modeLabels'],
                    'label' => Yii::t('om', 'Mode'),
                    'url' => ArrayHelper::merge(['/orders/orders/index'], yii::$app->request->get()),
                    'addGetParam' => 'mode'
                ]),
                'enableSorting' => false,
                'headerOptions' => ['class'=>'dropdown-th'],
                'encodeLabel' => false,
                'value' => function($model){
                    $labels = Yii::$app->getView()->params['modeLabels'];
                    return isset($labels[$model['mode']])?Yii::t('om', $labels[$model['mode']]):'N/A';
                }
            ],
            [
                'attribute' => 'created_at',
                'label' => Yii::t('om', 'Created At'),
                'format' => ['date', 'php:Y.m.d H:i:s']
            ]


        ],
    ]); ?>
