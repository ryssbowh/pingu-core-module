<?php
namespace Pingu\Core\Contracts\Models;

interface HasContextualLinksContract
{
	public function getContextualLinks(): array;
}