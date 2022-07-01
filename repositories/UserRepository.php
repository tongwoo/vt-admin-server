<?php

namespace app\repositories;

use app\common\utils\Pagination;
use app\models\business\User;
use Yii;
use yii\db\ActiveQuery;
use yii\db\Expression;
use DateTime;

/**
 * 用户
 */
class UserRepository extends BaseRepository
{
    /**
     * 构建查询条件
     * @param array $params 查询参数
     * @return ActiveQuery
     */
    public function buildCondition(array $params = []): ActiveQuery
    {
        $query = User::find();
        //用户名
        if (isset($params['username'])) {
            $query->andFilterWhere(['LIKE', 'username', $params['username']]);
        }
        //登录密码
        if (isset($params['password'])) {
            $query->andFilterWhere(['LIKE', 'password', $params['password']]);
        }
        //姓名
        if (isset($params['name'])) {
            $query->andFilterWhere(['LIKE', 'name', $params['name']]);
        }
        //头像
        if (isset($params['avatar'])) {
            $query->andFilterWhere(['LIKE', 'avatar', $params['avatar']]);
        }
        //状态
        if (isset($params['state'])) {
            $query->andFilterWhere(['state' => $params['state']]);
        }
        //日期查询
        if (isset($params['beginDate']) && isset($params['endDate'])) {
            $beginDate = DateTime::createFromFormat('Y-m-d',$params['beginDate']);
            $endDate = DateTime::createFromFormat('Y-m-d',$params['endDate']);
            if($beginDate && $endDate){
                $query->andFilterWhere(['BETWEEN', 'login_time', $params['beginDate'], $params['endDate']]);
            }
        }
        return $query;
    }
}
