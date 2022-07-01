<?php

namespace app\models\base;

use app\models\BaseModel;

/**
 * 附件
 * @property int $id
 * @property string $name 名称
 * @property string $filename 文件名
 * @property string $extension 扩展名
 * @property string $path 路径
 * @property int $size 大小
 * @property string $time 时间
 * @property string $hash 哈希
 * @property int $isValid 是否有效
 */
class BaseAttachment extends BaseModel
{
    /**
     * 表名
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%attachment}}';
    }

    /**
     * 设置名称
     * @param string|null $value 参数值
     */
    public function setName(?string $value)
    {
        $this->name = $value;
    }

    /**
     * 获取名称
     * @return string|null
     */
    public function getName(): ?string    {
        return $this->name;
    }

    /**
     * 设置文件名
     * @param string|null $value 参数值
     */
    public function setFilename(?string $value)
    {
        $this->filename = $value;
    }

    /**
     * 获取文件名
     * @return string|null
     */
    public function getFilename(): ?string    {
        return $this->filename;
    }

    /**
     * 设置扩展名
     * @param string|null $value 参数值
     */
    public function setExtension(?string $value)
    {
        $this->extension = $value;
    }

    /**
     * 获取扩展名
     * @return string|null
     */
    public function getExtension(): ?string    {
        return $this->extension;
    }

    /**
     * 设置路径
     * @param string|null $value 参数值
     */
    public function setPath(?string $value)
    {
        $this->path = $value;
    }

    /**
     * 获取路径
     * @return string|null
     */
    public function getPath(): ?string    {
        return $this->path;
    }

    /**
     * 设置大小
     * @param int|null $value 参数值
     */
    public function setSize(?int $value)
    {
        $this->size = $value;
    }

    /**
     * 获取大小
     * @return int|null
     */
    public function getSize(): ?int    {
        return $this->size;
    }

    /**
     * 设置时间
     * @param string|null $value 参数值
     */
    public function setTime(?string $value)
    {
        $this->time = $value;
    }

    /**
     * 获取时间
     * @return string|null
     */
    public function getTime(): ?string    {
        return $this->time;
    }

    /**
     * 设置哈希
     * @param string|null $value 参数值
     */
    public function setHash(?string $value)
    {
        $this->hash = $value;
    }

    /**
     * 获取哈希
     * @return string|null
     */
    public function getHash(): ?string    {
        return $this->hash;
    }

    /**
     * 设置是否有效
     * @param int|null $value 参数值
     */
    public function setIsValid(?int $value)
    {
        $this->is_valid = $value;
    }

    /**
     * 获取是否有效
     * @return int|null
     */
    public function getIsValid(): ?int    {
        return $this->is_valid;
    }
}
