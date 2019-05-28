<?php
namespace Pingu\Core\Contracts;

interface APIableModel
{
	/**
	 * api route for index requests
	 * @return string
	 */
	public static function apiIndexUri();

	/**
	 * api route for patch requests
	 * @return string
	 */
	public static function apiPatchUri();

	/**
	 * api route for create requests
	 * @return string
	 */
	public static function apiCreateUri();

	/**
	 * api route for store requests
	 * @return [type] [description]
	 */
	public static function apiStoreUri();

	/**
	 * api route for update requests
	 * @return string
	 */
	public static function apiEditUri();

	/**
	 * api route for edit requests
	 * @return string
	 */
	public static function apiUpdateUri();

	/**
	 * api route for delete requests
	 * @return string
	 */
	public static function apiDeleteUri();

}