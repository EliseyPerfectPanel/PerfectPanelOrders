<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model orders\models\search\SearchForm */
/* @var $form yii\widgets\ActiveForm */

?>
<?php $form = ActiveForm::begin([
    'action' => ArrayHelper::merge(
            ['/orders/orders/index'],
            ['status' => yii::$app->request->get('status', null)]
    ),
    'method' => 'get',
    'options' => [
        'class' => 'form-inline'
    ],
    'fieldConfig' => [
        'options' => [
            'tag' => false,
        ],
    ],

]); ?>
    <?= Html::a(
            Yii::t('orders', 'views.orders._search.download'),
            ArrayHelper::merge(
                ['/orders/orders/csv'],
                yii::$app->request->get()
            ),
            ['class' => 'btn btn-success']
    ) ?>
    <div class="input-group">

        <?= $form->field($model, 'search_string', ['template' => "{input}"])
            ->textInput()
            ->input('text', ['placeholder' => Yii::t('orders', 'views.orders._search.search'), 'class' => 'form-control'])
            ->label(false)
        ?>
        <span class="input-group-btn search-select-wrap">
            <?= $form->field($model, 'search_type', ['template' => "{input}"])->dropDownList($model::searchTypeLabels(),['class' => 'form-control search-select'])
                ->label(false)
            ?>
            <?= Html::button('<span class="glyphicon glyphicon-search" aria-hidden="true"></span>', [
                    'type' => 'submit',
                    'class' => 'btn btn-default'
            ]) ?>
        </span>

    </div>
<?php ActiveForm::end(); ?>
