<?php

namespace Pingu\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Notify;
use Pingu\Core\Forms\SettingsForm;
use Pingu\Core\Http\Controllers\BaseController;
use Pingu\Core\Http\Requests\AddSettingRequest;
use Pingu\Core\Http\Requests\SettingsRequest;
use Pingu\Core\Settings\SettingsRepository;
use Pingu\Core\Traits\RendersAdminViews;
use Pingu\Forms\Support\Fields\Submit;
use Pingu\Forms\Support\Form;

class SettingsController extends BaseController
{
    use RendersAdminViews;

    /**
     * Edit action
     * 
     * @param Request $request   
     * @param string  $repository
     * 
     * @return view
     */
    public function edit(Request $request)
    {   
        $repository = \Settings::repository($request->segment(3));
        $form = $repository->editForm(['url' => $this->getUpdateUrl($repository)]);

        return $this->getEditView($repository, $form);
    }

    /**
     * update action
     * 
     * @param SettingsRequest $request 
     * @param string          $repository
     * 
     * @return redirect
     */
    public function update(SettingsRequest $request)
    {
        $repository = \Settings::repository($request->segment(3));
        $validated = $request->validated();
        $settings = collect();
        foreach ($validated as $key => $value) {
            $key = str_replace('_', '.', $key);
            $settings[] = \Settings::set($key, $value);
        }
        Notify::put('success', "Settings have been updated");
        return redirect(adminPrefix().'/settings/'.$repository->name());
    }

    /**
     * Index action
     * 
     * @param Request $request
     * @param string  $repository
     * 
     * @return view
     */
    public function index(Request $request)
    {
        $repository = \Settings::repository($request->segment(3));
        return $this->getIndexView($repository);
    }

    /**
     * Get edit view
     * 
     * @param SettingsRepository $repository
     * @param Form               $form   
     *     
     * @return view
     */
    protected function getEditView(SettingsRepository $repository, Form $form)
    {
        return $this->renderAdminView(
            $this->getEditViewNames($repository), 
            'edit-settings', 
            [
                'form' => $form, 
                'section' => $repository->section()
            ]
        );
    }

    /**
     * Get view names for edit view
     * 
     * @param SettingsRepository $repository
     * 
     * @return array
     */
    protected function getEditViewNames(SettingsRepository $repository): array
    {
        return ['pages.settings.'.$repository->name().'.edit', 'pages.settings.edit'];
    }

    /**
     * Get index view
     * 
     * @param SettingsRepository $repository
     * 
     * @return view
     */
    protected function getIndexView(SettingsRepository $repository)
    {
        return $this->renderAdminView(
            $this->getIndexViewNames($repository),
            'index-settings',
            [
                'repository' => $repository, 
                'canEdit' => \Auth::user()->hasPermissionTo($repository->editPermission()),
                'editUri' => $this->getEditUrl($repository)
            ]
        );
    }

    /**
     * Get view names for index view
     * 
     * @param SettingsRepository $repository
     * 
     * @return array
     */
    protected function getIndexViewNames(SettingsRepository $repository): array
    {
        return ['pages.settings.'.$repository->name().'.index', 'pages.settings.index'];
    }

    /**
     * Get url to edit
     * 
     * @param SettingsRepository $repository
     * 
     * @return string
     */
    protected function getEditUrl(SettingsRepository $repository): string
    {
        return '/admin/settings/'.$repository->name().'/edit';
    }

    /**
     * Get url to update
     * 
     * @param SettingsRepository $repository
     * 
     * @return string
     */
    protected function getUpdateUrl(SettingsRepository $repository): string
    {
        return '/admin/settings/'.$repository->name();
    }
}