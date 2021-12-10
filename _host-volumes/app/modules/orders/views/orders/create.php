<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\orders\models\Orders */

$this->title = Yii::t('om', 'Create Orders');
$this->params['breadcrumbs'][] = ['label' => Yii::t('om', 'Orders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="orders-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
