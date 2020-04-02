<?php

namespace Pingu\Core\Http\Controllers;

use Pingu\Core\Traits\RendersAdminViews;

class ModuleController extends BaseController
{
    use RendersAdminViews;
    /**
     * List all modules
     *
     * @return view
     */
    public function index()
    {
        $modules = \Module::all();
        $data = [
            'modules' => $modules
        ];
        return $this->renderAdminView('pages.modules.index', 'index-modules', $data);
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
