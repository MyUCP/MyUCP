<?php

namespace MyUCP\Routing;

use MyUCP\Request\Request;
use MyUCP\Session\Session;

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
     * @var Session
     */
    protected $session;

    /**
     * CsrfToken constructor.
     *
     * @param Request $request
     * @param Session $session
     *
     * @throws \Exception
     */
    public function __construct(Request $request, Session $session)
    {
        $this->request = $request;
        $this->session = $session;

        if(! $this->session->has($this->getTokenKey())) {
            $this->generate();
        } else {
            $this->token = $this->session->get($this->getTokenKey());
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

        $this->session->put($this->getTokenKey(), $this->token);
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

    /**
     * @return string
     */
    public function getTokenKey()
    {
        return "_csrf_token";
    }
}