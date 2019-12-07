<?php

namespace Pingu\Core\Http\Controllers;

class ModuleController extends BaseController
{
    /**
     * List all modules
     *
     * @return view
     */
    public function index()
    {
        $modules = \Module::all();
        return view('core::modules')->with(
            [
            'modules' => $modules
            ]
        );
    }

    /**
     * install a module
     * 
     * @param  string $moduleName
     * @return redirect
     */
    public function install(string $moduleName)
    {
        $module = \Module::find($moduleName);
        if($module and !$module->get('core')) {
            try{
                \Artisan::call('module:migrate', ['module' => $moduleName, '--seed' => true]);
                \Notify::success($moduleName.' has been installed');
                $module->enable();
            }
            catch(\Exception $e)
            {
                \Notify::danger($moduleName.' could not be installed, please check the logs');
            }
        }
        return redirect()->route('core.admin.modules');
    }

    /**
     * uninstall a module
     * 
     * @param  string $moduleName
     * @return redirect
     */
    public function uninstall(string $moduleName)
    {
        $module = \Module::find($moduleName);
        if($module and !$module->get('core')) {
            try{
                \Artisan::call('module:migrate-rollback', ['module' => $moduleName, '--unseed' => true]);
                \Notify::success($moduleName.' has been uninstalled');
                $module->disable();
            }
            catch(\Exception $e)
            {
                \Notify::danger($moduleName.' could not be uninstalled, please check the logs');
            }
        }
        return redirect()->route('core.admin.modules');
    }
}
