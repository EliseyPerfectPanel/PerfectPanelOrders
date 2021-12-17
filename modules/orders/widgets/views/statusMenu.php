<?php

use yii\widgets\Menu;

/**
 * @var int $id serial of widget
 * @var string $label Main caption
 * @var array $items Items for Menu::widget
 * @var array $options
 */
?>
<?php
echo Menu::widget([
    'options' => $options,
    'items' => $items,
    'encodeLabels' => false
]);
?>