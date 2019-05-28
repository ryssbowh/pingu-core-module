<?php
namespace Pingu\Core\Contracts;

interface HasContextualLinks
{
	public function getContextualLinks(): array;
}