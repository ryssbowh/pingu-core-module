<?php

namespace Modules\Core\Http\Controllers;

class CoreAdminController extends BaseAdminController
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('home');
    }
}
