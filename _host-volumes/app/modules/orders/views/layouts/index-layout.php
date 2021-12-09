<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\modules\orders\assets\DefaultAsset;
use app\assets\AppAsset;
use app\widgets\Alert;
use yii\widgets\Menu;
use yii\bootstrap4\Nav;
use yii\bootstrap4\Html;
use yii\bootstrap4\NavBar;


//AppAsset::register($this);
DefaultAsset::register($this);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
    <?php $this->beginBody() ?>
    <?php
//        NavBar::begin([
//            'options' => [
//                'class' => 'navbar navbar-fixed-top navbar-default',
//            ],
//            'innerContainerOptions' => [
//                    'class' => 'container-fluid'
//            ]
//        ]);

//        echo Nav::widget([
//            'options' => ['class' => 'nav navbar-nav'],
//            'items' => [
//                ['label' => 'Orders', 'url' => ['/orders/orders/index']],
//            ],
//        ]);

//        NavBar::end();
    ?>
    <nav class="navbar navbar-fixed-top navbar-default">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>
            <div class="collapse navbar-collapse" id="bs-navbar-collapse">
                <?= Menu::widget([
                    'options' => ['class' => 'nav navbar-nav'],
                    'items' => [
                    ['label' => Yii::t('app', 'Orders'), 'url' => ['/orders/orders/index']],
                    ],
                ]);?>
            </div>
        </div>
    </nav>
    <div class="container-fluid">
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
