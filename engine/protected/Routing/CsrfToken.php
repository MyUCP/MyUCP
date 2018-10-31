<?php

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
     */
    public function __construct(Request $request)
    {
        if(!app("session")->has("_csrf_token")) {
            $this->generate();
        } else {
            $this->token = session("_csrf_token");
        }
    }

    /**
     * CSRF Token generating
     *
     * @return void
     * @throws Exception
     */
    public function generate()
    {
        $this->token = md5(Request::ip() . config()->app_key . random_bytes(32));

        app('session')->put("_csrf_token", $this->token);
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
        return Request::input("_token") ?: $request->headers["X-CSRF-TOKEN"];
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