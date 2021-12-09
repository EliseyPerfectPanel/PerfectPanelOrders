<?php

namespace app\modules\orders\controllers;

use yii\base\Model;

class OrdersSearch extends Model
{
    public $search_type;
    public $search_string;

    /**
     * {@inheritdoc}
     */
    //--TODO: добавить проверку на точные значения
    public function rules()
    {
        return [
            [['search_type', 'search_string'], 'string'],
            [['search_type', 'search_string'], 'required'],
        ];
    }
}
