<?php

namespace Imanghafoori\Widgets;

use Symfony\Component\Console\Input\InputOption;
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
        $this->makeWidgetClass();

        if (! $this->option('plain')) {
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
        $path = $this->getViewPath();

        if ($this->files->exists($path)) {
            $this->error($this->qualifyClass($this->getNameInput()).'View.blade.php - Already exists! (@_@)');

            return;
        }

        $this->files->put($path, $this->getViewStub());

        $this->info(' - '.$this->qualifyClass($this->getNameInput()).'View.blade.php - was created. (^_^)');
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        $stubName = $this->option('plain') ? 'widget_plain' : 'widget';

        return __DIR__."/../stubs/$stubName.stub";
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
        return $rootNamespace.'\\Widgets';
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
     * @return string
     */
    private function getViewPath()
    {
        $name = $this->qualifyClass($this->getNameInput());
        $path = $this->getPath($name);
        $path = str_replace('.php', 'View.blade.php', $path);

        return $path;
    }

    /**
     * Creates the widget class.
     * @return bool
     */
    private function makeWidgetClass()
    {
        $name = $this->qualifyClass($this->getNameInput());

        $path = $this->getPath($name);

        // First we will check to see if the class already exists. If it does, we don't want
        // to create the class and overwrite the user's code. So, we will bail out so the
        // code is untouched. Otherwise, we will continue generating this class' files.
        if ($this->alreadyExists($this->getNameInput())) {
            $this->error($this->qualifyClass($this->getNameInput()).'.php - Already exists (@_@)');

            return false;
        }

        // Next, we will generate the path to the location where this class' file should get
        // written. Then, we will build the class and make the proper replacements on the
        // stub files so that it gets the correctly formatted namespace and class name.
        $this->makeDirectory($path);

        $this->files->put($path, $this->buildClass($name));

        $this->info(' - '.$name.'.php - was created.  (^_^)');
    }

    /**
     * @return string
     */
    private function getViewStub()
    {
        return 'Note that you can reference partials within "App\Widgets" folder like this: @include("Widgets::somePartial") ';
    }
}
