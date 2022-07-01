<?php

namespace app\repositories;

use app\common\utils\Pagination;
use app\models\business\Attachment;
use Yii;
use yii\db\ActiveQuery;
use yii\db\Expression;
use DateTime;

/**
 * 附件
 */
class AttachmentRepository extends BaseRepository
{
    /**
     * 构建查询条件
     * @param array $params 查询参数
     * @return ActiveQuery
     */
    public function buildCondition(array $params = []): ActiveQuery
    {
        $query = Attachment::find();
        //名称
        if (isset($params['name'])) {
            $query->andFilterWhere(['LIKE', 'name', $params['name']]);
        }
        //日期查询
        if (isset($params['beginDate']) && isset($params['endDate'])) {
            $beginDate = DateTime::createFromFormat('Y-m-d',$params['beginDate']);
            $endDate = DateTime::createFromFormat('Y-m-d',$params['endDate']);
            if($beginDate && $endDate){
                $query->andFilterWhere(['BETWEEN', 'time', $params['beginDate'], $params['endDate']]);
            }
        }
        return $query;
    }
}
