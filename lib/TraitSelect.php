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
trait TraitSelect
{
    protected $andWhere;
    protected $notWhere;
    protected $betweenWhere;
    protected $compareWhere;
    protected $likeWhere;
    protected $inWhere;
    protected $notInWhere;
    protected $orderBy;
    protected $aggregate;
    protected $QueryWhere = [];
    protected $bool = [];
    protected $offset = 0;
    protected $limit = 20;

    /**
     * @desc 定义查找对象
     * @author 1
     * @version v2.1
     * @date: 2021/07/24
     * @return ActiveQuery the newly created [[ActiveQuery]] instance.
     */
    public static function check()
    {
        return self::find();
    }
    /**
     * 单个
     * 默认返回object对象 返回数组 添加->asArray()
     */
    public function getOne()
    {
        $es_query = self::check();

        // 匹配查询
        if ($this->getQueryWhere() && !empty($this->QueryWhere)) {
            $es_query->query($this->QueryWhere);
        }
        // 分组
        $res = $es_query->one();

        return $res;
    }

    /**
     * @desc 搜索条件
     * @author 1
     * @version v2.1
     * @date: 2020/10/20
     * @param $condition ['field'=>111]
     * @return $this
     */
    public function andWhere($condition = array())
    {
        if ($condition) {
            $this->andWhere[] = $this->nestedQuery(["match" => $condition]);
        }
        return $this;
    }

    /**
     * @desc 搜索条件
     * @author 1
     * @version v2.1
     * @date: 2020/10/20
     * @param $condition ['field'=>111]
     * @return $this
     */
    public function notWhere($condition = array())
    {
        if ($condition) {
            $this->notWhere[] = $this->nestedQuery(["match" => $condition]);
        }
        return $this;
    }

    /**
     * @desc 搜索条件
     * @author 1
     * @version v2.1
     * @date: 2020/10/20
     * @param $field
     * @param $from_time
     * @param $to_time
     * @return $this
     */
    public function betweenWhere($field, $from_time, $to_time)
    {
        if ($field && $from_time && $to_time) {
            $this->betweenWhere['range'][$field] = ["from" => $from_time, 'to' => $to_time];
        }
        return $this;
    }

    /**
     * @desc 比较条件
     * @author 1
     * @version v2.1
     * @date: 2020/10/20
     * @param $field
     * @param $compare
     * @param $value
     * @return $this
     */
    public function compareWhere($field, $compare, $value)
    {
        $this->compareWhere['range'][$field][$compare] = $value;
        return $this;
    }

    /**
     * @desc like查询
     * @author 1
     * @version v2.1
     * @date: 2020/10/20
     * @param $field
     * @param $value
     * @return $this
     */
    public function likeWhere($field, $value)
    {
        if ($field && $value) {
            $value = str_replace('%', '*', $value);
            $this->likeWhere['query_string'] = ['fields' => [$field], 'query' => "$value"];
            // "query":"search_word:(*中国* NOT *美国* AND *VIP* AND *经济* OR *金融*)",
            // "default_operator":"and"
        }
        return $this;
    }

    /**
     * @desc in查询
     * @author 1
     * @version v2.1
     * @date: 2020/10/21
     * @param $field
     * @param array $value
     * @return TraitSelect
     */
    public function inWhere($field, $value = array())
    {
        if ($field && $value) {
            $this->inWhere = [$field => $value];
        }
        return $this;
    }

    /**
     * @desc not in查询
     * @author 1
     * @version v2.1
     * @date: 2020/10/21
     * @param $field
     * @param array $value
     * @return TraitSelect
     */
    public function notInWhere($field, $value = array())
    {
        if ($field && $value) {
            $this->notInWhere = [$field => $value];
        }
        return $this;
    }

    /**
     * @desc 排序
     * @author 1
     * @version v2.1
     * @date: 2020/10/20
     * @param $field
     * @param string $order
     * @return $this
     */
    public function orderBy($field, $order = 'desc')
    {
        if ($field) {
            $this->orderBy[$field] = ['order' => $order];
        }
        return $this;
    }

    /**
     * @desc 设置分页
     * @author 1
     * @version v2.1
     * @date: 2020/10/20
     * @param int $offset
     * @param int $limit
     * @return $this
     */
    public function setPage($offset = 0, $limit = 20)
    {
        if ($limit) {
            $this->limit = $limit;
        }
        if ($offset) {
            $this->offset = $offset;
        }
        return $this;
    }

    /**
     * @desc 列表 默认返回object对象 返回数组 添加->asArray()
     * @author 1 search 与 all 区别在于 all是在search基础上处理再拿出结果
     * @version v2.1
     * @date: 2021/07/24
     * @return array
     * @throws \yii\elasticsearch\Exception
     */
    public function select()
    {
        $es_query = self::check();
        // 匹配查询
        if ($this->getQueryWhere() && !empty($this->QueryWhere)) {
            $es_query->query($this->QueryWhere);
        }
        // 排序
        if ($this->orderBy && !empty($this->orderBy)) {
            $es_query->orderby($this->orderBy);

        }
        $count = $es_query->search();
        $res = $es_query->offset($this->offset)->limit($this->limit)->asArray()->all();
        $list = array_column($res, '_source');
        return ['list' => $list, 'total' => $count['hits']['total']];
    }

    /**
     * @desc 获取搜索条件
     * @author 1
     * @version v2.1
     * @date: 2020/10/20
     */
    private function getQueryWhere()
    {
        if ($this->andWhere) {
            $this->bool['must'] = $this->andWhere;
        }
        if ($this->compareWhere) {
            $this->bool['must'] = $this->compareWhere;
        }
        if ($this->betweenWhere) {
            $this->bool['filter'] = $this->betweenWhere;
        }
        if ($this->notWhere) {
            $this->bool['must_not'] = $this->notWhere;
        }
        if ($this->likeWhere) {
            $this->bool['must'] = $this->likeWhere;
        }
        if ($this->inWhere) {
            $this->bool['must'][]['terms'] = $this->inWhere;
        }
        if ($this->notInWhere) {
            $this->bool['must'][]['terms'] = $this->notInWhere;
        }
        if ($this->bool) {
            $this->QueryWhere = [
                "bool" => $this->bool
            ];
        }
        return $this->QueryWhere;
    }

    /**
     * @desc nested 查询类型判断
     * @param array $where 查询条件
     * @return array
     * @version v3.1
     * @date 2021/09/08
     * @author Jero
     */
    private function nestedQuery($where)
    {
        $whereKey = array_keys($where)[0];

        $nested = [];
        foreach ($where[$whereKey] as $key => $value) {

            if (!is_array($value)) return $where;

            $nestedKey = array_keys($value);
            $nestedValue = array_values($value);

            //索引数组则跳出循环
            if (is_integer($nestedKey[0])) return $where;

            $nestedPath = $key . '.' . $nestedKey[0];

            $nested = [
                "nested" => [
                    "path" => $key,
                    "query" => [
                        "bool" => [
                            "must" => [
                                "{$whereKey}" => [
                                    $nestedPath => $nestedValue[0],
                                ]
                            ],

                        ],
                    ],
                ],
            ];
        }

        return $nested;
    }
}
