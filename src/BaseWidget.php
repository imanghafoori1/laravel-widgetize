<?php


namespace Imanghafoori\Widgets;


use Illuminate\Support\Facades\Cache;

abstract class BaseWidget
{
    abstract protected function data();
    
    protected $template      = null;
    protected $minifyOutput;
    protected $cacheLifeTime = 0;
    protected $html;
    protected $context_as = '$data';
    
    protected function addIdentifierToHtml()
    {
        $name = $this->friendlyName;
        
        $this->html = "
<!-- ^ --> <!--  --> <!-- '$name' Widget Start -->"
. $this->html .
"<!-- '$name' Widget End --> <!--   --> <!-- ~ -->
";
    }
    
    public function __toString()
    {
        return $this->__invoke();
    }
    
    public function __invoke()
    {
        $phpCode = function () {
            $data = $this->data(func_get_args());
            return $this->renderTemplate($data);
        };
        
        return $this->cacheResult($phpCode);
    }
    
    private function getViewName()
    {
	if ($this->template === null) {
            $className = str_replace('App\\Widgets\\','', get_called_class()); // class name without namespace.
            $className = str_replace(['\\','/'],'.', $className); // replace slashes with dots
            return 'Widgets::'.$className;
        }
	       
        return $this->template;
    }
    
    private function minifyHtml()
    {
        $replace = [
//                '/<!--[^\[](.*?)[^\]]-->/s' => '',
"/<\?php/"   => '<?php ',
"/\n([\S])/" => '$1',
"/\r/"       => '', // remove carrage return
"/\n/"       => '', // remove new lines
"/\t/"       => '', // remove tab
"/\s+/"      => ' ', // remove spaces
        ];
        
        $this->html = preg_replace(array_keys($replace), array_values($replace), $this->html);
        
    }
    
    private function makeCacheKey($arg)
    {
        return md5(json_encode($arg, JSON_FORCE_OBJECT) . $this->template . $this->friendlyName);
    }
    
    private function renderTemplate($data)
    {
        $this->html = view($this->getViewName(), [$this->contextVariable() => $data ])->render();
        
        if ($this->minifyOutput == true) {
            $this->minifyHtml();
        }
        $this->addIdentifierToHtml();
        
        return $this->html;
    }
    
    private function cacheResult($phpCode)
    {
        if ($this->cacheLifeTime > 0) {
            return Cache::remember($this->makeCacheKey(func_get_args()), $this->cacheLifeTime, $phpCode);
        }
        
        if ($this->cacheLifeTime == 'forever' or $this->cacheLifeTime < 0) {
            return Cache::rememberForever($this->makeCacheKey(func_get_args()), $phpCode);
        }
        
        if ($this->cacheLifeTime === 0) {
            return $phpCode();
        }
        
    }
    
    private function contextVariable()
    {
        if ($this->context_as){
            return $varName = str_replace('$', '', $this->context_as);
        }
        return 'data';
    }
    
    
}
