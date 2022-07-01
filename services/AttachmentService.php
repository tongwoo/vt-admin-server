<?php

namespace app\services;

use app\common\constants\Confirm;
use app\models\business\Attachment;
use app\common\utils\Code;
use app\common\utils\CoordinateTransform;
use app\common\utils\Result;
use app\common\utils\Excel;
use app\common\utils\Pagination;
use app\repositories\AttachmentRepository;
use Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Throwable;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\UnprocessableEntityHttpException;

/**
 * 附件服务
 */
class AttachmentService
{
    /**
     * 附件所有记录
     * @param array $params 查询参数
     * @return Result
     */
    public function items(array $params = []): Result
    {
        $repo = Yii::$container->get(AttachmentRepository::class);
        /** @var Attachment[] $records */
        $records = $repo->buildCondition($params)->orderBy(['id' => \SORT_DESC])->all();
        $items = [];
        foreach ($records as $record) {
            $items[] = $record->toArray();
        }
        return Result::success()->setData([
            'items' => $items
        ]);
    }

    /**
     * 附件分页记录
     * @param array $params 查询参数
     * @return Result
     */
    public function pageItems(array $params = []): Result
    {
        $pagination = new Pagination();
        $repo = Yii::$container->get(AttachmentRepository::class);
        $query = $repo->buildCondition($params)->orderBy(['id' => \SORT_DESC]);
        /** @var Attachment[] $records */
        $records = $repo->pagination($pagination, $query)->all();
        $items = [];
        foreach ($records as $record) {
            $items[] = $record->toArray();
        }
        $data = [
            'total' => (int)$pagination->totalCount,
            'items' => $items
        ];
        return Result::success()->setData($data);
    }

    /**
     * 附件详情
     * @param int $id 主键ID
     * @return Result
     */
    public function detail(int $id): Result
    {
        /** @var Attachment $item */
        $attachment = Attachment::find()->where(['id' => $id])->limit(1)->one();
        if (!$attachment) {
            throw new NotFoundHttpException('不存在此记录');
        }
        return Result::success()->setData([
            'item' => $attachment->toArray()
        ]);
    }

    /**
     * 创建附件
     * @param array $data
     * @return Result
     */
    public function create(array $data): Result
    {
        //新增附件
        $attachment = new Attachment();
        $attachment->setCamelAttributes($data);
        if (!$attachment->validate()) {
            throw new UnprocessableEntityHttpException($attachment->validatorError);
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$attachment->save(false)) {
                throw new ServerErrorHttpException('创建失败');
            }
            $transaction->commit();
        } catch (Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
        return Result::success();
    }

    /**
     * 更新附件
     * @param array $data 新的数据
     * @return Result
     */
    public function update(array $data): Result
    {
        if (!isset($data['id']) || !is_numeric($data['id'])) {
            throw new UnprocessableEntityHttpException('数据缺失');
        }
        $id = (int)$data['id'];
        /** @var Attachment $attachment */
        $attachment = Attachment::find()->where(['id' => $id])->limit(1)->one();
        if (!$attachment) {
            throw new NotFoundHttpException('没有找到记录');
        }
        $attachment->setCamelAttributes($data);
        if (!$attachment->validate()) {
            throw new UnprocessableEntityHttpException($attachment->validatorError);
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$attachment->save(false)) {
                throw new ServerErrorHttpException('更新失败');
            }
            $transaction->commit();
        } catch (Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
        return Result::success();
    }

    /**
     * 删除附件
     * @param string|int $id 主键ID
     * @return Result
     */
    public function delete($id): Result
    {
        $ids = is_numeric($id) ? [$id] : explode(',', $id);
        if (count($ids) === 0 || $ids[0] === '') {
            throw new UnprocessableEntityHttpException('参数无效');
        }
        $models = [];
        foreach ($ids as $id) {
            $id = (int)$id;
            if (!$id) {
                throw new UnprocessableEntityHttpException('参数ID无效');
            }
            /** @var Attachment $attachment */
            $attachment = Attachment::findOne($id);
            if (!$attachment) {
                throw new NotFoundHttpException('没有找到记录');
            }
            $models[] = $attachment;
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            foreach ($models as $model) {
                $model->delete();
            }
            $transaction->commit();
        } catch (Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
        return Result::success();
    }

}
