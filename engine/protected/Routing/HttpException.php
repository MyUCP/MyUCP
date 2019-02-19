<?php

namespace MyUCP\Routing;

use Exception;
use MyUCP\Response\Response;

class HttpException extends Exception
{
    /**
     * @var string
     */
    protected $message;

    /**
     * @var int
     */
    protected $code = Response::HTTP_NOT_FOUND;

    /**
     * HttpException constructor.
     *
     * @param int $code
     * @param int $message
     */
    public function __construct($code = null, $message = null)
    {
        $code = $code ?? $this->code;

        $message = $message ?? Response::$statusTexts[$code];

        parent::__construct($message, $code);

        $this->code = $code;
        $this->message = $message;
    }

    /**
     * @return bool|mixed
     */
    public function getResponse()
    {
        try {
            $view = $this->loadView();

            return $view;
        } catch (Exception $e) {
            $this->code = 500;

            $view = $this->loadView();

            return $view;
        }
    }

    /**
     * @return bool|mixed
     */
    protected function loadView()
    {
        return view("errors/" . $this->code, ['message' => $this->message, "code" => $this->code], true);
    }
}