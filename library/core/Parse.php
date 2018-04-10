<?php
/**
 * @author: axios
 *
 * @email: axiosleo@foxmail.com
 * @blog:  http://hanxv.cn
 * @datetime: 2018/4/10 13:25
 */

namespace tpr\db\core;


class Parse
{
    public static function parseName($name, $type = 0, $ucfirst = true)
    {
        if ($type) {
            $name = preg_replace_callback('/_([a-zA-Z])/', function ($match) {
                return strtoupper($match[1]);
            }, $name);
            return $ucfirst ? ucfirst($name) : lcfirst($name);
        } else {
            return strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $name), "_"));
        }
    }

    public static function arrayToString($array, $glue)
    {
        $str = "";
        $n = 0;
        foreach ($array as $k => $v) {
            if ($n !== 0) {
                $str .= $glue;
            }
            if (is_array($v)) {
                $str = $str . self::arrayToString($v, $glue);
            } else {
                $str .= $v;
            }
            $n++;
        }
        return $str;
    }
}