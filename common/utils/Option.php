<?php


namespace app\common\utils;

use Reflection;
use ReflectionClass;
use ReflectionClassConstant;

/**
 * 参数选项
 */
class Option
{
    /**
     * 启用映射器
     */
    const ENABLE_MAPPER = 'enable_mapper';

    /**
     * 启用接口路径前缀
     */
    const ENABLE_API_PREFIX = 'enable_api_prefix';

    /**
     * 表单生成Row分列
     */
    const ENABLE_ROW_COLUMN = 'enable_row_column';

    /**
     * 列表页生成搜索栏
     */
    const ENABLE_LIST_SEARCH_BAR = 'enable_list_search_bar';

    /**
     * 表单生成验证规则
     */
    const ENABLE_FORM_RULE = 'enable_form_rule';

    /**
     * 保存调用save方法
     */
    const SAVE_METHOD = 'save_method';

    /**
     * 枚举常量引用单独的文件
     */
    const CONSTANT_REFERENCE_SINGLE_FILE = 'constant_reference_single_file';

    /**
     * 枚举常量样式使用背景色
     */
    const CONSTANT_STYLE_BACKGROUND = 'constant_style_background';

    /**
     * 枚举常量样式使用前景色
     */
    const CONSTANT_STYLE_FOREGROUND = 'constant_style_foreground';

    /**
     * 导出EXCEL
     */
    const ENABLE_EXPORT = 'enable_export';

    /**
     * 导入EXCEL
     */
    const ENABLE_IMPORT = 'enable_import';

    /**
     * 文字生成下拉框
     */
    const TEXT_CREATE_SELECT = 'text_create_select';

    /**
     * 文字下拉框远程搜索
     */
    const TEXT_SELECT_REMOTE = 'text_select_remote';

    /**
     * 启用列表复选项
     */
    const ENABLE_LIST_CHECKBOX = 'enable_list_checkbox';

    /**
     * POSTMAN请求内容类型 - URLEncode
     */
    const POSTMAN_REQUEST_CONTENT_TYPE_URLENCODE = 'postman_request_content_type_urlencode';

    /**
     * POSTMAN请求内容类型 - RAW
     */
    const POSTMAN_REQUEST_CONTENT_TYPE_RAW = 'postman_request_content_type_raw';

    /**
     * 增删改查 - 平湖
     */
    const MODULE_JS_CRUD_PINGHU = 'MODULE_JS_PINGHU';

    protected $options = [];

    /**
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    /**
     * 是否存在指定的配置选项
     * @param string $optionName 配置名称
     * @return bool
     */
    public function has(string $optionName): bool
    {
        foreach ($this->options as $option) {
            if ($option == $optionName) {
                return true;
            }
        }
        return false;
    }

    /**
     * 获得所有常量组成一个数组
     */
    public static function items(): array
    {
        $items = [];
        $reflection = new ReflectionClass(self::class);
        $constants = $reflection->getReflectionConstants();
        foreach ($constants as $constant) {
            $comment = $constant->getDocComment();
            $comment = str_replace('*', '', $comment);
            $comment = str_replace('/', '', $comment);
            $comment = str_replace("\n", '', $comment);
            $comment = str_replace(" ", '', $comment);
            $items[] = [
                'label' => $comment,
                'value' => $constant->getValue(),
            ];
        }
        return $items;
    }
}
