<?php

namespace sys;

use app\configs\View as __view;

class View
{

    public function page($path)
    {
        return $this->render('pages/' . $path);
    }

    public function request($path)
    {
        return $this->render('requests/' . $path);
    }

    public function template($subj, $path, $data = null)
    {
        return $this->render('templates/' . $subj . '/' . $path, [$subj => $data]);
    }

    public function layout($path = __view::layout__)
    {
        include app__ . '/views/layouts/' . $path . '.php';

        exit();
    }

    public function render($path, $data = null)
    {
        ob_start();

        if ($data) {
            extract($data);
        }
        include app__ . '/views/' . $path . '.php';

        return ob_get_clean();
    }

}
