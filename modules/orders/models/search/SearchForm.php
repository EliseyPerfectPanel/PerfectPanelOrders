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

    public const SEARCH_TYPE_ID = 'order_id';
    public const SEARCH_TYPE_LINK = 'link';
    public const SEARCH_TYPE_USERNAME = 'username';

    /**
     * @return array Possible keys for search types
     */
    public static function searchTypeLabels(): array
    {
        return [
            static::SEARCH_TYPE_ID => Yii::t('orders', 'modules.orders.label.order_id'),
            static::SEARCH_TYPE_LINK => Yii::t('orders', 'modules.orders.label.link'),
            static::SEARCH_TYPE_USERNAME => Yii::t('orders', 'modules.orders.label.username')
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['search_string'], 'filter', 'filter' => 'trim', 'skipOnArray' => true],
            [['search_type', 'search_string'], 'required'],
            [['search_string'], 'string'],
            [['search_type'], 'in', 'range' => array_keys($this::searchTypeLabels())],
        ];
    }

    /**
     * Override for pretty names in url (without Class name)
     * {@inheritdoc}
     */
    public function formName(): string
    {
        return '';
    }


}