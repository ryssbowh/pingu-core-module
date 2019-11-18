<?php

namespace Pingu\Core\Support\Database;

use Illuminate\Database\Schema\Blueprint as LaravelBlueprint;

class Blueprint extends LaravelBlueprint
{
    public function revisionnable($table = 'users', $column = 'id')
    {
        $this->unsignedInteger('created_by')->nullable()->index();
        $this->foreign('created_by')->references($column)->on($table)->onDelete('set null');

        $this->unsignedInteger('updated_by')->nullable()->index();
        $this->foreign('updated_by')->references($column)->on($table)->onDelete('set null');

        $this->unsignedInteger('deleted_by')->nullable()->index();
        $this->foreign('deleted_by')->references($column)->on($table)->onDelete('set null');
    }
}