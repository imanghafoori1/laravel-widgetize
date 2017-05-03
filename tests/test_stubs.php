<?php


class ForeverWidget
{
    public $cacheLifeTime = -1;
    public $template = 'hello';

    public function data()
    {
    }
}

class ForeverWidget2
{
    public $cacheLifeTime = 'forever';
    public $template = 'hello';

    public function data()
    {
    }
}

class Widget1
{
    //    public $cacheLifeTime = -1;
    public $template = 'hello';

    public function data()
    {
    }
}

class Widget2
{
    //    public $cacheLifeTime = 'forever';

    public function data()
    {
    }
}

class Widget3
{
    public $template = 'hello';
    public $contextAs = '$myData';

    public function data()
    {
    }
}

class Widget4
{
    public $template = 'hello';
    public $controller = 'Widget4Ctrl';

}

class Widget4Ctrl
{
    public function data($arg1,$arg2)
    {
        return $arg1.$arg2;
    }
}

class Widget5
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
        return 'bar'.$data;
    }
}

class Widget6
{
    public $template = 'hello';

    public function data($foo, $bar)
    {
        return $bar.$foo;
    }
}

class Widget7
{
    public $template = 'hello';

    public function data()
    {
        return null;
    }
}