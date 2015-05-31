<?php

namespace app\modules;

use sys\Loader;
use sys\utils\Helper;

abstract class Nav
{

    use Loader;

    public $nav_id;

    public $nav = [], $items = [];

    abstract protected function items();

    public function get($params = null, $attr = [])
    {
        $this->nav_id = strtolower(Helper::class_name($this));

        $this->items();

        $attr['id'] = $this->nav_id;

        $this->nav['attr'] = Helper::attr($attr);
        $this->nav['params'] = $params;
        $this->nav['items'] = $this->items;

        return $this->nav;
    }

    public function render($template = 'bootstrap')
    {
        return $this->load()->init('sys/view')->view('app/views/templates/nav/' . $template, ['nav' => $this->nav]);
    }

    protected function item($label = null, $path = null, $params = [])
    {
        $count = count($this->items);
        $index = ($count > 0) ? $count + 1 : 0;

        if ($params) {
            foreach ($params as $key => $val) {
                if (is_int($key)) {
                    $this->items[$index]['label'] = $label;
                    $this->items[$index]['path'] = $path;
                    $this->items[$index]['item'][] = $val;
                    continue;
                }
                $this->items[] = ['label' => $label, 'path' => $path, 'item' => $params];
                break;
            }
        } else {
            $this->items[] = ['label' => $label, 'path' => $path, 'item' => $params];
        }
    }

}
