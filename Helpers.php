<?php


if (! function_exists('objectToArray')) {
    /**
     * 对象转换数组
     *
     * @param $e StdClass对象实例
     * @return array|void
     */
    function objectToArray($e)
    {
        $e = (array)$e;
        foreach ($e as $k => $v) {
            if (gettype($v) == 'resource') return;
            if (gettype($v) == 'object' || gettype($v) == 'array')
                $e[$k] = (array)objectToArray($v);
        }
        return $e;
    }
}
