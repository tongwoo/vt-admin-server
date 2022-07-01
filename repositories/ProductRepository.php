<?php

namespace app\repositories;

use app\common\utils\Pagination;
use app\models\ar\business\Product;
use Yii;
use yii\db\ActiveQuery;
use DateTime;

/**
 * 产品
 */
class ProductRepository extends BaseRepository
{
    public function __construct()
    {
        $this->query = Yii::$container->get(Product::class)->find();
    }

    /**
     * 构建查询条件
     * @param array $params 查询参数
     * @return ActiveQuery
     */
    protected function buildCondition(array $params): ActiveQuery
    {
        $query = $this->query;
        //所属类目
        if (isset($params['categoryId'])) {
            $query->andFilterWhere(['category_id' => $params['categoryId']]);
        }
        //名称
        if (isset($params['name'])) {
            $query->andFilterWhere(['LIKE', 'name', $params['name']]);
        }
        //性别
        if (isset($params['gender'])) {
            $query->andFilterWhere(['gender' => $params['gender']]);
        }
        //日期查询
        if (isset($params['beginDate']) && isset($params['endDate'])) {
            $beginDate = DateTime::createFromFormat('Y-m-d',$params['beginDate']);
            $endDate = DateTime::createFromFormat('Y-m-d',$params['endDate']);
            if($beginDate && $endDate){
                $query->andFilterWhere(['BETWEEN', 'date', $params['beginDate'], $params['endDate']]);
            }
        }
        //日期查询
        if (isset($params['beginDate']) && isset($params['endDate'])) {
            $beginDate = DateTime::createFromFormat('Y-m-d',$params['beginDate']);
            $endDate = DateTime::createFromFormat('Y-m-d',$params['endDate']);
            if($beginDate && $endDate){
                $query->andFilterWhere(['BETWEEN', 'time', $params['beginDate'], $params['endDate']]);
            }
        }
        //关联 - 种类
        $query->with('category');

        return $query;
    }
}