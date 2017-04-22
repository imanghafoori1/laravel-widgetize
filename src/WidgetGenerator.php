<?php

namespace Imanghafoori\Widgets;

use Illuminate\Console\GeneratorCommand as LaravelGeneratorCommand;

class WidgetGenerator extends LaravelGeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:widget	{name : The name of the widget class} {--p|plain}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new widget class';

    /**
     * String to store the command type.
     *
     * @var string
     */
    protected $type = 'Widget Class';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        parent::fire();

        if (!$this->option('plain')) {
            $this->createView();
        }
    }

    /**
     * Create a new view file for the widget.
     *
     * return void
     */
    private function createView()
    {
        $path = $this->_getViewPath();

        if ($this->files->exists($path)) {
            $this->error('View already exists!');

            return;
        }

        $this->files->put($path, '');

        $this->info('View created successfully.');
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        $stubName = $this->option('plain') ? 'widget_plain' : 'widget';
        return __DIR__ . "/../stubs/$stubName.stub";
    }

    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     *
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\\Widgets';
    }


    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['plain', null, InputOption::VALUE_NONE, 'No docs on widget class. No view is being created too.'],
        ];
    }

    /**
     * @return mixed|string
     */
    private function _getViewPath()
    {
        $name = $this->qualifyClass($this->getNameInput());
        $path = $this->getPath($name);
        $path = str_replace('.php', 'View.blade.php', $path);
        return $path;
    }
}
