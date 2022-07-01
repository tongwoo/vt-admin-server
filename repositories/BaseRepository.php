<?php

namespace app\repositories;

use app\common\utils\Pagination;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

abstract class BaseRepository
{
    /**
     * 构建查询条件
     * @param array $params 查询参数
     * @return ActiveQuery
     */
    abstract function buildCondition(array $params): ActiveQuery;

    /**
     * 参数查询
     * @param array $params 查询参数
     * @return array
     */
    public function findByParams(array $params = []): array
    {
        return $this->buildCondition($params)->all();
    }

    /**
     * 分页查询
     * @param Pagination  $pagination 分页实例
     * @param ActiveQuery $query      查询
     * @return ActiveQuery
     */
    public function pagination(Pagination $pagination, ActiveQuery $query): ActiveQuery
    {
        $pagination->totalCount = $query->count();
        return $query->offset($pagination->offset)
            ->limit($pagination->limit);
    }
}
