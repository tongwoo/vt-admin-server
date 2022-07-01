<?php


namespace app\common\utils;


class Code
{
    public const OK = 200;
    public const BAD_REQUEST = 400;
    public const UNAUTHORIZED = 401;
    public const PAYMENT_REQUIRED = 402;
    public const FORBIDDEN = 403;
    public const NOT_FOUND = 404;
    public const INTERNAL_SERVER_ERROR = 500;

    public const VALIDATE_FAILURE = 10000; //数据验证失败
    public const INVALID_PARAM = 10001; //参数无效
}