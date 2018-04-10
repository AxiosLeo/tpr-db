<?php
/**
 * @author: axios
 *
 * @email: axiosleo@foxmail.com
 * @blog:  http://hanxv.cn
 * @datetime: 2018/4/10 14:16
 */

namespace tpr\db\exception;


class BindParamException extends DbException
{
    public function __construct($message, $config, $sql, $bind, $code = 10502)
    {
        $this->setData('Bind Param', $bind);
        parent::__construct($message, $config, $sql, $code);
    }
}