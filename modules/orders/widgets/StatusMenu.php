<?php

namespace orders\widgets;

/*
 * Widget for status menu :)
 */


use yii;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\base\Exception;

class StatusMenu extends Widget
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
     * @var string parameter to add to the link as $_GET
     */
    public $addGetParam;
    /**
     * @var string Label for default title. For the first item
     */
    public $allTitle;
    /**
     * @var Model Model with form
     */
    public $form;
    /**
     * @var array Options form Widget Menu
     */
    public $options;
    /**
     * @var array Precooked url
     */
    public $url;
    /**
     * @var array Url For use in form
     */
    public $downloadLink;


    /**
     * @throws Exception
     */
    public function init()
    {
        parent::init();
        if (!isset($this->allTitle)) {
            $this->allTitle = Yii::t('orders', 'widgets.default.label.all');
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
            $links = [];
            $links[] = [
                'label' => Yii::t('orders', $this->allTitle),
                'url' => $this->url,
                //-- remove active trail from first link
                'active' => !empty($this->addGetParam) ? function () {
                    return !(yii::$app->request->get($this->addGetParam) !== null);
                } : null
            ];

            foreach ($this->items as $key => $val) {
                $links[] = [
                    'label' => $val,
                    'url' => ArrayHelper::merge(
                        $this->url,
                        !empty($this->addGetParam) ? [$this->addGetParam => $key] : null
                    )
                ];
            }

            if (!empty($this->form)) {
                $links[] = [
                    'template' => yii::$app->view->render(
                        '@orders/views/orders/_search',
                        [
                            'model' => $this->form,
                            'downloadLink' => $this->downloadLink
                        ]
                    ),
                    'options' => [
                        'class' => 'pull-right custom-search'
                    ]
                ];
            }

            return $this->render('statusMenu', [
                'id' => $id,
                'items' => $links,
                'options' => $this->options
            ]);
        } else {
            return '';
        }
    }
}