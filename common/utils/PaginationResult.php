<?php


namespace app\common\utils;


class PaginationResult
{
    /**
     * 记录总数
     * @var int
     */
    private $total = 0;

    /**
     * 记录集合
     * @var array
     */
    private $items = [];

    /**
     * @return int
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * @param int $total
     * @return PaginationResult
     */
    public function setTotal(int $total): PaginationResult
    {
        $this->total = $total;
        return $this;
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param array $items
     * @return PaginationResult
     */
    public function setItems(array $items): PaginationResult
    {
        $this->items = $items;
        return $this;
    }

    public function __construct(array $items, int $total)
    {
        $this->setItems($items);
        $this->setTotal($total);
    }

    public static function initialize(array $items, int $total): PaginationResult
    {
        return new self($items, $total);
    }

    /**
     * @return array
     */
    public function asArray(): array
    {
        return [
            'total' => $this->getTotal(),
            'items' => $this->getItems()
        ];
    }
}