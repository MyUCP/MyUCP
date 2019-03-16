<?php

namespace MyUCP\Localization;

use MyUCP\Debug\DebugException;
use MyUCP\Support\App;

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
     *
     * @param $locale
     * @param $fallback_locale
     */
    public function __construct($locale = null, $fallback_locale = null)
    {
        $this->setLocale($locale ?? config('locale'))
             ->setFallbackLocale($fallback_locale ?? config('fallback_locale'));
    }

    /**
     * @param $path
     *
     * @throws DebugException
     *
     * @return array|mixed
     */
    public function load($path)
    {
        if ($this->checkLocaleDir($this->locale)) {
            return $this->loadLocalizationFile($this->locale, $path);
        } else {
            if ($this->checkLocaleDir($this->fallback_locale)) {
                return $this->loadLocalizationFile($this->fallback_locale, $path);
            } else {
                throw new DebugException("Fallback locale [{$this->fallback_locale}] not found!");
            }
        }

        return [];
    }

    /**
     * @param $locale
     * @param $file
     *
     * @throws DebugException
     *
     * @return mixed
     */
    private function loadLocalizationFile($locale, $file)
    {
        $file_to_load = App::resourcesPath('lang/'.$locale.DIRECTORY_SEPARATOR.$file.'.php');

        if (file_exists($file_to_load)) {
            return require_once $file_to_load;
        } else {
            throw new DebugException("Cannot load localization file [$file]");
        }
    }

    /**
     * @param $locale
     *
     * @return bool
     */
    private function checkLocaleDir($locale)
    {
        return is_dir(App::resourcesPath('lang/'.$locale));
    }

    /**
     * @param mixed $locale
     *
     * @return LocalizationLoader
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @param mixed $fallback_locale
     *
     * @return LocalizationLoader
     */
    public function setFallbackLocale($fallback_locale)
    {
        $this->fallback_locale = $fallback_locale;

        return $this;
    }
}
