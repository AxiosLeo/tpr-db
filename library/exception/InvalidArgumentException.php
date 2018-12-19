<?php
/**
 * @author  : axios
 * @email   : axiosleo@foxmail.com
 * @blog    :  http://hanxv.cn
 * @datetime: 2018/4/10 10:43
 */

namespace tpr\db\exception;

use Throwable;

class InvalidArgumentException extends Exception
{
    protected $arguments = [];

    /**
     * InvalidArgumentException constructor.
     *
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct($message = "InvalidArgument", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->message = $message;
        $this->code    = $code;

        $this->setData('InvalidArgument', [
            'Error Code'    => $code,
            'Error Message' => $message
        ]);
    }

    public function setArguments($arguments = [])
    {
        $this->arguments = $arguments;
    }

    public function getArguments()
    {
        return $this->arguments;
    }
}