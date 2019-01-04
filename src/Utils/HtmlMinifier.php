<?php

namespace Imanghafoori\Widgets\Utils;

/**
 * Class Html Minifier.
 */
class HtmlMinifier
{
    private $replace = [
        '<!--(.*?)-->' => '', //remove comments
        "/<\?php/" => '<?php ',
        "/\n([\S])/" => '$1',
        "/\r/" => '', // remove carriage return
        "/\n/" => '', // remove new lines
        "/\t/" => '', // remove tab
        "/\s+/" => ' ', // remove spaces
    ];

    /**
     * @param $htmlString string
     * @return string
     */
    public function minify(string $htmlString) : string
    {
        return preg_replace(array_keys($this->replace), array_values($this->replace), $htmlString);
    }
}
