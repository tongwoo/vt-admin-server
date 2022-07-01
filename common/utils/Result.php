<?php

namespace app\common\utils;

use Yii;
use yii\web\Response;

class Result
{
    /**
     * 结果是否成功 成功=true 失败=false
     * @var bool
     */
    private bool $success;

    /**
     * 状态码
     * @var int
     */
    private int $code;

    /**
     * 消息
     * @var string
     */
    private string $message;

    /**
     * 存储的数据
     * @var mixed
     */
    private $data;

    /**
     * 创建一个操作成功的Result
     * @param string $message 消息
     * @param int    $code    状态码
     * @return Result
     */
    public static function success(string $message = '操作成功', int $code = Code::OK): Result
    {
        $result = new self();
        $result->setSuccess(true);
        $result->setMessage($message);
        $result->setCode($code);
        return $result;
    }

    /**
     * 创建一个操作失败的Result
     * @param string $message 消息
     * @param int    $code    状态码
     * @return Result
     */
    public static function failure(string $message = '操作失败', int $code = Code::INTERNAL_SERVER_ERROR): Result
    {
        $result = new self();
        $result->setSuccess(false);
        $result->setMessage($message);
        $result->setCode($code);
        return $result;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * @param bool $success
     * @return Result
     */
    public function setSuccess(bool $success): Result
    {
        $this->success = $success;
        return $this;
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @param int $code
     * @return Result
     */
    public function setCode(int $code): Result
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return Result
     */
    public function setMessage(string $message): Result
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     * @return Result
     */
    public function setData($data): Result
    {
        $this->data = $data;
        return $this;
    }

    /**
     * 获取HTTP状态码
     * @return int
     */
    public function getHttpCode(): int
    {
        return $this->isSuccess() ? Response::HTTP_OK : Response::HTTP_INTERNAL_SERVER_ERROR;
    }

    /**
     * 转换成数组结构的code格式
     * @return array
     */
    public function asCode(): array
    {
        return [
            'code' => $this->getCode(),
            'message' => $this->getMessage(),
            'data' => $this->getData()
        ];
    }

    /**
     * 转换成JsonResponse响应
     * @return Response
     */
    public function asJsonResponse(): Response
    {
        $response = Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        $response->data = $this->asCode();
        return $response;
    }
}