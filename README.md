Laravel Widgetize
=================


![untitled2](https://cloud.githubusercontent.com/assets/6961695/24345454/7d5c9e4c-12e5-11e7-8c22-015395dbb796.jpg)

<a href="https://scrutinizer-ci.com/g/imanghafoori1/laravel-widgetize"><img src="https://img.shields.io/scrutinizer/g/imanghafoori1/laravel-widgetize.svg?style=flat-square" alt="Quality Score"></img></a>
[![Latest Stable Version](https://poser.pugx.org/imanghafoori/laravel-widgetize/v/stable)](https://packagist.org/packages/imanghafoori/laravel-widgetize)
[![Latest Unstable Version](https://poser.pugx.org/imanghafoori/laravel-widgetize/v/unstable)](https://packagist.org/packages/imanghafoori/laravel-widgetize)
[![Build Status](https://scrutinizer-ci.com/g/imanghafoori1/laravel-widgetize/badges/build.png?b=master)](https://scrutinizer-ci.com/g/imanghafoori1/laravel-widgetize/build-status/master)
[![License](https://poser.pugx.org/imanghafoori/laravel-widgetize/license)](https://packagist.org/packages/imanghafoori/laravel-widgetize)
[![Awesome Laravel](https://img.shields.io/badge/Awesome-Laravel-brightgreen.svg)](https://github.com/z-song/laravel-admin)




## :ribbon::ribbon::ribbon: Widget Objects help you have "_cleaner code_" :heavy_plus_sign: "_easy caching_" :ribbon::ribbon::ribbon:


* :flashlight: [Introduction](#introduction)
    - [What is a _widget object_ ?](#what-is-a-widget-object)
    - [When to use the _widget_ concept?](#when-to-use-the-widget-concept)
    - :gem: [Package Features](#gem-package-features-gem)
    
* :wrench: [Installation](#wrench-installation-arrow_down)
* :earth_africa: [Global Configuration](#earth_africa-global-config)
* :blue_car: [Per Widget Configuration](#blue_car-per-widget-config)
    - [public $template (optional)](#public-template-string)
    - [public $controller (optional)](#public-controller-string)
    - [public $presenter (optional)](#public-presenter-string)
    - [public $cacheLifeTime (optional)](#public-cachelifetime-int)
    - [public $cacheTags (optional)](#public-cachetags-array)
    - [public function extraCacheKeyDependency (optional)](#public-function-extracachekeydependency)
    - [public function cacheKey (optional)](#public-function-cachekey)
   
* :bulb: [Usage and Example](#bulb-example)
    - [How to make a widget class](#how-to-make-a-widget)
    - [How to use a widget class](#how-to-use-a-widget-class)
    
* :shipit: [Some Theory for Experts](#)
    - :snake: [The Problems](#snake-what-is-our-problems-snake)
    - :dart: [The Solution](#dart-what-is-the-solution)
    - :book: [The Theory Behind Widget Objects](#book-design-patterns-theory)
* :star: [Your Stars Makes Us Do More](#star-your-stars-make-us-do-more-star)








This page may look long and boring to read at first, but bear with me!!!

I bet if you read through it you won't get disappointed at the end.So let's Go... :horse_racing:



### :flashlight: Introduction
>This package helps you in :
- Page Partial Caching
- Code Organization
- HTML Minification

#### What is a widget?

>You can think of a widget as a page partial with a 'View Composer' attached to it.

>Or If you know `Drupal's Views` concept, they are very similar to each other.

>In fact a Widget is are normal php class without any magical methods,
 when you pass them into the `render_widget()` helper function or `@widget()` directive they magically output `HTML`!!! 
 Which is the result of rendering a view partial with data from the widget controller.
 So we can replace `@include('myPartial')` with `@widget('myWidget')` in our laravel applications.
 The main benefit you get here is the fact that widget objects are __cached__ and they know how to provide data from them selves.



#### When to use the _widget_ concept?

>The simple answer is : `Always` This concept (this design pattern) really shines when you want to create crowded web pages with multiple widgets (on sidebar, menu, carousels ...) and each widget needs separate sql queries and php logic to be provided with data for its template. Anyway installing it has minimal overhead since surprisingly it is just a small abstract class and Of course you can use it to __refactor your monster code and tame it__ into managable pieces or __boost the performance 4x-5x__ times faster! :dizzy:




### :gem: Package Features :gem:

> 1. It optionally `caches the output` of each widget. (which give a very powerful, flexible and easy to use caching opportunity) You can set different cache config for each part of the page. Similar to `ESI` standard.
> 2. It optionally `minifies` the output of the widget.
> 3. It support the `nested widgets` tree structure. (Use can inject and use widgets within widgets.)
> 4. It can help you generate widget class boilerplate with artisan command. 
> 5. It helps you to have a dedicated presenter class of each widget to clean up your views.

### :wrench: Installation: :arrow_down:
``` bash
composer require imanghafoori/laravel-widgetize
```

:electric_plug: Next, you must install the service provider to `config/app.php`: :electric_plug:

```php
'providers' => [
    // ...
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

## :bulb: Example

### How to make a Widget?

>__You can use : `php artisan make:widget MyWidget` to make your widget class.__

Sample widget class :
```php
namespace App\Widgets;

class MyWidget
{
    // The data returned here would be available in widget view file automatically.
    public function data($param=5)
    {
        // It's the perfect place to query the database for your widget...
        return Product::orderBy('id', 'desc')->take($param1)->get();

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


### How to use a widget class?

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

#### __An other way to think of widgets :__

```
All of us, more or less have some ajax experience. One scenario is to lazy load a page partial after
the page has been fully loaded. (like jQuery Pjax plug-in does)
You can think of @widget() as an ajax call from "middle-end" to the "back-end" to load a piece of HTML
into the main page.
In facts your widgets are the 'Back-end' and your typical views are the 'middle-end' !!!
```



## :earth_africa: Global Config:
> You can set the variables in your config file to globally set some configs for you widgets and override them per widget if needed.
>Read the docblocks in config file for more info. 



## :blue_car: Per Widget Config:


## __public $template__ (string)

>If you do not set it,By default, it refers to app/Widgets folder and looks for the 'widgetNameView.blade.php'
(Meaning that if your widget is `app/Widgets/home/recentProducts.php` the default view for that is `app/Widgets/home/recentProductsView.blade.php`)
Anyway you can override it to point to any partial in views folder.(For example: `public $template='home.footer'` will look for resource/views/home/footer.blade.php)
So the entire widget lives in one folder:

>| _app\Widgets\Homepage\RecentProductsWidget.php_

>| _app\Widgets\Homepage\RecentProductsWidgetView.blade.php_


### __public $controller__ (string)

> If you do not want to put your _data_ method on your widget class, you can set `public $controller = App\Some\Class\MyController::class` and put your `public data` method on a dedicated class.(instead od having it on your widget class)



### __public $presenter__ (string)

> If you do not want to put your _present_ method on your widget class, you can set
`public $presenter = App\Some\Class\MyPresenter::class` and put your `public present` method on a dedicated class.The data retured from your controller is first piped to your presenter and then to your view.(So if you specify a presenter your view file gets its data from the presenter and not the controller.)



### __public $cacheLifeTime__ (int)

> If you want to override the global cache life time (which is set in your config file) for a specific widget, you can set $cacheLifeTime on your widget class.

 value  | effect
:-------|:----------
   -1   | forever
'forever' | forever
  0    | disable
  1    | 1 minute


### __public $cacheTags__ (array)

> You can set `public $cacheTags = ['tag1','tag2']` to exactly target a group of widgets to flush their cached state.
using the helper function :

```php
expire_widgets(['someTag', 'someOtherTag']);
```
This causes all the widgets with 'someTag' and 'someOtherTag' to be refreshed.


__Note: Tagging feature works with ALL the laravel cache drivers including 'file' and 'database'.__


### __public function extraCacheKeyDependency__

> It is important to note that if your final widget HTML output depends on PHP's super global variables and you 
want to cache it,Then they must be included in the cache key of the widget. So for example :

### __public function cacheKey__

> If you want to explicitly define the cache key used to store the html result of your widget, you can implement this method.


```php
namespace App\Widgets;

class MyWidget
{

    public function data()
    {
        $id = request('order_id');
        return Product::where('order_id', $id)->get();
    }
    

    public function extraCacheKeyDependency()
    {
        return request()->get('order_id');
    }
    
}

```


You may want to look at the source code and read the comments for more information.

__Tip:__ If you decide to use some other template engine instead of Blade it would be no problem.


### :snake: What is our problems? :snake:

#### Problem 1 : Controllers easily get crowded :weary:
>Imagine An online shop like amazon which shows the list of products, popular products, etc (in the sidebar), user data and basket data in the navbar and a tree of product categories in the menu and etc... In traditional good old MVC model you have a single controller method to provide all the widgets with data. You can immidiately see that you are violating the SRP (Single Responsibility Priciple)!!! The trouble is worse when the client changes his mind over time and asks the deveploper to add, remove and modify those widgets on the page. And it always happens. Clients do change their minds.The developoer's job is to be ready to cope with that as effortlessly as possible.

#### Problem 2 : Page caching is always hard (But no more) :disappointed:
>Trying to cache the pages which include user specific data (for example the username on the top menu) is a often fruitless. Because each user sees slightly different page from other users. Or in cases when we have some parts of the page which update frequently and some other parts which change rarly... we have to expire the entire page cache to match the most frequently updated one. :(
AAAAAAAAAhh...


#### Problem 3 : View templates easily get littered with if/else blocks :dizzy_face:
>We ideally want our view files to be as logic-less as possible and very much like the final output HTML.Don't we ?! if/else blocks and other computations are always irritating within our views. specially for static page designers in our team. We just want to print out already defined variables wiout the to decide what to print. Anyway the data we store in database are sometimes far from ready to be printed on the page.


### :dart: What is the solution?

> So, How to fight against those ?

>__The main idea is simple, Instead of one controller method to handle all widgets of the page, Each widget should have it's own `controller class`, `view partial`, `view presenter class` and `cache config`, isolated from others.__
>That's it !! :)
>This idea originally comes from the client-side js frameworks and is somewhat new in server-side world.

###  :book: Design Patterns Theory
>The widget object pattern is in fact a variation of the famous `single responsibility principle`.
Instead of having one bloated controller method that was resposible to supply data for all the widgets...
You distribute your controller code amougst multiple widget classes.(Each widget is responsible for small portion of the page.)

>It helps you to conforms to `Open-closed principle`.Because if you want to add a widget on your page you do not need to add to the controller code. Instead you create a new widget class from scratch or when you want to remove something from the page you do not have go to the controller find and comment out related controller code. removing the `@widget('myWidget')` is enough to disable the corresponding controller and hence db queries.

### How to referrence widget controllers from routes ?

This way you can also expose your data as json for client-side apps.

```php
Route::get('/api/recent-products', '\App\Widgets\MyWidget@data');
```
\* It is important to put `\` before `App` when you wnat to refer to a class outside the `Http\Controller` folder.


### :raising_hand: Contributing 
If you find an issue, or have a better way to do something, feel free to open an issue or a pull request.


### :exclamation: Security
If you discover any security related issues, please email imanghafoori1@gmail.com instead of using the issue tracker.


### :star: Your Stars Make Us Do More :star:
As always if you found this package useful and you want to encourage us to maintain and work on it. Just press the star button to declare your willing.
