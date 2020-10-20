<?php
/**
 * @desc 功能描述
 * @author 1
 * @version v2.1
 * @date: 2020/09/17
 * Time: 15:22
 */

namespace es;

/**
 * @desc es 用户模型类
 * @author 1
 * @version v2.1
 * @date: 2020/09/17
 * Class User
 * @package backend\models
 */
class Orders extends Es
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
        return 'test_orders_es_db';
    }


    public function attributes()
    {
        return ['OrderCode', 'target_country', 'shop_id', 'AddTime'];
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
                'OrderCode' => ['type' => 'text'],
                'target_country' => ['type' => 'keyword'],
                'shop_id' => ['type' => 'keyword'],
                'AddTime' => ['type' => 'keyword'],
            ]
        ];
    }
}
