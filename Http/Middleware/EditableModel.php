<?php

namespace Pingu\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Pingu\Core\Entities\BaseModel;
use Pingu\Core\Exceptions\ProtectedModel;

class EditableModel
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $slug)
    {
        $model = $request->route()->parameters[$slug];
        if($model and !$model->editable) {
            throw ProtectedModel::forEdition($model);
        }
        return $next($request);
    }
}
