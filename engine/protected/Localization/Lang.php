<?php

/**
 * MyUCP
 * Alias Translator
 */
class Lang
{
    /**
     * @param $key
     * @return bool
     */
    public function has($key)
    {
        return registry()->lang->has($key);
    }

    /**
     * @param $key
     * @param array $replace
     * @return mixed
     */
    public static function get($key, $replace = [])
    {
        return registry()->lang->get($key, $replace);
    }

    /**
     * @param $locale
     * @return mixed
     */
    public static function setLocale($locale)
    {
        return registry()->lang->setLocale($locale);
    }

    /**
     * @return string
     */
    public static function currentLocale()
    {
        return registry()->lang->currentLocale();
    }
}