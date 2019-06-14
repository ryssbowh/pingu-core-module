<?php

namespace Pingu\Core\Contracts;

use Pingu\Core\Contracts\Renderable;

interface RenderableWithSuggestions extends Renderable
{
	public function getViewSuggestions();
}