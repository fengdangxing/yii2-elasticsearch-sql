# yii2-elasticsearch-sql
链式查询es 建立es模型继承YiiElasticsearchSql 基类即可
```php
#效果
$merge = new Orders();
$query = $merge
    ->andWhere(['TransportName' => '万邦-欧洲速邮专线平邮-带电'])
    ->andWhere(['shop_id' => 42])
    ->notWhere(['shop_id' => 42])
    ->betweenWhere('AddTime', '2019-12-23 16:11:00', '2019-12-23 16:11:40')
    ->likeWhere('TransportName', '%云途% AND %云途% NOT %标快% NOT %标快% NOT %标快% NOT %标快%')
    ->compareWhere('PayTime', 'gt', '2019-12-23 15:20:40')
    ->compareWhere('PayTime', 'lt', '2019-12-23 15:20:44')
    ->setPage(0, 20)
    ->orderBy('PayTime', 'asc')
    ->select();
