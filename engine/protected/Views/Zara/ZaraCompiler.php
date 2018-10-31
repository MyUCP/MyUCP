<?php 
/*
  * MyUCP
  */

class ZaraCompiler
{
    /**
     * @var array
     */
	protected $rawTags = ['{!!', '!!}'];

    /**
     * @var array
     */
	protected $contentTags = ['{{', '}}'];

    /**
     * @var array
     */
    protected $escapedTags = ['{{{', '}}}'];

    /**
     * @var string
     */
    protected $echoFormat = 'e(%s)';

    /**
     * @var array
     */
    protected $compilers = [
        'Extensions',
        'Statements',
        'Comments',
        'Echos',
    ];

    /**
     * @var array
     */
    protected $extensions = [];

    /**
     * @var int
     */
    protected $forelseCounter = 0;

    /**
     * @var array
     */
    protected $footer = [];

    /**
     * @var ZaraFactory
     */
    protected $factory;

    /**
     * @var \App\services\ZaraService
     */
    protected $service;

    /**
     * @var array
     */
    protected $customDirectives = [];

    /**
     * @var array
     */
    protected $conditions = [];

    /**
     * @param null|string $path
     * @param ZaraFactory $factory
     */
    public function compile($path = null, ZaraFactory $factory)
    {
        $this->factory = $factory;
        $this->service = new \App\services\ZaraService(
            str_replace(".zara.php", "", pathinfo($path)['basename'])
        );

        $contents = $this->compileString(file_get_contents($path));

		file_put_contents("./assets/cache/".md5($path)."", $contents);
    }

    /**
     * @param string $value
     * @return string
     */
    public function compileString($value)
    {
        $result = '';

        $this->footer = [];

        // Here we will loop through all of the tokens returned by the Zend lexer and
        // parse each one into the corresponding valid PHP. We will then have this
        // template as the correctly rendered PHP that can be rendered natively.
        foreach (token_get_all($value) as $token) {
            $result .= is_array($token) ? $this->parseToken($token) : $token;
        }

        // If there are any footer lines that need to get added to a template we will
        // add them here at the end of the template. This gets used mainly for the
        // template inheritance via the extends keyword that should be appended.
        if (count($this->footer) > 0) {
            $result = ltrim($result, PHP_EOL)
                    .PHP_EOL.implode(PHP_EOL, array_reverse($this->footer));
        }

        return $result;
    }

    /**
     * @param string $token
     * @return mixed
     */
    protected function parseToken($token)
    {
        list($id, $content) = $token;

        if ($id == T_INLINE_HTML) {
            foreach ($this->compilers as $type) {
                $content = $this->{"compile{$type}"}($content);
            }
        }

        return $content;
    }

    /**
     * @param string $value
     * @return mixed
     */
    protected function compileExtensions($value)
    {
        foreach ($this->extensions as $compiler) {
            $value = call_user_func($compiler, $value, $this);
        }

        return $value;
    }

    /**
     * @param string $value
     * @return mixed
     */
    protected function compileStatements($value)
    {
        return preg_replace_callback('/\B@(\w+)([ \t]*)(\( ( (?>[^()]+) | (?3) )* \))?/x',
            function ($match) {
                return $this->compileStatement($match);
            }, $value);
    }

    protected function compileStatement($match)
    {
        if (method_exists($this, $method = 'compile'.ucfirst($match[1]))) {
            $match[0] = $this->$method((isset($match[3]) ? $match[3] : ""));
        } elseif (isset($this->customDirectives[$match[1]])) {
            $match[0] = $this->callCustomDirective($match[1], (isset($match[3]) ? $match[3] : ""));
        }

        return isset($match[3]) ? $match[0] : $match[0].$match[2];
    }

    /**
     * Call the given directive with the given value.
     *
     * @param  string  $name
     * @param  string|null  $value
     * @return string
     */
    protected function callCustomDirective($name, $value)
    {
        if (Str::startsWith($value, '(') && Str::endsWith($value, ')')) {
            $value = Str::substr($value, 1, -1);
        }

        return call_user_func($this->customDirectives[$name], trim($value));
    }

    /**
     * Compile Zara comments into valid PHP.
     *
     * @param  string  $value
     * @return string
     */
    protected function compileComments($value)
    {
        $pattern = sprintf('/%s--((.|\s)*?)--%s/', $this->contentTags[0], $this->contentTags[1]);

        return preg_replace($pattern, '<?php /*$1*/ ?>', $value);
    }

    /**
     * Compile Zara echos into valid PHP.
     *
     * @param  string  $value
     * @return string
     */
    protected function compileEchos($value)
    {
        foreach ($this->getEchoMethods() as $method => $length) {
            $value = $this->$method($value);
        }

        return $value;
    }

    /**
     * @param string $value
     * @return mixed
     */
    protected function compileRawEchos($value)
    {
        $pattern = sprintf('/(@)?%s\s*(.+?)\s*%s(\r?\n)?/s', $this->rawTags[0], $this->rawTags[1]);

        $callback = function ($matches) {
            $whitespace = empty($matches[3]) ? '' : $matches[3].$matches[3];

            return $matches[1] ? substr($matches[0], 1) : '<?php echo '.$this->compileEchoDefaults($matches[2]).'; ?>'.$whitespace;
        };

        return preg_replace_callback($pattern, $callback, $value);
    }

    /**
     * Get the echo methods in the proper order for compilation.
     *
     * @return array
     */
    protected function getEchoMethods()
    {
        $methods = [
            'compileRawEchos' => strlen(stripcslashes($this->rawTags[0])),
            'compileEscapedEchos' => strlen(stripcslashes($this->escapedTags[0])),
            'compileRegularEchos' => strlen(stripcslashes($this->contentTags[0])),
        ];

        uksort($methods, function ($method1, $method2) use ($methods) {
            // Ensure the longest tags are processed first
            if ($methods[$method1] > $methods[$method2]) {
                return -1;
            }
            if ($methods[$method1] < $methods[$method2]) {
                return 1;
            }

            // Otherwise give preference to raw tags (assuming they've overridden)
            if ($method1 === 'compileRawEchos') {
                return -1;
            }
            if ($method2 === 'compileRawEchos') {
                return 1;
            }

            if ($method1 === 'compileEscapedEchos') {
                return -1;
            }
            if ($method2 === 'compileEscapedEchos') {
                return 1;
            }
        });

        return $methods;
    }

    /**
     * @param $haystack
     * @param $needles
     * @return bool
     */
    protected function startsWith($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ($needle != '' && strpos($haystack, $needle) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $value
     * @return mixed
     */
    protected function compileRegularEchos($value)
    {
        $pattern = sprintf('/(@)?%s\s*(.+?)\s*%s(\r?\n)?/s', $this->contentTags[0], $this->contentTags[1]);
      	
        $callback = function ($matches) {
            $whitespace = empty($matches[3]) ? '' : $matches[3].$matches[3];

            $wrapped = sprintf($this->echoFormat, $this->compileEchoDefaults($matches[2]));

            return $matches[1] ? substr($matches[0], 1) : '<?php echo '.$wrapped.'; ?>'.$whitespace;
        };

        return preg_replace_callback($pattern, $callback, $value);
    }

    /**
     * @param $value
     * @return mixed
     */
    protected function compileEscapedEchos($value)
    {
        $pattern = sprintf('/(@)?%s\s*(.+?)\s*%s(\r?\n)?/s', $this->escapedTags[0], $this->escapedTags[1]);

        $callback = function ($matches) {
            $whitespace = empty($matches[3]) ? '' : $matches[3].$matches[3];

            return $matches[1] ? $matches[0] : '<?php echo e('.$this->compileEchoDefaults($matches[2]).'); ?>'.$whitespace;
        };

        return preg_replace_callback($pattern, $callback, $value);
    }

    /**
     * @param $value
     * @return mixed
     */
    public function compileEchoDefaults($value)
    {
        return preg_replace('/^(?=\$)(.+?)(?:\s+or\s+)(.+?)$/s', 'isset($1) ? $1 : $2', $value);
    }

    /**
     * @param $expression
     * @return string
     */
    protected function compileInject($expression)
    {
        $segments = explode(',', preg_replace("/[\(\)\\\"\']/", '', $expression));

        return '<?php $'.trim($segments[0])." = new ".trim($segments[1])."; ?>";
    }

    /**
     * @param $expression
     * @return string
     */
    protected function compileYield($expression)
    {
        return "<?php echo \$zara->factory->yieldContent{$expression}; ?>";
    }

    /**
     * @param $expression
     * @return string
     */
    protected function compileShow($expression)
    {
        return '<?php echo $zara->factory->yieldSection(); ?>';
    }

    /**
     * @param $expression
     * @return string
     */
    protected function compileSection($expression)
    {
        return "<?php \$zara->factory->startSection{$expression}; ?>";
    }

    /**
     * @param $expression
     * @return string
     */
    protected function compileAppend($expression)
    {
        return '<?php $zara->factory->appendSection(); ?>';
    }

    /**
     * @param $expression
     * @return string
     */
    protected function compileEndsection($expression)
    {
        return '<?php $zara->factory->stopSection(); ?>';
    }

    /**
     * @param $expression
     * @return string
     */
    protected function compileStop($expression)
    {
        return '<?php $zara->factory->stopSection(); ?>';
    }

    /**
     * @param $expression
     * @return string
     */
    protected function compileExtends($expression)
    {
        if ($this->startsWith($expression, '(')) {
            $expression = substr($expression, 1, -1);
        }

        $data = "<?php echo \$zara->compile($expression, get_defined_vars(), \$this->factory, true)->getCompiled(); ?>";

        $this->footer[] = $data;

        return '';
    }

    /**
     * @param $expression
     * @return string
     */
    protected function compileUnless($expression)
    {
        return "<?php if ( ! $expression): ?>";
    }

    /**
     * @param $expression
     * @return string
     */
    protected function compileEndunless($expression)
    {
        return '<?php endif; ?>';
    }

    /**
     * @param $expression
     * @return string
     */
    protected function compileFor($expression)
    {
        return "<?php for{$expression}: ?>";
    }

    /**
     * @param $expression
     * @return string
     */
    protected function compileForeach($expression)
    {
        return "<?php foreach{$expression}: ?>";
    }

    /**
     * @param $expression
     * @return string
     */
    protected function compileForelse($expression)
    {
        $empty = '$__empty_'.++$this->forelseCounter;

        return "<?php {$empty} = true; foreach{$expression}: {$empty} = false; ?>";
    }

    /**
     * @param $expression
     * @return string
     */
    protected function compileIf($expression)
    {
        return "<?php if{$expression}: ?>";
    }

    /**
     * @param $expression
     * @return string
     */
    protected function compileElseif($expression)
    {
        return "<?php elseif{$expression}: ?>";
    }

    /**
     * @param $expression
     * @return string
     */
    protected function compileEmpty($expression)
    {
        $empty = '$__empty_'.$this->forelseCounter--;

        return "<?php endforeach; if ({$empty}): ?>";
    }

    /**
     * @param $expression
     * @return string
     */
    protected function compileWhile($expression)
    {
        return "<?php while{$expression}: ?>";
    }

    /**
     * @param $expression
     * @return string
     */
    protected function compileEndwhile($expression)
    {
        return '<?php endwhile; ?>';
    }

    /**
     * @param $expression
     * @return string
     */
    protected function compileEndfor($expression)
    {
        return '<?php endfor; ?>';
    }

    /**
     * @param $expression
     * @return string
     */
    protected function compileEndforeach($expression)
    {
        return '<?php endforeach; ?>';
    }

    /**
     * @param $expression
     * @return string
     */
    protected function compileEndcan($expression)
    {
        return '<?php endif; ?>';
    }

    /**
     * @param $expression
     * @return string
     */
    protected function compileEndcannot($expression)
    {
        return '<?php endif; ?>';
    }

    /**
     * @param $expression
     * @return string
     */
    protected function compileEndif($expression)
    {
        return '<?php endif; ?>';
    }

    /**
     * @param $expression
     * @return string
     */
    protected function compileEndforelse($expression)
    {
        return '<?php endif; ?>';
    }

    /**
     * @param $expression
     * @return string
     */
    protected function compileElse($expression)
    {
        return '<?php else: ?>';
    }

    /**
     * @method()
     *
     * @param $expression
     * @return string
     */
    protected function compileMethod($expression)
    {
        return "<?php echo method_field{$expression}; ?>";
    }

    /**
     * @lang()
     *
     * @param $expression
     * @return string
     */
    protected function compileLang($expression)
    {
        return "<?php echo lang{$expression}; ?>";
    }

    /**
     * @csrf_token()
     *
     * @param $expression
     * @return string
     */
    protected function compileCsrf_token($expression)
    {
        return "<?php echo csrf_token(); ?>";
    }

    /**
     * @csrf_field()
     *
     * @param $expression
     * @return string
     */
    protected function compileCsrf_field($expression)
    {
        return "<?php echo csrf_field(); ?>";
    }

    /**
     * @asset()
     *
     * @param $expression
     * @return string
     */
    protected function compileAsset($expression)
    {
        return "<?php echo asset$expression; ?>";
    }

    /**
     * Register a handler for custom directives.
     *
     * @param  string  $name
     * @param  callable  $handler
     * @return void
     */
    public function directive($name, callable $handler)
    {
        $this->customDirectives[$name] = $handler;
    }

    /**
     * Register an "if" statement directive.
     *
     * @param  string  $name
     * @param  callable  $callback
     * @return void
     */
    public function if($name, callable $callback)
    {
        $this->conditions[$name] = $callback;

        $this->directive($name, function ($expression) use ($name) {
            return $expression
                ? "<?php if (Zara::check('{$name}', {$expression})): ?>"
                : "<?php if (Zara::check('{$name}')): ?>";
        });

        $this->directive('else'.$name, function ($expression) use ($name) {
            return $expression
                ? "<?php elseif (Zara::check('{$name}', {$expression})): ?>"
                : "<?php elseif (Zara::check('{$name}')): ?>";
        });

        $this->directive('end'.$name, function () {
            return '<?php endif; ?>';
        });
    }

    /**
     * Check the result of a condition.
     *
     * @param  string  $name
     * @param  array  $parameters
     * @return bool
     */
    public function check($name, ...$parameters)
    {
        return call_user_func($this->conditions[$name], ...$parameters);
    }
}