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
        return app()->lang->has($key);
    }

    /**
     * @param $key
     * @param array $replace
     * @return mixed
     */
    public static function get($key, $replace = [])
    {
        return app()->lang->get($key, $replace);
    }

    /**
     * @param $locale
     * @return mixed
     */
    public static function setLocale($locale)
    {
        return app()->lang->setLocale($locale);
    }

    /**
     * @return string
     */
    public static function currentLocale()
    {
        return app()->lang->currentLocale();
    }
}