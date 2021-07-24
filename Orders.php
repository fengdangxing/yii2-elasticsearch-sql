<?php
/**
 * @desc 功能描述
 * @author 1
 * @version v2.1
 * @date: 2020/09/17
 * Time: 15:22
 */

namespace es;

use fengdangxing\YiiElasticsearchSql;

/**
 * @desc es 用户模型类
 * @author 1
 * @version v2.1
 * @date: 2020/09/17
 * Class User
 * @package backend\models
 */
class Orders extends YiiElasticsearchSql
{
    /**
     * @desc 索引
     * @author 1
     * @version v2.1
     * @date: 2020/10/18
     * @return string
     */
    public static function index()
    {
        return 'oms_order_list_test_2';
    }


    public function attributes()
    {
        return ['order_sn', 'country', 'shop_id', 'create_time'];
    }

    /**
     * @desc 设置索引字段
     * @author 1
     * @version v2.1
     * @date: 2020/10/18
     * @return array
     */
    public static function mapping()
    {
        return [
            'properties' => [
                'order_sn' => ['type' => self::$text],
                'country' => ['type' => self::$keyword],
                'shop_id' => ['type' => self::$integer],
                'create_time' => ['type' => self::$date],
            ]
        ];
    }
}
