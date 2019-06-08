<?php
namespace Pingu\Core\Contracts\Controllers;

interface UsesModelContract
{
	public function getModel(): string;
}