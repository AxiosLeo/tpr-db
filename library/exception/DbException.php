<?php
/**
 * @author: axios
 *
 * @email: axiosleo@foxmail.com
 * @blog:  http://hanxv.cn
 * @datetime: 2018/4/10 14:12
 */

namespace tpr\db\exception;


class DbException extends Exception
{
    /**
     * DbException constructor.
     * @param string    $message
     * @param array     $config
     * @param string    $sql
     * @param int       $code
     */
    public function __construct($message, array $config, $sql, $code = 10500)
    {
        $this->message = $message;
        $this->code    = $code;

        $this->setData('Database Status', [
            'Error Code'    => $code,
            'Error Message' => $message,
            'Error SQL'     => $sql,
        ]);

        $this->setData('Database Config', $config);
    }

}