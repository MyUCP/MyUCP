<?php
/*
* MyUCP
*/

class Debug
{
    /**
     * @var string
     */
    public $message = "Unknown error";

    /**
     * @var int
     */
    public $code = 500;

    /**
     * @var string
     */
    public $file;

    /**
     * @var int
     */
    public $line = 0;

    /**
     * @var array
     */
    public $trace = [];

    /**
     * Debug constructor.
     *
     * @param string $message
     * @param string $file
     * @param int $line
     * @param int $code
     * @param array $trace
     */
    public function __construct($message, $file = "", $line = 0, $code = 0, $trace = [])
    {
        $this->message = $message;
        $this->file = $file;
        $this->line = $line;
        $this->code = $code;

        if(empty($trace)) {
            $this->trace = debug_backtrace();
        } else {
            $this->trace = $trace;
        }
    }

    /**
     * @return array
     */
    public function traceToReadable()
    {
        $result = [];

        foreach ($this->trace as $trace) {

            if(!isset($trace['file']))
                continue;

            $at = "";

            if(isset($trace['class']))
                $at .= "<abbr title='". $trace['class'] ."::class'>". $trace['class'] . "</abbr>";

            if(isset($trace['type']))
                $at .= $trace['type'];

            if(isset($trace['function']))
                $at .= $trace['function'];

            if(empty($trace['args'])) {
                $at .= "()";
            } else {
                $at .= "(";

                foreach ($trace['args'] as $num => $arg) {
                    if(is_object($arg)) {
                        $at .= "<abbr title='" . get_class($arg) . "::class'>object(" . get_class($arg) . ")</abbr>";
                    } elseif(is_array($arg)) {
                        $at .= "<abbr title='array of ". count($arg) ."'>array()</abbr>";
                    } else {
                        $at .= "<abbr title='$arg'>". gettype($arg) ."(" . basename($arg) . ")</abbr>";
                    }

                    if($num != count($trace['args']) - 1)
                        $at .= ", ";
                }

                $at .= ")";
            }

            $in = basename($trace['file']) . " строка " . $trace['line'];
            $full_in = $trace['file'] . " строка " . $trace['line'];

            $result[] = [
                "at" => $at,
                "in" => $in,
                "full_in" => $full_in,
            ];
        }

        return $result;
    }

    /**
     * @return string
     */
    private function visualizeCode()
    {
        $result = "";
        $file = file($this->file);

        if($this->line == 0) {
            $start = 0;
        } elseif($this->line - 12 >= 0) {
            $start = $this->line - 12;
        } else {
            $start = 0;
        }

        for($i = $start; $i < $this->line; $i++) {
            $result .= $file[$i];
        }


        $limit = (count($file) < $this->line + 11 ? count($file) : $this->line + 10);

        for($i = $this->line; $i < $limit; $i++) {
            $result .= $file[$i];
        }

        $result = str_replace("<?php", "&lt;?php", $result);

        return $result;
    }

    /**
     * @return string
     * @throws DebugException
     */
    public function __toString()
    {
        if(!config()->debug_mode) {
            return (new HttpException($this->code, $this->message))->getResponse();
        }

        $traces = $this->traceToReadable();
        $lines = $this->visualizeCode();

        return (string) require_once("DebugViewTemplate.php");
    }
}