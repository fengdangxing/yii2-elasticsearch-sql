<?php

namespace fengdangxing\db;

use yii\elasticsearch\ActiveQuery;

/**
 * @desc es查询类
 * @author 1
 * @version v2.1
 * @date: 2021/07/19
 * ${PARAM_DOC}
 * @return ${TYPE_HINT}
 * ${THROWS_DOC}
 */
trait TraitSelectAgg
{
    protected $termsBy;

    /**
     * @desc 定义查找对象
     * @author 1
     * @version v2.1
     * @date: 2021/07/24
     * @return ActiveQuery the newly created [[ActiveQuery]] instance.
     */
    public static function checkAdd()
    {
        return self::find();
    }

    /**
     * @desc 分桶
     * @author 1
     * @version v2.1
     * @date: 2021/07/24
     * @param $name |分桶名称和分桶字段
     * @return TraitSelectAgg
     */
    public function termsBy($name)
    {
        $this->termsBy[] = array(
            $name => [
                'terms' => [
                    'field' => $name,
                   // 'size' => 10000,
                ],
            ]
        );
        return $this;
    }

    /**
     * @desc 获取聚合列表 默认返回object对象 返回数组 添加->asArray()
     * @author search 与 all 区别在于 all是在search基础上处理再拿出结果
     * @version v2.1
     * @date: 2021/07/24
     * @return array
     * @throws \yii\elasticsearch\Exception
     */
    public function selectAgg()
    {
        $es_query = self::checkAdd();
        //聚合分桶
        if (!empty($this->termsBy)) {
            foreach ($this->termsBy as $key => $value) {
                $es_query->addAggregate(key($value), $value[key($value)]);
            }
        }
        // 匹配查询
        if ($this->getQueryWhere() && !empty($this->QueryWhere)) {
            $es_query->query($this->QueryWhere);
        }
        // 分组
        $res = $es_query->asArray()->search();
        return $res['aggregations'];
    }
}
