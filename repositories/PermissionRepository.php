<?php

namespace app\repositories;

use app\common\utils\Pagination;
use app\models\business\Permission;
use Yii;
use yii\db\ActiveQuery;
use yii\db\Expression;
use DateTime;

/**
 * 权限
 */
class PermissionRepository extends BaseRepository
{
    /**
     * 构建查询条件
     * @param array $params 查询参数
     * @return ActiveQuery
     */
    public function buildCondition(array $params = []): ActiveQuery
    {
        $query = Permission::find();
        //父权限
        if (isset($params['parentId'])) {
            $query->andFilterWhere(['parent_id' => $params['parentId']]);
        }
        //权限名称
        if (isset($params['name'])) {
            $query->andFilterWhere(['LIKE', 'name', $params['name']]);
        }
        //权限描述
        if (isset($params['description'])) {
            $query->andFilterWhere(['LIKE', 'description', $params['description']]);
        }
        //规则名称
        if (isset($params['ruleName'])) {
            $query->andFilterWhere(['LIKE', 'rule_name', $params['ruleName']]);
        }
        return $query;
    }
}
