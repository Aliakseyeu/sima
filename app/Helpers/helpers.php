<?php

function msgGen(array ...$keys): string
{
    foreach($keys as &$key){
        $key = array_map(function($k){
            return __('messages.'.$k);
        }, $key);
        $key = mb_ucfirst(mb_strtolower(implode(' ', $key)));
    }
    unset($key);
    return implode('. ', $keys);
}

function mb_ucfirst(string $string, string $encoding = 'utf-8'): string
{
    $strlen = mb_strlen($string, $encoding);
    $firstChar = mb_substr($string, 0, 1, $encoding);
    $then = mb_substr($string, 1, $strlen - 1, $encoding);
    return mb_strtoupper($firstChar, $encoding) . $then;
}