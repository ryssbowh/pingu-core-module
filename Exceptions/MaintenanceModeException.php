<?php

namespace Pingu\Core\Exceptions;

use Illuminate\Contracts\Support\Responsable;

class MaintenanceModeException extends \Illuminate\Foundation\Http\Exceptions\MaintenanceModeException implements Responsable
{
    /**
     * Create a new exception instance.
     *
     * @param                 $time
     * @param null            $retryAfter
     * @param null            $message
     * @param null            $view
     * @param \Exception|null $previous
     * @param int             $code
     */
    public function __construct($time, $retryAfter = null, $message = null, $view = null, \Exception $previous = null, $code = 0)
    {
        parent::__construct($time, $retryAfter, $message, $previous, $code);
        $this->view = config('core.maintenance.view');
    }
    /**
     * Build a response for Laravel to show
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request)
    {
        $headers = array();
        if ($this->retryAfter) {
            $headers = array('Retry-After' => $this->retryAfter);
        }
        // Figure out what view to show them
        // @TODO: fallback to the default 503 page (laravel/framework/src/Illuminate/Foundation/Exceptions/views/503.blade.php)
        $view = view($this->view)->with([
            'message' => $this->message,
            'retryAfter' => $this->retryAfter,
            'willBeAvailableAt' => $this->willBeAvailableAt
        ]);
        return response($view, 503)->withHeaders($headers);
    }
}