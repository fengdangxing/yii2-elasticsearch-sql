<?php

namespace fengdangxing\db;

/**
 * @desc es字段类型
 * @author 1
 * @version v2.1
 * @date: 2021/07/19
 * ${PARAM_DOC}
 * @return ${TYPE_HINT}
 * ${THROWS_DOC}
 */
trait TraitType
{
    //核心数据类型
    public static $text = 'text';
    public static $keyword = 'keyword';//关键字家庭，其中包括keyword，constant_keyword，和wildcard。

    public static $long = 'long';//带符号的64位整数，最小值-263，最大值263-1
    public static $integer = 'integer';//带符号的32位整数，最小值-231，最大值231-1
    public static $short = 'short';//带符号的16位整数，最小值-32768，最大值32767
    public static $byte = 'byte';//带符号的8位整数，最小值-128，最小值127
    public static $double = 'double';//双精度64位IEEE 754 浮点数
    public static $float = 'float';//单精度32位IEEE 754 浮点数

    public static $date = 'date';//日期类型，包括date和 date_nanos。
}
