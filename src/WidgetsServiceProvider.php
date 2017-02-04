<?php

namespace Imanghafoori\Widgets;


use Illuminate\Support\ServiceProvider;

class WidgetsServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
        $this->loadViewsFrom($this->app->basePath().'/app/Widgets/', 'Widgets');
	}

	/**
	 * Register any application services.
	 *
	 * This service provider is a great spot to register your various container
	 * bindings with the application. As you can see, we are registering our
	 * "Registrar" implementation here. You can add your own bindings too!
	 *
	 * @return void
	 */
	public function register()
	{
	
	}

}
