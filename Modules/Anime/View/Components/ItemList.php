<?php

namespace Modules\Anime\View\Components;

use Illuminate\View\Component;

class ItemList extends Component
{

    public $animes;
    /**
     * Create a new component instance.
     *
     * @return void
     */

    public function __construct($animes)
    {
        $this->animes = $animes;
    }


    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('anime::components.itemlist');
    }
}
