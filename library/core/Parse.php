<?php

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
        }

        return strtolower(trim(preg_replace('/[A-Z]/', '_\\0', $name), '_'));
    }

    public static function arrayToString($array, $glue)
    {
        $str = '';
        $n   = 0;
        foreach ($array as $k => $v) {
            if (0 !== $n) {
                $str .= $glue;
            }
            if (\is_array($v)) {
                $str = $str . self::arrayToString($v, $glue);
            } else {
                $str .= $v;
            }
            ++$n;
        }

        return $str;
    }
}
