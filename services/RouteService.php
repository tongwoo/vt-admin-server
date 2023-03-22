<?php

namespace app\services;

use app\common\constants\Confirm;
use app\models\business\Route;
use app\common\utils\Result;
use app\common\utils\Pagination;
use app\repositories\RouteRepository;
use Directory;
use Exception;
use Throwable;
use Yii;
use yii\helpers\Inflector;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\UnprocessableEntityHttpException;

/**
 * 路由服务
 */
class RouteService
{
    /**
     * 路由所有记录
     * @param array $params 查询参数
     * @return Result
     */
    public function items(array $params = []): Result
    {
        $repo = Yii::$container->get(RouteRepository::class);
        /** @var Route[] $records */
        $records = $repo->buildCondition($params)->orderBy(['path' => \SORT_DESC])->all();
        $items = [];
        foreach ($records as $record) {
            $items[] = $record->toArray();
        }
        return Result::success()->setData([
            'items' => $items
        ]);
    }

    /**
     * 路由分页记录
     * @param array $params 查询参数
     * @return Result
     */
    public function pageItems(array $params = []): Result
    {
        $pagination = new Pagination();
        $repo = Yii::$container->get(RouteRepository::class);
        $query = $repo->buildCondition($params)->orderBy(['path' => \SORT_ASC]);
        /** @var Route[] $records */
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
     * 路由详情
     * @param int $id 主键ID
     * @return Result
     */
    public function detail(int $id): Result
    {
        /** @var Route $item */
        $route = Route::find()->where(['id' => $id])->limit(1)->one();
        if (!$route) {
            throw new NotFoundHttpException('不存在此记录');
        }
        return Result::success()->setData([
            'item' => $route->toArray()
        ]);
    }

    /**
     * 创建路由
     * @param array $data
     * @return Result
     */
    public function create(array $data): Result
    {
        //新增路由
        $route = new Route();
        $route->setCamelAttributes($data);
        if (!$route->validate()) {
            throw new UnprocessableEntityHttpException($route->validatorError);
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$route->save(false)) {
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
     * 更新路由
     * @param array $data 新的数据
     * @return Result
     */
    public function update(array $data): Result
    {
        if (!isset($data['id']) || !is_numeric($data['id'])) {
            throw new UnprocessableEntityHttpException('数据缺失');
        }
        $id = (int)$data['id'];
        /** @var Route $route */
        $route = Route::find()->where(['id' => $id])->limit(1)->one();
        if (!$route) {
            throw new NotFoundHttpException('没有找到记录');
        }
        $route->setCamelAttributes($data);
        if (!$route->validate()) {
            throw new UnprocessableEntityHttpException($route->validatorError);
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$route->save(false)) {
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
     * 删除路由
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
            /** @var Route $route */
            $route = Route::findOne($id);
            if (!$route) {
                throw new NotFoundHttpException('没有找到记录');
            }
            $models[] = $route;
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

    /**
     * 清空所有路由
     * @return void
     */
    public function truncate()
    {
        $sql = "TRUNCATE TABLE " . Route::tableName();
        Yii::$app->db->createCommand($sql)->execute();
    }

    /**
     * 从指定目录创建路由
     * @param string $dir 目录全路径
     * @return void
     */
    public function createFromDir(string $dir)
    {
        if (!file_exists($dir)) {
            throw new Exception('目录不存在');
        }
        $directory = dir($dir);
        if ($directory === false) {
            throw new Exception('无法打开目录');
        }
        $routes = [];
        while (($fileName = $directory->read()) !== false) {
            if ($fileName === '.' || $fileName === '..') {
                continue;
            }
            if (($offset = strpos($fileName, 'Controller')) === false) {
                continue;
            }
            $className = substr($fileName, 0, $offset);
            $ref = new \ReflectionClass('app\\controllers\\' . $className . 'Controller');
            $methods = $ref->getMethods(\ReflectionMethod::IS_PUBLIC);
            foreach ($methods as $method) {
                $methodName = $method->getName();
                if (strpos($methodName, 'action') !== 0 || $methodName === 'actions') {
                    continue;
                }
                $actionName = substr($methodName, 6);
                $routePath = '/' . Inflector::camel2id($className) . '/' . Inflector::camel2id($actionName);
                $methodComments = explode("\n", $method->getDocComment());
                $comment = '未知';
                if (count($methodComments) > 1) {
                    $comment = trim(str_replace('*', '', $methodComments[1]));
                }
                $route = new Route();
                $route->path = $routePath;
                $route->name = $comment;
                $routes[] = $route;
            }
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            /** @var Route $route */
            foreach ($routes as $route) {
                $exists = Route::find()->where(['path' => $route->path])->exists();
                if ($exists) {
                    continue;
                }
                $route->save();
            }
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
}
