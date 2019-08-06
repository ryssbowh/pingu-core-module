<?php

namespace Pingu\Core\Http\Controllers;

class ModuleController extends BaseController
{
	/**
	 * List all modules
	 * @return view
	 */
	public function index()
	{
		$modules = \Module::all();
		return view('core::modules')->with([
			'modules' => $modules
		]);
	}

	/**
	 * enables a module
	 * @param  string $moduleName
	 * @return redirect
	 */
	public function enable(string $moduleName)
	{
		$module = \Module::find($moduleName);
		if($module and !$module->get('core')){
			$module->enable();
			\Notify::success($moduleName.' has been enabled');
		}
		return redirect()->route('core.admin.modules');
	}

	/**
	 * Disables a module
	 * @param  string $moduleName
	 * @return redirect
	 */
	public function disable(string $moduleName)
	{
		$module = \Module::find($moduleName);
		if($module and !$module->get('core')){
			$module->disable();
			\Notify::success($moduleName.' has been disabled');
		}
		return redirect()->route('core.admin.modules');
	}
}
