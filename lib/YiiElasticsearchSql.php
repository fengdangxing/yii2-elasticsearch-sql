<?php

namespace fengdangxing;

use yii\elasticsearch\ActiveRecord;

class YiiElasticsearchSql extends ActiveRecord
{
    protected $andWhere;
    protected $notWhere;
    protected $betweenWhere;
    protected $compareWhere;
    protected $likeWhere;
    protected $orderBy;
    protected $QueryWhere = [];
    protected $bool = [];
    protected $offset = 0;
    protected $limit = 20;

    /**
     * @desc 这个就是第二步配置的组件的名字（key值）
     * @author 1
     * @version v2.1
     * @date: 2020/10/18
     * @return null|object|\yii\elasticsearch\Connection
     * @throws \yii\base\InvalidConfigException
     */
    public static function getDb()
    {
        return \Yii::$app->get('elasticsearch');
    }

    /**
     * @desc 文档类型-7取消该类型
     * @author 1
     * @version v2.1
     * @date: 2020/10/18
     * @return string
     */
    public static function type()
    {
        return '_doc';
    }

    /**
     * @return array This model's mapping
     */
    public static function mapping()
    {
        return [
            'properties' => [
                'first_name' => ['type' => 'text'],
                'last_name' => ['type' => 'text'],
                'order_ids' => ['type' => 'keyword'],
                'email' => ['type' => 'keyword'],
                'registered_at' => ['type' => 'date'],
                'updated_at' => ['type' => 'date'],
                'status' => ['type' => 'keyword'],
                'is_active' => ['type' => 'boolean'],
            ]
        ];
    }

    /**
     * @desc 更新索引结构
     * @author 1
     * @version v2.1
     * @date: 2020/10/19
     * @throws \yii\base\InvalidConfigException
     */
    public static function updateMapping()
    {
        $db = static::getDb();
        $command = $db->createCommand();
        $command->setMapping(static::index(), static::type(), static::mapping());
    }

    /**
     * @desc 创建索引
     * @author 1
     * @version v2.1
     * @date: 2020/10/19
     * @throws \yii\base\InvalidConfigException
     */
    public static function createIndex()
    {
        $db = static::getDb();
        $command = $db->createCommand();
        $command->createIndex(static::index(), [
            //'aliases' => [ /* ... */ ],
            'mappings' => static::mapping(),
            //'settings' => [ /* ... */ ],
        ]);
    }

    /**
     * @desc 删除索引
     * @author 1
     * @version v2.1
     * @date: 2020/10/19
     * @throws \yii\base\InvalidConfigException
     */
    public static function deleteIndex()
    {
        $db = static::getDb();
        $command = $db->createCommand();
        $command->deleteIndex(static::index(), static::type());
    }

    /**
     * @desc 增加数据
     * @author 1
     * @version v2.1
     * @date: 2020/10/20
     * @param array $data
     * @param string $id
     * @throws \yii\base\InvalidConfigException
     */
    public static function addData($data = array(), $id = '')
    {
        $db = static::getDb();
        $command = $db->createCommand();
        $command->insert(static::index(), static::type(), $data, $id);
    }

    /**
     * 单个
     * 默认返回object对象 返回数组 添加->asArray()
     */
    public function getOne($query = [])
    {
        $es_query = self::find();

        // 匹配查询
        if ($query && !empty($query)) {
            $es_query->query($query);
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
            $this->andWhere[] = ["match" => $condition];
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
            $this->notWhere[] = ["match" => $condition];
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
     * @return Es
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
            $this->likeWhere = ['query' => "$field:($value)"];
            // "query":"search_word:(*中国* NOT *美国* AND *VIP* AND *经济* OR *金融*)",
            // "default_operator":"and"
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
     * @return Es
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
     * * 列表
     * 默认返回object对象 返回数组 添加->asArray()
     *  * search 与 all 区别在于 all是在search基础上处理再拿出结果
     */
    public function select()
    {
        $es_query = self::find();
        // 匹配查询
        if ($this->getQueryWhere() && !empty($this->getQueryWhere())) {
            $es_query->query($this->getQueryWhere());
        }
        // 排序
        if ($this->orderBy && !empty($this->orderBy)) {
            $es_query->orderby($this->orderBy);
        }
        $count = $es_query->search();
        // 分组
        $res = $es_query->offset($this->offset)->limit($this->limit)->asArray()->all();
        $list = array_column($res, '_source');

        return ['list' => $list, 'total' => $count['hits']['total']];
    }

    /**
     * 获取聚合列表
     * 默认返回object对象 返回数组 添加->asArray()
     * search 与 all 区别在于 all是在search基础上处理再拿出结果
     *
     */
    public function getAggList($aggregate_name, $addAggregate_arr = [], $query = [], $offset = 0, $limit = 20)
    {
        $es_query = self::find();// 聚合
        if ($addAggregate_arr && !empty($addAggregate_arr)) {
            $es_query->addAggregate($aggregate_name, $addAggregate_arr);
        }
        // 匹配查询
        if ($query && !empty($query)) {
            $es_query->query($query);
            // 分组
            $res = $es_query->offset($offset)->limit($limit)->search();
            return ['list' => $res['hits']['hits'], $aggregate_name => $res['aggregations'][$aggregate_name]];

        }
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
            $this->QueryWhere['query_string'] = $this->likeWhere;
        }
        if ($this->bool) {
            $this->QueryWhere = [
                "bool" => $this->bool
            ];
        }
        return $this->QueryWhere;
    }
}
