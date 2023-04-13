<?php
namespace app;

use app\common\exception\Base as BaseException;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\Handle;
use think\exception\HttpException;
use think\exception\HttpResponseException;
use think\exception\ValidateException;
use think\facade\Env;
use think\facade\Log;
use think\Response;
use Throwable;

/**
 * 应用异常处理类
 */
class ExceptionHandle extends Handle
{
    private $httpCode;
    private $errorMessage;
    private $errorCode;

    /**
     * 不需要记录信息（日志）的异常类列表
     * @var array
     */
    protected $ignoreReport = [
        HttpException::class,
        HttpResponseException::class,
        ModelNotFoundException::class,
        DataNotFoundException::class,
        ValidateException::class,
    ];

    /**
     * 记录异常信息（包括日志或者其它方式记录）
     *
     * @access public
     * @param  Throwable $exception
     * @return void
     */
    public function report(Throwable $exception): void
    {
        // 使用内置的方式记录异常日志
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @access public
     * @param \think\Request   $request
     * @param Throwable $e
     * @return Response
     */
    public function render($request, Throwable $e): Response
    {
        // 添加自定义异常处理机制
        if ($e instanceof BaseException) {
            $this->httpCode = $e->httpCode;
            $this->errorMessage = $e->errorMessage;
            $this->errorCode = $e->errorCode;
        } else {
            if (Env::get('app_debug', false)) {
                // 其他错误交给系统处理
                return parent::render($request, $e);
            } else {
                $this->httpCode = 500;
                $this->errorMessage = '服务器内部错误，哈哈哈';
                $this->errorCode = 999;
                $this->recordErrorLog($e);
            }
        }
        $result = [
            'error_message' => $this->errorMessage,
            'error_code' => $this->errorCode,
            'request_url' => $request->url(),
        ];
        //return json($result, $this->httpCode);
        //根据前端要求修改返回的数据格式
        return returnResponse($this->httpCode, $this->errorMessage, ['request_url' => $request->url()]);
    }

    /*
     * 记录错误日志
     */
    private function recordErrorLog(Throwable $e)
    {
        Log::record($e->getMessage(), 'error');
    }
}
