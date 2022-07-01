<?php

namespace app\services;

use app\models\ar\business\Product;
use app\models\ar\business\Category;
use app\common\constants\Gender;
use app\common\utils\Code;
use app\common\utils\CoordinateTransform;
use app\common\utils\Result;
use app\common\utils\Excel;
use app\common\utils\Pagination;
use app\repositories\ProductRepository;
use Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Throwable;
use Yii;

/**
 * 产品服务
 */
class ProductService
{
    /**
     * 产品所有记录
     * @param array $params 查询参数
     * @return Result
     */
    public function items(array $params = []): Result
    {
        /** @var ProductRepository $repo */
        $repo = Product::repository();
        /** @var Product[] $results */
        $results = $repo->findByParams($params);
        $items = [];
        foreach ($results as $result) {
            $items[] = $result->toArray();
        }
        return Result::success()->setData([
            'items' => $items
        ]);
    }

    /**
     * 产品分页记录
     * @param array $params 查询参数
     * @return Result
     */
    public function pageItems(array $params = []): Result
    {
        $pagination = new Pagination();
        /** @var ProductRepository $repo */
        $repo = Product::repository();
        /** @var Product[] $results */
        $results = $repo->findByPagination($pagination, $params);
        $items = [];
        foreach ($results as $result) {
            $items[] = $result->toArray();
        }
        $data = [
            'total' => (int)$pagination->totalCount,
            'items' => $items
        ];
        return Result::success()->setData($data);
    }

    /**
     * 产品详情
     * @param int $id 主键ID
     * @return Result
     */
    public function detail(int $id): Result
    {
        /** @var Product $item */
        $product = Product::findOne($id);
        if (!$product) {
            return Result::failure('没有记录', Code::NOT_FOUND);
        }
        return Result::success()->setData([
            'item' => $product->toArray()
        ]);
    }

    /**
     * 创建产品
     * @param array $data
     * @return Result
     */
    public function create(array $data): Result
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            //新增产品
            $product = new Product();
            $product->setCamelAttributes($data);
            if (!$product->validate()) {
                return Result::failure($product->getValidatorError(), Code::VALIDATE_FAILURE);
            }
            if (!$product->save(false)) {
                return Result::failure('创建失败, ' . $product->getValidatorError());
            }
            $transaction->commit();
        } catch (Throwable $e) {
            $transaction->rollBack();
            return Result::failure($e->getMessage());
        }
        return Result::success();
    }

    /**
     * 更新产品
     * @param array $data 新的数据
     * @return Result
     */
    public function update(array $data): Result
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!isset($data['id']) || !is_numeric($data['id'])) {
                throw new Exception('缺少ID');
            }
            $id = (int)$data['id'];
            /** @var Product $product */
            $product = Product::findOne($id);
            if (!$product) {
                return Result::failure('没有找到记录', Code::NOT_FOUND);
            }
            $product->setCamelAttributes($data);
            if (!$product->validate()) {
                return Result::failure($product->getValidatorError(), Code::VALIDATE_FAILURE);
            }
            if (!$product->save(false)) {
                return Result::failure('更新失败, ' . $product->getValidatorError());
            }
            $transaction->commit();
        } catch (Throwable $e) {
            $transaction->rollBack();
            return Result::failure($e->getMessage());
        }
        return Result::success();
    }

    /**
     * 删除产品
     * @param string|int $id 主键ID
     * @return Result
     */
    public function delete($id): Result
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $ids = is_numeric($id) ? [$id] : explode(',', $id);
            if (count($ids) === 0 || $ids[0] === '') {
                return Result::failure('非法参数', Code::INVALID_PARAM);
            }
            foreach ($ids as $id) {
                $id = (int)$id;
                if (!$id) {
                    throw new Exception('非法参数 ' . $id);
                }
                /** @var Product $product */
                $product = Product::findOne($id);
                if (!$product) {
                    throw new Exception('记录不存在 ' . $id);
                }
                $product->delete();
            }
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            return Result::failure($e->getMessage());
        }
        return Result::success();
    }

    /**
     * 导入产品
     * @param string $path 数据文件路径
     * @return Result
     */
    public function import(string $path): Result
    {
        if (!file_exists($path)) {
            return Result::failure(sprintf('导入的文件「%s」不存在', $path));
        }
        $i = null;
        $products = [];
        try {
            $sheetName = '产品';
            $reader = IOFactory::createReaderForFile($path);
            $reader->setReadDataOnly(true);
            $reader->setLoadSheetsOnly($sheetName);
            $spreadsheet = $reader->load($path);
            $sheet = $spreadsheet->getSheetByName($sheetName);
            if ($sheet === null) {
                throw new Exception('缺少「' . $sheetName . '」sheet');
            }
            $ct = new CoordinateTransform();
            $records = $sheet->toArray(null, true, true, true);
            if (count($records) <= 1) {
                throw new Exception('没有发现有效数据');
            }
            /** @var Product[] $products */
            $products = [];
            //映射字段的位置，键为中文名称，值为中文名称所在的列位置
            $mapFields = [];
            //读取第一行并将每列字段位置保存
            foreach ($records[1] as $column => $name) {
                $mapFields[$name] = $column;
            }
            /**
             * 外键缓存 - 种类
             * @var Category[] $categories
             */
            $categories = [];
            //循环所有记录行进行验证处理
            foreach ($records as $i => $record) {
                if ($i <= 1) {
                    continue;
                }
                //种类
                if (isset($mapFields['种类'])) {
                    $categoryValue = $record[$mapFields['种类']];
                    if (!key_exists($categoryValue, $categories)) {
                        $category = Category::find()->where(['name' => $categoryValue])->one();
                        if (!$category) {
                            throw new Exception('「种类」中数据有误，系统中没有找到「' . $categoryValue . '」');
                        }
                        $categories[$categoryValue] = $category;
                    }
                    $data['categoryId'] = $categories[$categoryValue]->id;
                }
                if (isset($mapFields['名称'])) {
                    $data['name'] = $record[$mapFields['名称']];
                }
                if (isset($mapFields['日期'])) {
                    $data['date'] = $record[$mapFields['日期']];
                }
                if (isset($mapFields['时间'])) {
                    $data['time'] = $record[$mapFields['时间']];
                }
                if (isset($mapFields['性别'])) {
                    $data['gender'] = Gender::value($record[$mapFields['性别']]);
                }
                //位置
                if (isset($mapFields['位置经度']) && isset($mapFields['位置纬度'])) {
                    if (is_numeric($record[$mapFields['位置经度']]) && is_numeric($record[$mapFields['位置纬度']])) {
                        $data['location'] = [
                            $record[$mapFields['位置经度']],
                            $record[$mapFields['位置纬度']]
                        ];
                    } else {
                        $data['location'] = null;
                    }
                }
                //实例化
                $product = new Product();
                $product->setCamelAttributes($data);
                if (!$product->validate()) {
                    throw new Exception($product->getValidatorError());
                }
                $products[] = $product;
            }
        } catch (Exception $e) {
            $message = sprintf('第 %d 行，%s', $i, $e->getMessage());
            return Result::failure($message);
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            foreach ($products as $i => $product) {
                $product->save(false);
            }
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            $message = sprintf('第 %d 行，%s', $i + 1, $e->getMessage());
            return Result::failure($message)->setData([
                'traces' => $e->getTrace()
            ]);
        }
        return Result::success();
    }

    /**
     * 导出产品
     * @param array $params 查询参数
     * @return string|null
     */
    public function export(array $params): ?string
    {
        //保存到的目录
        $base = Yii::$app->basePath . '/web/exports/' . date('Y') . '/' . date('m');
        if (!file_exists($base)) {
            mkdir($base, 0777, true);
        }
        $file = $base . '/' . date('d-His') . '.xlsx';
        $results = Product::repository()->findByParams($params);
        //导出的数据
        $items[] = [
            '种类',
            '名称',
            '日期',
            '时间',
            '性别',
            '位置经度',
            '位置纬度',
        ];
        foreach ($results as $result) {
            $items[] = [
                $result->category ? $result->category->name : null, //种类
                $result->name, //名称
                $result->date, //日期
                $result->time, //时间
                $result->genderName, //性别
                $result->getLocation()->getLongitude(), //位置经度
                $result->getLocation()->getLatitude(), //位置纬度
            ];
        }
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('产品');
            $sheet->fromArray($items);
            $excel = new Excel($spreadsheet);
            $excel->formatExport();
            $excel->setWidths(Excel::calculateColumnWidths($items));
            $writer = new Xlsx($spreadsheet);
            $writer->save($file);
        } catch (Exception $e) {
            return null;
        }
        return $file;
    }
}
