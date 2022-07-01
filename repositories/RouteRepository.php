<?php

namespace app\repositories;

use app\common\utils\Pagination;
use app\models\business\Route;
use Yii;
use yii\db\ActiveQuery;
use yii\db\Expression;
use DateTime;

/**
 * 路由
 */
class RouteRepository extends BaseRepository
{
    /**
     * 构建查询条件
     * @param array $params 查询参数
     * @return ActiveQuery
     */
    public function buildCondition(array $params = []): ActiveQuery
    {
        $query = Route::find();
        //权限
        if (isset($params['permissionId'])) {
            $query->andFilterWhere(['permission_id' => $params['permissionId']]);
        }
        //名称
        if (isset($params['name'])) {
            $query->andFilterWhere(['LIKE', 'name', $params['name']]);
        }
        //路径
        if (isset($params['path'])) {
            $query->andFilterWhere(['LIKE', 'path', $params['path']]);
        }
        //查询关联 - 权限
        $query->with('permission');
        return $query;
    }
}
