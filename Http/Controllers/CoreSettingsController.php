<?php

namespace Pingu\Core\Http\Controllers;

use Illuminate\Support\Collection;
use Pingu\Settings\Contracts\SettingsController as SettingsControllerContract;
use Pingu\Settings\Http\Requests\SettingsRequest;
use Pingu\Settings\Traits\SettingsController;
use Route;

class CoreSettingsController extends BaseController implements SettingsControllerContract
{
	use SettingsController;

	/**
	 * @inheritDoc
	 */
	public function getSection()
	{
		return 'core';
	}

	/**
	 * @inheritDoc
	 */
	public function getUpdateRoute()
	{
		return route_by_name('settings.admin.core.edit');
	}

	/**
     * @inheritDoc
     */
    public function afterUpdatingSettings(SettingsRequest $request, Collection $settings)
    {
    	return redirect()->route('settings.admin.core');
    }

    /**
     * @inheritDoc
     */
    public function canEdit()
    {
    	return \Auth::user()->hasPermissionTo('edit core settings');
    }
}
