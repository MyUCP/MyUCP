<?php
/**
 * MyUCP
 */

namespace Extensions\Validate;

use MyUCP\Extension\BaseExtension;

class Validate extends BaseExtension
{
    public function run()
    {
        //
    }

    /**
     * Разрешенные символы: А-Я а-я
     * Длина: 2-16
     *
     * @param string $firstname
     * @return bool
     */
    public function firstName($firstname)
    {
        return (bool) preg_match("/^([А-ЯЁ]{1})([а-яё]{1,15})$/u", $firstname);
    }

    /**
     * Разрешенные символы: А-Я а-я
     * Длина: 2-16
     *
     * @param string $lastname
     * @return bool
     */
    public function lastName($lastname)
    {
        return (bool) preg_match("/^([А-ЯЁ]{1})([а-яё]{1,15})$/u", $lastname);
    }

    /**
     * @param string $email
     * @return bool
     */
    public function email($email)
    {
        return (bool) preg_match("/^([a-z0-9_\.-]+)@([a-z0-9_\.-]+)\.([a-z\.]{2,6})$/", $email);
    }

    /**
     * Разрешенные символы: A-Z a-z 0-9
     * Длина: 6-32
     *
     * @param string $password
     * @return bool
     */
    public function password($password)
    {
        return (bool) preg_match("/^[a-zA-Z0-9,\.!?_-]{6,32}$/", $password);
    }

    /**
     * Разрешенные символы: 0-9 и .
     * Длина: 1
     *
     * @param string $money
     * @return bool
     */
    public function money($value)
    {
        return (bool) preg_match("/^([0-9]{1,10})(\.[0-9]{2})?$/", $value);
    }

    /**
     * Разрешенные символы: 0-9 и .
     * Длина: 1
     *
     * @param string $ip
     * @return bool
     */
    public function ip($ip)
    {
        return (bool) preg_match("/^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})$/", $ip);
    }

    /**
     * Разрешенные символы: a-f и 0-9
     * Длина: 32
     *
     * @param string $md
     * @return bool
     */
    public function md5($md)
    {
        return (bool) preg_match("/^([a-f0-9]{32})$/", $md);
    }
}