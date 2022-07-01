<?php

namespace app\repositories;

use app\common\utils\Pagination;
use app\models\business\Role;
use Yii;
use yii\db\ActiveQuery;
use yii\db\Expression;
use DateTime;

/**
 * 角色
 */
class RoleRepository extends BaseRepository
{
    /**
     * 构建查询条件
     * @param array $params 查询参数
     * @return ActiveQuery
     */
    public function buildCondition(array $params = []): ActiveQuery
    {
        $query = Role::find();
        //角色名称
        if (isset($params['name'])) {
            $query->andFilterWhere(['LIKE', 'name', $params['name']]);
        }
        //角色描述
        if (isset($params['description'])) {
            $query->andFilterWhere(['LIKE', 'description', $params['description']]);
        }
        return $query;
    }
}
