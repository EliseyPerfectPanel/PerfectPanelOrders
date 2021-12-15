<?php

namespace orders\models\search;

use yii;
use yii\base\Model;

class SearchForm extends Model
{
    /**
     * @var string Types of search. Used in search form
     */
    public $search_type;
    /**
     * @var  string Search string. Used in search form
     */
    public $search_string;

    /**
     * @return array Possible keys for search types
     */
    public static function searchTypeLabels(): array
    {
        return [
            'order_id' => Yii::t('orders', 'modules.orders.label.order_id'),
            'link'      => Yii::t('orders', 'modules.orders.label.link'),
            'username'  => Yii::t('orders', 'modules.orders.label.username')
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules() :array
    {
        return [
            [['search_type', 'search_string'], 'required'],
            [['search_string'], 'string'],
            [['search_type'], 'in', 'range' => array_keys($this::searchTypeLabels())],
        ];
    }
}