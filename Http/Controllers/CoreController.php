<?php

namespace Pingu\Core\Http\Controllers;

class CoreController extends BaseController
{
    public function home()
    {
        return view('pages.home');
    }
}
