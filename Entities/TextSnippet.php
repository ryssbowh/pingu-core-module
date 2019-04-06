<?php

namespace Modules\Core\Entities;

class TextSnippet extends BaseModel
{
    protected $fillable = ['name', 'text'];
    public $timestamps = true;
    public $formFields = [
    	'name' => [
    		'label' => 'Name',
    		'type' => 'text',
    		'options' => [null, ['class' => 'test']]
    	],
    	'text' => [
    		'label' => 'Text',
    		'type' => 'text',
    		'options' => [null, ['class' => 'test2']]
    	]
    ];
}
