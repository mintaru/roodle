<?php

namespace App\View\Components;

use Illuminate\View\Component;

class BackButton extends Component
{
    public $url;
    public $text;

    public function __construct($url = '#', $text = 'Back')
    {
        $this->url = $url;
        $this->text = $text;
    }

    public function render()
    {
        return view('components.back-button');
    }
}
