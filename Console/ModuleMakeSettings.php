<?php

namespace Pingu\Core\Console;

use Illuminate\Support\Str;
use Nwidart\Modules\Commands\GeneratorCommand;
use Nwidart\Modules\Support\Config\GenerateConfigReader;
use Nwidart\Modules\Support\Stub;
use Nwidart\Modules\Traits\ModuleCommandTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ModuleMakeSettings extends GeneratorCommand
{
    use ModuleCommandTrait;

    /**
     * The name of argument name.
     *
     * @var string
     */
    protected $argumentName = 'name';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'module:make-settings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate new settings repository for the specified module.';

    public function getDefaultNamespace() : string
    {
        return $this->laravel['modules']->config('paths.generator.settings.path', 'Config/');
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the settings repository.'],
            ['module', InputArgument::OPTIONAL, 'The name of module will be used.'],
        ];
    }

    /**
     * @return mixed
     */
    protected function getTemplateContents()
    {
        $module = $this->laravel['modules']->findOrFail($this->getModuleName());

        Stub::setBasePath(__DIR__ . '/stubs');

        return (new Stub(
            "/modules/settings.stub", [
            'NAMESPACE'    => $this->getClassNamespace($module),
            'CLASS'        => $this->getFileName(),
            'NAME'         => strtolower($this->getSection()),
            'SECTION'      => $this->getSection()
            ]
        ))->render();
    }

    /**
     * @return mixed
     */
    protected function getDestinationFilePath()
    {
        $path = $this->laravel['modules']->getModulePath($this->getModuleName());

        $settingsPath = GenerateConfigReader::read('settings');

        return $path . $settingsPath->getPath() . '/' . $this->getFileName() . '.php';
    }

    private function getSection()
    {
        $name = $this->argument('name');
        return Str::replaceLast('Settings', '', $name);
    }

    /**
     * @return string
     */
    private function getFileName()
    {
        return $this->getSection() . 'Settings';
    }
}