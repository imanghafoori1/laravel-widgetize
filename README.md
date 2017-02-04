
### When to use it ?

>This package helps you in stuations that you want to create crowded web pages with multiple widgets (on sidebar, menu, ...) and each widget needs seperate sql queries and php logic to be provided with data for its template. If you need a small application with low traffic this package is not much of a help.Anyway installing it has minimal overhead since surprisingly it is just a small abstract class.



### So what is our problem ?
#### Problem 1 :
>Imagine An online shop like amazon which shows the list of products (in the main column) popular products, related products,etc (in the sidebar), user data and basket data (in the navbar) and a tree of product categories (in the menu) and etc... And in traditional MVC model you have a single controller method to provide all the widgets with data. You can immidiately see that you are violating the SRP (Single Responsibility Priciple)!!! The trouble is worse when the client changes his mind over time and asks the deveploper to add, remove and modify those widgets on the page. And it always happens. Clients change their minds.The developoer's job is to be ready to cop with that as effortlessly as possible.

#### Problem 2 : 
>Trying to cache the pages which include user specific data (for example the username on the top menu) is a often fruitless. Because each user sees slightly different page from other users. Or in cases when we have some parts of the page(recent products section) which update frequently and some other parts which change rarly... we have to expire the entire page cache to match the most most frequently updated one. :(



### How this package is going to help us ?

1. It helps you to conforms to SRP (`single responsibility principle`) in your controllers (Because each widget class is only responsible for one and only one widget of the page but before you had a single controller method that was resposible for all the widgets. Effectively exploding one controller method into multiple widget classes.)
2. It helps you to conforms to `Open-closed principle`. (Because if you want to add a widget on your page you do not need to touch the controller code. Instead you create a new widget class from scratch.)
3. It optionally `caches the output` of each widget. (which give a very powerful, flexible and easy to use caching opportunity) You can set different cache config for each part of the page. Similar to `ESI` standard.
4. It executes the widget code `Lazily`. Meaning that the widget's data method `protected function data(){` is hit only and only after the widget object is forced to be rendered in the blade file like this: `{!! $widgetObj !!}`, So for example if you comment out `{!! $widgetObj !!}` from your blade file then all database queries will be disabled automatically. No need to comment out the controller codes anymore...
5. It optionally `minifies the output` of the widget. Removing the white spaces.
6. It support the `nested widgets` tree structure. (Use can inject and use widgets within widgets)



### Installation:

`composer require imanghafoori/laravel-widgetize`

>Optionally add `Imanghafoori\Widgets\WidgetsServiceProvider` to the providers array in your config/app.php
>Then you are free to extend the `Imanghafoori\Widgets\BaseWidget` abstract class which enforces to implement the protected `data` method in your sub-class.



### Usage:

>__The main idea is the each widget should have it's own controller class and view partial, isolated from others.__

###Guideline:

>1. So we first extract each widget into its own partial. (recentProducts.blade.php)
>2. Create a class and extend the BaseWidget. (App\Widgets\RecentProductsWidget.php)
>3. Set configurations like __$cacheLifeTime__ , __$template__, etc and implement the `data` method.
>4. Your widget is ready to be instanciated and be used in your view files. (see example below)



###How to create a Widget?

Sample widget class :
```php
namespace App\Widgets;

use Imanghafoori\Widgets\BaseWidget;


class RecentProductsWidget extends BaseWidget
{
    protected $template = 'widgets.recentProducts.blade.php'; // referes to: views/widgets/recentProducts.blade.php
    protected $cacheLifeTime = 1; // 1(min) ( 0 : disable, -1 : forever) default: 0
    protected $friendlyName = 'A Friendly Name Here'; // Showed in html Comments
    protected $context_as = '$recentProducts'; // you can access $recentProducts in recentProducts.blade.php file
    protected $minifyOutput = true; 

    // The data returned here would be available in widget view file.
    protected function data($param1=null)
    {
        // It's the perfect place to query the database...
        return Product::all();

    }
}
```

==============

views/widgets/recentProducts.blade.php

```blade
<ul>
  @foreach($recentProducts as $product)
    <li>
      <h3> {{ $product->title }} </h3>
      <p>$ {{ $product->price }} </p>
    </li>
  @endforeach
</ul>
```

Ok, Now it's done! We have a ready to use widget. let's use it...


###How to leverage a Widget Object?

In your typical controller method we should instanciate our widget class and pass the object to our view:
```php

use \App\Widgets\RecentProductsWidget;

public function index(RecentProductsWidget $recentProductsWidget)
{
    return view('home', compact('recentProductsWidget'));
}
```

And then you can render it in your view (home.blade.php) like this:
```blade
<div class="container">
    <h1>Hello {{ auth()->user()->username }} </h1>
    <br>
    {!! $recentProductsWidget !!}
    <p> if you need to pass parameters to data method :</p>
    {!! $recentProductsWidget('param1') !!}
</div>
```

=============

In order to understand what's going on here...
Think of `{!! $recentProductsWidget !!}` as `@include('widgets.recentProductsWidget')` but more sophisticated.
The actual result is the same piece of HTML, which is the result of rendering the partial.

Pro tip: After `{!! $myWidget('param1') !!}` is executed in your view file, the `data` method is called on your wid class with the correspoding parameters. `But only if it is Not already cached` or the `protected $cacheLifeTime` is set to 0.
If the widget HTML output is already in the cache it prints out the HTML with out executing `data` method (hence performing database queries) or even rendering the blade file.

