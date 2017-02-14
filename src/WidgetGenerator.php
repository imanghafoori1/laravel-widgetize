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
    protected $signature = 'make:widget	{name : The name of the widget class}';

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
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
       
		return __DIR__.'/../stubs/widget.stub';
       
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
        return 'App\\Widgets';
    }
	
}