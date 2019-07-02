<?php
namespace Pingu\Core\Contracts\Models;

use Pingu\Core\Contracts\Models\HasRouteSlugContract;

interface HasAjaxRoutesContract extends HasRouteSlugContract
{
	/**
	 * api route for index requests
	 * @return string
	 */
	public static function ajaxIndexUri();

	/**
	 * api route for patch requests
	 * @return string
	 */
	public static function ajaxPatchUri();

	/**
	 * api route for create requests
	 * @return string
	 */
	public static function ajaxCreateUri();

	/**
	 * api route for store requests
	 * @return [type] [description]
	 */
	public static function ajaxStoreUri();

	/**
	 * api route for update requests
	 * @return string
	 */
	public static function ajaxEditUri();

	/**
	 * api route for edit requests
	 * @return string
	 */
	public static function ajaxUpdateUri();

	/**
	 * api route for delete requests
	 * @return string
	 */
	public static function ajaxDeleteUri();

}