<?php

namespace app\modules\orders\components;

use yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\widgets\Menu;
use yii\helpers\ArrayHelper;

class DropdownWidget extends Widget
{
    public $items;
    public $url;
    public $addGetParam;
    public $label;
    public $allTitle;

    public function init()
    {
        parent::init();
        if(!isset($this->allTitle)){
            $this->allTitle = 'All';
        }
    }



    public function run()
    {
        static $id = 1;
        $id++;

        $html = '';
        if(!empty($this->items)){
            $html.='
                <div class="dropdown">
                  <button class="btn btn-th btn-default dropdown-toggle" type="button" id="dropdownMenu'.$id.'" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                    '.Html::encode(Yii::t('app', $this->label)).'
                    <span class="caret"></span>
                  </button>';

            $links = [];

            unset($this->url[$this->addGetParam]);
            $links[] = ['label' => Yii::t('app', $this->allTitle), 'url'   => $this->url];

            foreach ($this->items as $key => $val){
                $links[] = ['label' => $val, 'url' => ArrayHelper::merge($this->url, [$this->addGetParam => $key])];
            }

           $html.= Menu::widget([
               'options' => ['class' => 'dropdown-menu', 'aria-labelledby' => 'dropdownMenu'.$id],
               'items' => $links,
               'encodeLabels' => false
           ]);
           $html.='</div>';
            
        }
        return $html;
    }
}