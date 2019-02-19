<?php

namespace MyUCP\Routing;

use MyUCP\Request\Request;

class CsrfToken
{
    /**
     * @var string
     */
    protected $token;

    /**
     * @var Request
     */
    protected $request;

    /**
     * CsrfToken constructor.
     * @param Request $request
     * @throws \Exception
     */
    public function __construct(Request $request)
    {
        $this->request = $request;

        if(! session()->has("_csrf_token")) {
            $this->generate();
        } else {
            $this->token = session("_csrf_token");
        }
    }

    /**
     * CSRF Token generating
     *
     * @return void
     * @throws \Exception
     */
    public function generate()
    {
        $this->token = md5($this->request->ip() . config()->app_key . random_bytes(32));

        session()->put("_csrf_token", $this->token);
    }

    /**
     * Check if the token from request equal current token
     *
     * @return bool
     */
    public function check()
    {
        $result = $this->token == $this->getTokenFromRequest($this->request);

        return $result;
    }

    /**
     * @param Request $request
     *
     * @return string
     */
    public function getTokenFromRequest($request)
    {
        return $this->request->input("_token") ?: $request->headers["X-CSRF-TOKEN"];
    }

    /**
     *
     *
     * @return string
     */
    public function token()
    {
        return $this->token;
    }
}