<?php
class ForeverWidget extends Imanghafoori\Widgets\BaseWidget
{
    public $cacheLifeTime = -1;
    public $template = 'hello';
    public function data()
    {
    }
}

class ForeverWidget2 extends Imanghafoori\Widgets\BaseWidget
{
    public $cacheLifeTime = 'forever';
    public $template = 'hello';
    public function data()
    {
    }
}

class Widget1 extends Imanghafoori\Widgets\BaseWidget
{
//    public $cacheLifeTime = -1;
    public $template = 'hello';
    public function data()
    {
    }
}

class Widget2 extends Imanghafoori\Widgets\BaseWidget
{
//    public $cacheLifeTime = 'forever';

    public function data()
    {
    }
}

class Widget3 extends Imanghafoori\Widgets\BaseWidget
{
    public $template = 'hello';
    public $contextAs = '$myData';

    public function data()
    {
    }
}

class Widget4 extends Imanghafoori\Widgets\BaseWidget
{
    public $template = 'hello';
    public $controller = 'Widget4Ctrl';

    public function data()
    {
    }
}

class Widget4Ctrl
{
    public function data()
    {
    }
}


class Widget5 extends Imanghafoori\Widgets\BaseWidget
{
    public $template = 'hello';
    public $presenter = 'Widget5Presenter';

    public function data()
    {
        return 'foo';
    }
}

class Widget5Presenter
{
    public function present($data)
    {
        return 'bar' . $data;
    }
}
