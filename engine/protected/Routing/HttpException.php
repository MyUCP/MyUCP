<?php

class HttpException extends Exception
{
    /**
     * @var string
     */
    protected $message;

    /**
     * @var int
     */
    protected $code = 404;

    /**
     * HttpException constructor.
     *
     * @param $code
     * @param null $message
     */
    public function __construct($code, $message = null)
    {
        $this->code = $code;
        $this->message = $message;
    }

    /**
     * @return bool|Debug|mixed
     */
    public function getResponse()
    {
        if(($view = $this->loadView()) === false)
            return new Debug($this->code, $this->message);

        return $view;
    }

    /**
     * @return bool|mixed
     */
    protected function loadView()
    {
        return view("errors/" . $this->code, ['message' => $this->message, "code" => $this->code], true);
    }
}