<?php
namespace Pingu\Core\Contracts;

interface UsesModel
{
	public function getModel(): string;
}