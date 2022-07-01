<?php

namespace app\common\utils;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

/**
 * Excel相关处理
 */
class Excel
{
    /**
     * @var Spreadsheet
     */
    private $spreadsheet;

    public function __construct(Spreadsheet $spreadsheet)
    {
        $this->spreadsheet = $spreadsheet;
    }

    /**
     * 格式化导出
     * @return Excel
     */
    public function formatExport(): Excel
    {
        $sheet = $this->spreadsheet->getActiveSheet();
        //设置自适应
        //$sheet->getDefaultColumnDimension()->setAutoSize(true);
        //$sheet->calculateColumnWidths();
        $sheet->getDefaultRowDimension()->setRowHeight(24);
        $style = $this->spreadsheet->getDefaultStyle();
        //对齐
        //$style->getAlignment()->setIndent(1);
        $style->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $style->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        //字体
        $style->getFont()->setName('微软雅黑')->setSize(10);
        //行高
        $sheet->getDefaultRowDimension()->setRowHeight(16);
        //美化首行
        $this->formatHeader();
        //设置自适应
        foreach ($sheet->getColumnDimensions() as $column => $dimension) {
            $dimension->setWidth(100);
        }

        return $this;
    }

    /**
     * 美化首行字段
     * @return static
     */
    public function formatHeader()
    {
        $sheet = $this->spreadsheet->getActiveSheet();
        //最后一列
        $endColumn = Coordinate::columnIndexFromString($sheet->getHighestDataColumn());
        //冻结第一行
        $sheet->freezePaneByColumnAndRow(1, 2);
        //设置首行样式
        $style = $sheet->getStyleByColumnAndRow(1, 1, $endColumn, 1);
        $style->getFont()->setBold(true);
        $style->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR);
        $style->getFill()->setRotation(90);
        $style->getFill()->getEndColor()->setRGB('f0f0f0');
        $style->getFill()->getStartColor()->setRGB('fcfcfc');
        $sheet->getRowDimension(1)->setRowHeight(26);

        return $this;
    }

    /**
     * 设置宽度
     * @param array $widths
     * @return $this
     */
    public function setWidths(array $widths): Excel
    {
        $sheet = $this->spreadsheet->getActiveSheet();
        $i = 0;
        foreach ($widths as $width) {
            $i += 1;
            $sheet->getColumnDimensionByColumn($i)->setWidth($width * 1.1);
        }
        return $this;
    }

    /**
     * 计算一个数据集合的尺寸，返回的尺寸键为字段名，值为宽度
     * @param array $records
     * @return array
     */
    public static function calculateColumnWidths(array $records): array
    {
        $widths = [];
        foreach ($records as $record) {
            foreach ($record as $key => $item) {
                $widths[$key][] = strlen($item);
            }
        }
        foreach ($widths as $key => $width) {
            $widths[$key] = max($width);
        }
        return $widths;
    }
}