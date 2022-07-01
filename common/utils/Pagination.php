<?php

namespace app\common\utils;


class Pagination extends \yii\data\Pagination
{
    const LINK_NEXT = '下一页';

    const LINK_PREV = '上一页';

    const LINK_FIRST = '首页';

    const LINK_LAST = '尾页';

    /**
     * @var string name of the parameter storing the current page index.
     * @see params
     */
    public $pageParam = 'page';

    /**
     * @var string name of the parameter storing the page size.
     * @see params
     */
    public $pageSizeParam = 'pageSize';

    public $validatePage = false;

}