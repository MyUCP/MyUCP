<?php

/**
 * MyUCP
 */
class LocalizationLoader
{
    /**
     * @var string
     */
    protected $locale;

    /**
     * @var string
     */
    protected $fallback_locale;

    /**
     * LocalizationLoader constructor.
     * @param $locale
     * @param $fallback_locale
     */
    public function __construct($locale, $fallback_locale)
    {
        $this->setLocale($locale)->setFallbackLocale($fallback_locale);
    }

    /**
     * @param $path
     * @return array|mixed
     * @throws DebugException
     */
    public function load($path)
    {
        if($this->checkLocaleDir($this->locale)) {
            return $this->loadLocalizationFile($this->locale, $path);
        } else {
            if($this->checkLocaleDir($this->fallback_locale)) {
                return $this->loadLocalizationFile($this->fallback_locale, $path);
            } else {
                throw new DebugException("Резервный язык локализации не найден!");
            }
        }

        return [];
    }

    /**
     * @param $locale
     * @param $file
     * @return mixed
     * @throws DebugException
     */
    private function loadLocalizationFile($locale, $file)
    {
        $file_to_load = RESOURCES_DIR . "lang/" . $locale . "/" . $file . ".php";

        if(file_exists($file_to_load)) {
            return require_once($file_to_load);
        } else {
            throw new DebugException("Не удалось загрузить файл локализации ". $file);
        }
    }

    /**
     * @param $locale
     * @return bool
     */
    private function checkLocaleDir($locale)
    {
        return is_dir(RESOURCES_DIR . "lang/" . $locale);
    }

    /**
     * @param mixed $locale
     * @return LocalizationLoader
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * @param mixed $fallback_locale
     * @return LocalizationLoader
     */
    public function setFallbackLocale($fallback_locale)
    {
        $this->fallback_locale = $fallback_locale;
        return $this;
    }
}