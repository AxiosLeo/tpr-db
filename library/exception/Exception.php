<?php
/**
 * @author: axios
 *
 * @email: axiosleo@foxmail.com
 * @blog:  http://hanxv.cn
 * @datetime: 2018/4/10 10:22
 */

namespace tpr\db\exception;


class Exception extends \Exception
{
    protected $data = [];

    final protected function setData($label, array $data)
    {
        $this->data[$label] = $data;
    }

    final public function getData()
    {
        return $this->data;
    }
}