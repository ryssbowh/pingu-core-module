<?php
namespace Pingu\Core\Contracts\Models;

interface HasCrudUrisContract extends HasRouteSlugContract
{
	/**
	 * route for index requests
	 * @return string
	 */
	public static function indexUri();

	/**
	 * route for patch requests
	 * @return string
	 */
	public static function patchUri();

	/**
	 * route for create requests
	 * @return string
	 */
	public static function createUri();

	/**
	 * route for store requests
	 * @return string
	 */
	public static function storeUri();

	/**
	 * route for update requests
	 * @return string
	 */
	public static function editUri();

	/**
	 * route for edit requests
	 * @return string
	 */
	public static function updateUri();

	/**
	 * route for delete requests
	 * @return string
	 */
	public static function deleteUri();

	/**
	 * Gets an Uri
	 * 
	 * @param  string      $action
	 * @param  string|null $prefix
	 * @return string
	 */
	public static function getUri(string $action, ?string $prefix = null);

	/**
	 * Transform an uri, replacing slugs with replacements array
	 * 
	 * @param  string      $action
	 * @param  array       $replacements
	 * @param  string|null $prefix
	 * @return string
	 */
	public static function makeUri(string $action, $replacements = [], ?string $prefix = null);

}