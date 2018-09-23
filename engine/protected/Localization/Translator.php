<?php
/*
 * MyUCP
 */

class Translator
{
    /**
     * @var LocalizationLoader
     */
    protected $loader;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var array
     */
    protected $loaded = [];

    /**
     * Translator constructor.
     * @param LocalizationLoader $loader
     * @param $locale
     */
    public function __construct(LocalizationLoader $loader, $locale)
    {
        $this->loader = $loader;

        if(empty(cookie("__lang"))) {
            $this->setLocale($locale);
        } else {
            $this->setLocale(cookie("__lang"));
        }
    }

    /**
     * @param $key
     * @return bool
     */
    public function has($key)
    {
        return $this->get($key, []) !== $key;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function get($key, $replace = [])
    {
        list($group, $item) = $this->parseKey($key);
        $this->load($group);

        $line = $this->getLine($group, $item, $replace);

        if (! isset($line)) {
            return $key;
        }

        return $line;
    }

    /**
     * @param $group
     * @param $item
     * @param array $replace
     * @return string
     */
    protected function getLine($group, $item, $replace = [])
    {
        $line = $this->loaded[$this->locale][$group][$item];

        if (is_string($line)) {
            return $this->makeReplacements($line, $replace);
        } elseif (is_array($line) && count($line) > 0) {
            return $line;
        }
    }

    /**
     * @param $locale
     * @param $group
     * @return bool
     */
    protected function isLoaded($locale, $group)
    {
        return isset($this->loaded[$locale][$group]);
    }

    /**
     * @param $group
     * @throws DebugException
     */
    protected function load($group)
    {
        if ($this->isLoaded($this->locale, $group)) {
            return;
        }
        $items = $this->loader->load($group);
        $this->loaded[$this->locale][$group] = $items;
    }

    /**
     * @param $key
     * @return array
     */
    protected function parseKey($key)
    {
        return explode(".", $key);
    }

    /**
     * @param  string  $line
     * @param  array   $replace
     * @return string
     */
    protected function makeReplacements($line, array $replace)
    {
        foreach ($replace as $key => $value) {
            $line = str_replace(':'.$key, $value, $line);
        }

        return $line;
    }

    /**
     * @param mixed $locale
     * @return Translator
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
        $this->loader->setLocale($locale);
        cookie("__lang", $locale)->forever();
        return $this;
    }

    /**
     * @return string
     */
    public function currentLocale()
    {
        return $this->locale;
    }
}