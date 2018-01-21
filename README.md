Laravel Widgetize
=================


![widgetize_header](https://cloud.githubusercontent.com/assets/6961695/24345454/7d5c9e4c-12e5-11e7-8c22-015395dbb796.jpg)

<a href="https://scrutinizer-ci.com/g/imanghafoori1/laravel-widgetize"><img src="https://img.shields.io/scrutinizer/g/imanghafoori1/laravel-widgetize.svg?style=flat-square" alt="Quality Score"></img></a>
[![Latest Stable Version](https://poser.pugx.org/imanghafoori/laravel-widgetize/v/stable)](https://packagist.org/packages/imanghafoori/laravel-widgetize)
[![Build Status](https://travis-ci.org/imanghafoori1/laravel-widgetize.svg?branch=master)](https://travis-ci.org/imanghafoori1/laravel-widgetize)
[![Awesome Laravel](https://img.shields.io/badge/Awesome-Laravel-brightgreen.svg)](https://github.com/chiraggude/awesome-laravel)
[![Daily Downloads](https://poser.pugx.org/imanghafoori/laravel-widgetize/d/monthly)](https://packagist.org/packages/imanghafoori/laravel-widgetize)
[![StyleCI](https://styleci.io/repos/80939052/shield?branch=master)](https://styleci.io/repos/80939052)
[![Code Coverage](https://scrutinizer-ci.com/g/imanghafoori1/laravel-widgetize/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/imanghafoori1/laravel-widgetize/?branch=master)





## :ribbon::ribbon::ribbon: Widgetize : "_cleaner code_" :heavy_plus_sign: "_easy caching_" :ribbon::ribbon::ribbon:

### Built with :heart: for every smart laravel developer


You can reach the maintainer throught telegram IM:

@imanghafoori or imanghafoori1@gmail.com


---------------------



* :flashlight: [Introduction](#introduction)
    - [What is a _widget object_ ?](#what-is-a-widget-object)
    - [When to use the _widget_ concept?](#when-to-use-the-widget-concept)
    - :gem: [Package Features](#gem-package-features-gem)
    - [Sample App](https://github.com/zigstum/laracasts-forum-widgets)
    
* :wrench: [Installation](#wrench-installation-arrow_down)
* :earth_africa: [Global Configuration](#earth_africa-global-config)
* :blue_car: [Per Widget Configuration](#blue_car-per-widget-config)
    - [public $template (optional)](#public-template-string)
    - [public $cacheLifeTime (optional)](#public-cachelifetime-int)
    - [public $cacheTags (optional)](#public-cachetags-array)
    - [public $controller (optional)](#public-controller-string) Advanced
    - [public $presenter (optional)](#public-presenter-string) Advanced
    - [public function extraCacheKeyDependency (optional)](#public-function-extracachekeydependency) Advanced
    - [public function cacheKey (optional)](#public-function-cachekey) Advanced
   
   
* :bulb: [Usage and Example](#bulb-example)
    - [How to make a widget class](#how-to-make-a-widget)
    - [How to use a widget class](#how-to-use-a-widget-class)
   
More readings:
* :shipit: [Some Theory for Experts](#)
    - [Article about widgetize and Solid Design Patterns](https://medium.com/@imanghafoori1/taste-single-responsibility-in-your-laravel-controllers-with-laravel-widgetize-package-9e0800d8b559)
    - [Article about widgetize and Solid Design Patterns](https://medium.com/@imanghafoori1/taste-single-responsibility-in-your-laravel-controllers-with-laravel-widgetize-package-9e0800d8b559)
    - [Article about widgetize and Solid Design Patterns](https://medium.com/@imanghafoori1/taste-single-responsibility-in-your-laravel-controllers-with-laravel-widgetize-package-9e0800d8b559)

* :star: [Your Stars Makes Us Do More](#star-your-stars-make-us-do-more-star)








This page may look long and boring to read at first, but bear with me!!!

I bet if you read through it you won't get disappointed at the end.So let's Go... :horse_racing:


-----------------------

## Overview:

### This package helps you in :
- #### Page Partial Caching
- #### Clean up your Controllers Code
- #### Minify HTML
- #### Easily provide page partials for varnish or nginx for ESI caching


And to show the information you need.. It is :
- #### Integrated with laravel-debugbar package
out of the box ! 



---------------

### Overview:When to use this package?

This concept (this design pattern) really shines when you want to create crowded web pages with multiple sections (on sidebar, menu, carousels ...) and each widget needs separate sql queries and php logic to be provided with data for its template. Anyway installing it has minimal overhead since surprisingly it is just a small abstract class and Of course you can use it to __refactor your monster code and tame it__ into managable pieces or __boost the performance 4x-5x__ times faster! :dizzy:

-----------------

### Overview:What is a widget?

You can think of a widget as a blade partial (which know how to provide data for itself.)

Or If you know `Drupal's Views` concept, they are very similar.

You can include `@widget('myWidget')` within your blade files and it will turn into `HTML`!!! 
 
 So you can replace `@include('myPartial')` with `@widget('myWidget')` in our laravel applications.

------------------


### :gem: Overview:Technical Package Features

> 1. It optionally `caches the output` of each widget. (which give a very powerful, flexible and easy to use caching opportunity) You can set different cache config for each part of the page. Similar to `ESI` standard.
> 2. It optionally `minifies` the output of the widget.
> 3. It shows Debug info for your widgets as html title="" attributes.
> 4. __php artisan make:widget__ command 
> 5. It helps you to have a dedicated presenter class of each widget to clean up your views.
> 6. It extends the Route facade with `Route::view` , `Route::jsonWidget` , `Route::widget`

-------------------

#### Overview:What happens when your write @widget('SomeWidget') in your views (Long story, short)


Given that we have disabled caching in the widgetize config file...

1 - It first looks for "SomeWidget" class to inspect it.

2 - Then calls the widget's controller method and gets some data from it.

3 - Using that data it "compiles" (in other word "renders") the blade file ($template). (to produce some html)

4 - At last, (If caching is enabled for the widget) it puts a copy of the resulting html in cache, for future use and return it.

-------------------------

### Overview:"Widgets" vs. "View Composers":

You might think that "view composers" are already doing the job, so why "widgets" ?

1- The worst thing about view composers is you never know which composer is attached to a @include not to mention other members of your team.

2- You have no way of passing data to the compose() method from your view.
They receive a \Illuminate\View\View object. so they can not be re-used to expose json data.
widgetize designed to provide fully freedom and resuability for widget-controllers.

``` php
public function compose(View $view)
{
    $view->with('count', $this->users->count());
}

```
3- $view->with('count', $this->users->count()); 
This exposes the $count to all views not just the partial.
And this violates encapsulation.

4- They offer no caching out of the box.


----------------------


## :bulb: Sample Code:


### Sample:How to make a Widget?


>__You can use : `php artisan make:widget MyWidget` to make your widget class.__

Sample widget class :
```php
namespace App\Widgets;

class MyWidget
{
    // The data returned here would be available in widget view file automatically.
    public function data($my_param=5)
    {
        // It's the perfect place to query the database for your widget...
        return Product::orderBy('id', 'desc')->take($my_param)->get();

    }
}
```



App\Widgets\MyWidgetView.blade.php :

```blade
<ul>
  @foreach($data as $product)
    <li>
      {{ $product->title }}
    </li>
  @endforeach
  
  Note that it is perfectly ok to use an other widget here 
  @widget('AnOtherWidget')
</ul>
```

Ok, Now it's done! We have a ready to use widget. let's use it...


### Sample:Then how to use that widget ?

In a normal day to day view (middle-end):
```blade
<html>
    <head></head>
    <body>
        <h1>Hello {{ auth()->user()->username }} </h1> <!-- not cached -->

        @widget('RecentProductsWidget') <!-- Here we send request to back-end to get HTML -->
        
    <body>
</html>
```

---------------------


#### __An other way to think of @widget() in your blade files :__

```
All of us, more or less have some ajax experience. One scenario is to lazy load a page partial after
the page has been fully loaded.
You can think of @widget() as an ajax call from "middle-end" to the "back-end" to load a piece of HTML
into the page.
```

-----------------

## :book: Documentation:


### Installation: :arrow_down:
``` bash
composer require imanghafoori/laravel-widgetize
```

:electric_plug: (For Laravel <=5.4) Next, you must add the service provider to `config/app.php` :electric_plug:

```php
'providers' => [
    // for laravel 5.4 and below
    Imanghafoori\Widgets\WidgetsServiceProvider::class,
];
```

__Publish your config file__
``` bash
php artisan vendor:publish
```

 :fire: And you will be on fire!:fire:

``` bash
php artisan make:widget MySexyWidget
```

A lot of docs are included in the generated widget file so it is not needed to memorize or even read the rest of this page.
You can jump right-in and start using it.




### :earth_africa: Global Config:

> You can set the variables in "config/widgetize.php" file to globally set some configs for you widgets and override them per widget if needed.
>Read the docblocks in __config/widgetize.php__ file for more info. 



### :blue_car: Per Widget Config:


#### __public $template__ (string)

>If you do not set it,By default, it refers to app/Widgets folder and looks for the 'widgetNameView.blade.php'
(Meaning that if your widget is `app/Widgets/home/recentProducts.php` the default view for that is `app/Widgets/home/recentProductsView.blade.php`)
Anyway you can override it to point to any partial in views folder.(For example: `public $template='home.footer'` will look for resource/views/home/footer.blade.php)
So the entire widget lives in one folder:

>| _app\Widgets\Homepage\RecentProductsWidget.php_

>| _app\Widgets\Homepage\RecentProductsWidgetView.blade.php_


#### __public $controller__ (string)

> If you do not want to put your _data_ method on your widget class, you can set `public $controller = App\Some\Class\MyController::class` and put your `public data` method on a dedicated class.(instead od having it on your widget class)



#### __public $presenter__ (string)

> If you do not want to put your _present_ method on your widget class, you can set
`public $presenter = App\Some\Class\MyPresenter::class` and put your `public present` method on a dedicated class.The data retured from your controller is first piped to your presenter and then to your view.(So if you specify a presenter your view file gets its data from the presenter and not the controller.)



#### __public $cacheLifeTime__ (int)

> If you want to override the global cache life time (which is set in your config file).

 value  | effect
:-------|:----------
   -1   | forever
'forever' | forever
  0    | disable
  1    | 1 minute


#### __public $cacheTags__ (array)

> You can set tags `public $cacheTags = ['tag1','tag2']` to target a group of widgets and flush their cache.
using the helper function :

```php
expire_widgets(['someTag', 'tag1']);
```
This causes all the widgets with 'someTag' or 'tag1' to be refreshed.


__Note: Tagging feature works with ALL the laravel cache drivers including 'file' and 'database'.__



#### __public function cacheKey__

> If you want to explicitly define the cache key used to store the html result of your widget, you can implement this method.



#### __public function extraCacheKeyDependency__

> It is important to note that if your final widget HTML output depends on PHP's super global variables and you 
want to cache it,Then they must be included in the cache key of the widget.

```php
namespace App\Widgets;

class MyWidget
{

    public function data()
    {
        $id = request('order_id'); // here we are using a request parameter to fetch database...
        return Product::where('order_id', $id)->get();
    }
    

    public function extraCacheKeyDependency()
    {
        // so the value of this parameter should be considered for caching.
        return request()->get('order_id');
    }
    
}

```

You may want to look at the source code and read the comments for more information.

__Tip:__ If you decide to use some other template engine instead of Blade it would be no problem.


### :book: Solid Design Pattern
You can Find more information in the article below :
It is a 3 minutes read.

[Single Responsibility Prinsiple](https://medium.com/@imanghafoori1/taste-single-responsibility-in-your-laravel-controllers-with-laravel-widgetize-package-9e0800d8b559)


--------------------------

## Q&A

### Q&A:How to expose only a widget HTML content from a url ?

```php
Route::widget('/some-url', 'MyWidget', 'MyRouteName1'); // <-- exposes HTML
// or
Route::jsonWidget('/my-api','MyWidget', 'MyRouteName2'); // <-- exposes json

```
A `GET` request to `/some-url/{a}/{b}` will see the widget.
_a_ and _b_ parameters are passed to widget controller.


`jsonWidget` will expose the cached data returned from the widget's controller.


You can also :

```php
Route::view('/some-url-2', 'someView', 'theRouteName'); //loads resource/views/someView.blade.php with a GET
```

--------------------

### Q&A:How to reference widget controllers from routes ?

This way you can also expose your data as json for client-side apps.

```php
Route::get('/api/products/{id}', '\App\Widgets\MyWidget@data');
```
\* It is important to put `\` before `App` when you want to refer to a class outside the `Http\Controller` folder.

--------------------

### :raising_hand: Contributing 
If you find an issue, or have a better way to do something, feel free to open an issue or a pull request.
If you use laravel-widgetize in your open source project, create a pull request to provide it's url as a sample application in the README.md file. 


### :exclamation: Security
If you discover any security related issues, please email imanghafoori1@gmail.com instead of using the issue tracker.


### :star: Your Stars Make Us Do More :star:
As always if you found this package useful and you want to encourage us to maintain and work on it. Just press the star button to declare your willing.
