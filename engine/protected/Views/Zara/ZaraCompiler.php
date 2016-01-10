<?php 

class ZaraCompiler {

	protected $rawTags = ['{!!', '!!}'];
    protected $contentTags = ['{{', '}}'];
    protected $escapedTags = ['{{{', '}}}'];
    protected $echoFormat = 'e(%s)';
    protected $compilers = [
        'Extensions',
        'Statements',
        'Comments',
        'Echos',
    ];
    protected $extensions = [];
    protected $forelseCounter = 0;
    protected $footer = [];
    protected $factory;

    public function compile($path = null, ZaraFactory $factory)
    {
        $this->factory = $factory;
        $contents = $this->compileString(file_get_contents($path));
		file_put_contents("./assets/cache/".md5($path)."", $contents);
    }

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

    protected function compileExtensions($value)
    {
        foreach ($this->extensions as $compiler) {
            $value = call_user_func($compiler, $value, $this);
        }

        return $value;
    }

    protected function compileStatements($value)
    {
        $callback = function ($match) {
            if (method_exists($this, $method = 'compile'.ucfirst($match[1]))) {
                $match[0] = $this->$method($match[3]);
            } elseif (isset($this->customDirectives[$match[1]])) {
                $match[0] = call_user_func($this->customDirectives[$match[1]], $match[3]);
            }

            return isset($match[3]) ? $match[0] : $match[0].$match[2];
        };

        return preg_replace_callback('/\B@(\w+)([ \t]*)(\( ( (?>[^()]+) | (?3) )* \))?/x', $callback, $value);
    }

    /**
     * Compile Blade comments into valid PHP.
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
     * Compile Blade echos into valid PHP.
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

    protected function startsWith($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ($needle != '' && strpos($haystack, $needle) === 0) {
                return true;
            }
        }

        return false;
    }

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

    protected function compileEscapedEchos($value)
    {
        $pattern = sprintf('/(@)?%s\s*(.+?)\s*%s(\r?\n)?/s', $this->escapedTags[0], $this->escapedTags[1]);

        $callback = function ($matches) {
            $whitespace = empty($matches[3]) ? '' : $matches[3].$matches[3];

            return $matches[1] ? $matches[0] : '<?php echo e('.$this->compileEchoDefaults($matches[2]).'); ?>'.$whitespace;
        };

        return preg_replace_callback($pattern, $callback, $value);
    }

    public function compileEchoDefaults($value)
    {
        return preg_replace('/^(?=\$)(.+?)(?:\s+or\s+)(.+?)$/s', 'isset($1) ? $1 : $2', $value);
    }

    protected function compileInject($expression)
    {
        $segments = explode(',', preg_replace("/[\(\)\\\"\']/", '', $expression));

        return '<?php $'.trim($segments[0])." = new ".trim($segments[1])."; ?>";
    }

    protected function compileYield($expression)
    {
        return "<?php echo \$zara->factory->yieldContent{$expression}; ?>";
    }

    protected function compileShow($expression)
    {
        return '<?php echo $zara->factory->yieldSection(); ?>';
    }

    protected function compileSection($expression)
    {
        return "<?php \$zara->factory->startSection{$expression}; ?>";
    }

    protected function compileAppend($expression)
    {
        return '<?php $zara->factory->appendSection(); ?>';
    }

    protected function compileEndsection($expression)
    {
        return '<?php $zara->factory->stopSection(); ?>';
    }

    protected function compileStop($expression)
    {
        return '<?php $zara->factory->stopSection(); ?>';
    }

    protected function compileExtends($expression)
    {
        if ($this->startsWith($expression, '(')) {
            $expression = substr($expression, 1, -1);
        }

        $data = "<?php echo \$zara->compile($expression, get_defined_vars(), \$this->factory)->getCompiled(); ?>";

        $this->footer[] = $data;

        return '';
    }

    protected function compileUnless($expression)
    {
        return "<?php if ( ! $expression): ?>";
    }

    protected function compileEndunless($expression)
    {
        return '<?php endif; ?>';
    }

    protected function compileFor($expression)
    {
        return "<?php for{$expression}: ?>";
    }

    protected function compileForeach($expression)
    {
        return "<?php foreach{$expression}: ?>";
    }

    protected function compileForelse($expression)
    {
        $empty = '$__empty_'.++$this->forelseCounter;

        return "<?php {$empty} = true; foreach{$expression}: {$empty} = false; ?>";
    }

    protected function compileIf($expression)
    {
        return "<?php if{$expression}: ?>";
    }

    protected function compileElseif($expression)
    {
        return "<?php elseif{$expression}: ?>";
    }

    protected function compileEmpty($expression)
    {
        $empty = '$__empty_'.$this->forelseCounter--;

        return "<?php endforeach; if ({$empty}): ?>";
    }

    protected function compileWhile($expression)
    {
        return "<?php while{$expression}: ?>";
    }

    protected function compileEndwhile($expression)
    {
        return '<?php endwhile; ?>';
    }

    protected function compileEndfor($expression)
    {
        return '<?php endfor; ?>';
    }

    protected function compileEndforeach($expression)
    {
        return '<?php endforeach; ?>';
    }

    protected function compileEndcan($expression)
    {
        return '<?php endif; ?>';
    }

    protected function compileEndcannot($expression)
    {
        return '<?php endif; ?>';
    }

    protected function compileEndif($expression)
    {
        return '<?php endif; ?>';
    }

    protected function compileEndforelse($expression)
    {
        return '<?php endif; ?>';
    }

    protected function compileElse($expression)
    {
        return '<?php else: ?>';
    }
}