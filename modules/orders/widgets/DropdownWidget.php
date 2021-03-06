<?php
/*
 * Widget for generating a drop-down menu
 */

namespace orders\widgets;

use yii;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\base\Exception;

class DropdownWidget extends Widget
{

    /**
     * @var array Items with links
     * Example $items = [
     *     0 => 'Link1',
     *     1 => 'Link2',
     *     ....
     * ]
     */
    public $items;
    /**
     * @var array Base url ['test/test', 'id' => 1]
     */
    public $url;
    /**
     * @var string parameter to add to the link as $_GET
     */
    public $addGetParam;
    /**
     * @var string  Label for dropdown menu
     */
    public $label;
    /**
     * @var string Label for default title. For the first item
     */
    public $allTitle;
    /**
     * @var string If need custom template for link
     * example <a href="{url}" class="ico ico-about">{label}</a>
     */
    public $template_a;

    /**
     * @throws Exception
     */
    public function init()
    {
        parent::init();

        if (empty($this->label)) {
            $this->label = Yii::t('orders', 'widgets.dropdown.label');
        }
        if (!isset($this->allTitle)) {
            $this->allTitle = Yii::t('orders', 'widgets.dropdown.label.all');
        }
        if (empty($this->addGetParam)) {
            throw new Exception('Undefined param addGetParam');
        }
    }


    /**
     * @throws \Exception
     */
    public function run(): string
    {
        static $id = 1;
        $id++;

        if (!empty($this->items) && is_array($this->items)) {
            $linkForFirstItem = $this->url;
            if (isset($this->url[$this->addGetParam])) {
                unset($linkForFirstItem[$this->addGetParam]);
            }
            $links = [];
            $links['all'] = [
                'label' => $this->allTitle,
                'url' => $linkForFirstItem,
                //-- remove active trail from first link
                'active' => !empty($this->addGetParam) ? function () {
                    return !(yii::$app->request->get($this->addGetParam) !== null);
                } : null
            ];
            if (!empty($this->template_a)) {
                ArrayHelper::setValue($links, '0.template', ['template' => $this->template_a]);
            }


            foreach ($this->items as $key => $val) {
                $links['item' . $key] = [
                    'label' => $val,
                    'url' => ArrayHelper::merge(
                        $this->url,
                        !empty($this->addGetParam) ? [$this->addGetParam => $key] : null
                    )
                ];
                if (!empty($this->template_a)) {
                    $links['item' . $key]['template'] = $this->template_a;
                }
            }

            return $this->render('dropdownWidget', [
                'id' => $id,
                'label' => $this->label,
                'items' => $links
            ]);
        } else {
            return '';
        }
    }
}