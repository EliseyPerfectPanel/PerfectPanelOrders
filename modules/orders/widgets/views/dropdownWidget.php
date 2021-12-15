<?php

use yii\helpers\Html;
use yii\widgets\Menu;

/**
 * @var int $id serial of widget
 * @var string $label Main caption
 * @var array $items Items for Menu::widget
 */
?>
<div class="dropdown">
    <button
            class="btn btn-th btn-default dropdown-toggle"
            type="button" id="dropdownMenu<?=$id?>"
            data-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="true">
        <?= Html::encode(Yii::t('orders', $label)) ?>
        <span class="caret"></span>
    </button>
    <?=
        Menu::widget([
            'options' => ['class' => 'dropdown-menu', 'aria-labelledby' => 'dropdownMenu' . $id],
            'items' => $items,
            'encodeLabels' => false
        ]);
    ?>
</div>