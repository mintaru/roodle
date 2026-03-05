<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Modal extends Component
{
    public $name;
    public $show;
    public $focusable;

    public function __construct($name = '', $show = false, $focusable = false)
    {
        $this->name = $name;
        $this->show = $show;
        $this->focusable = $focusable;
    }

    public function render()
    {
        return view('components.modal');
    }
}
