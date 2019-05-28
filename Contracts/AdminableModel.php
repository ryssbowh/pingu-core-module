<?php
namespace Pingu\Core\Contracts;

interface AdminableModel
{
	/**
	 * route for index requests
	 * @return string
	 */
	public static function adminIndexUri();

	/**
	 * route for patch requests
	 * @return string
	 */
	public static function adminPatchUri();

	/**
	 * route for create requests
	 * @return string
	 */
	public static function adminCreateUri();

	/**
	 * route for store requests
	 * @return [type] [description]
	 */
	public static function adminStoreUri();

	/**
	 * route for update requests
	 * @return string
	 */
	public static function adminEditUri();

	/**
	 * route for edit requests
	 * @return string
	 */
	public static function adminUpdateUri();

	/**
	 * route for delete requests
	 * @return string
	 */
	public static function adminDeleteUri();

}