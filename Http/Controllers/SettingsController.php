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
use Pingu\Forms\Support\Fields\Submit;
use Pingu\Forms\Support\Form;

class SettingsController extends BaseController
{
    /**
     * @inheritDoc
     */
    public function edit(Request $request, SettingsRepository $repository)
    {   
        $form = $repository->editForm(['url' => $this->getUpdateUrl($repository)]);

        return $this->getEditView($repository, $form);
    }

    /**
     * @inheritDoc
     */
    public function update(SettingsRequest $request, SettingsRepository $repository)
    {
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
     * @inheritDoc
     */
    public function index(Request $request, SettingsRepository $repository)
    {
        return $this->getIndexView($repository);
    }

    /**
     * @inheritDoc
     */
    protected function getEditView(SettingsRepository $repository, Form $form)
    {
        return view('core::settings.edit')->with(
            ['form' => $form, 'section' => $repository->section()]
        );
    }

    /**
     * @inheritDoc
     */
    protected function getIndexView(SettingsRepository $repository)
    {
        return view('core::settings.list')->with([
            'repository' => $repository, 
            'canEdit' => \Auth::user()->hasPermissionTo($repository->editPermission()),
            'editUri' => $this->getEditUrl($repository)
        ]);
    }

    protected function getEditUrl(SettingsRepository $repository)
    {
        return '/admin/settings/'.$repository->name().'/edit';
    }

    protected function getUpdateUrl(SettingsRepository $repository)
    {
        return '/admin/settings/'.$repository->name();
    }
}