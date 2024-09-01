<?php

namespace ZacoSoft\ZacoBase\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use URL;

class BaseResponse implements Responsable
{
    /**
     * @var bool
     */
    protected $error = false;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var string
     */
    protected $message = '';

    /**
     * @var array
     */
    protected $errorMessages = [];

    /**
     * @var string
     */
    protected $previousUrl = '';

    /**
     * @var string
     */
    protected $nextUrl = '';

    /**
     * @var bool
     */
    protected $withInput = false;

    /**
     * @var array
     */
    protected $additional = [];

    /**
     * @var array
     */
    protected $cookies = [];

    /**
     * @var LogData
     */
    protected $logData = [];

    /**
     * @var array
     */
    protected $errorData = [];

    /**
     * @var array
     */
    protected $headers = [
        'Author' => 'ZacoSoft',
    ];

    /**
     * @var int
     */
    protected $code = 200;

    /**
     * @var boolean
     */
    protected $isReturnResponse = false;

    /**
     * @param $data
     * @return BaseHttpResponse
     */
    public function setData($data): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param $data
     * @return LogData
     */
    public function setLog(array $data = [])
    {
        $this->logData = app('initLogData')->init($data);

        return $this->logData;
    }

    /**
     * @param string $previousUrl
     * @return BaseHttpResponse
     */
    public function setPreviousUrl($previousUrl): self
    {
        $this->previousUrl = $previousUrl;
        return $this;
    }

    /**
     * @param string $nextUrl
     * @return BaseHttpResponse
     */
    public function setNextUrl($nextUrl): self
    {
        $this->nextUrl = $nextUrl;
        return $this;
    }

    /**
     * @param bool $withInput
     * @return BaseHttpResponse
     */
    public function withInput(bool $withInput = true): self
    {
        $this->withInput = $withInput;
        return $this;
    }

    /**
     * @param int $code
     * @return BaseHttpResponse
     */
    public function setCode(int $code): self
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
     * @param $message
     * @return BaseHttpResponse
     */
    public function setMessage($message): self
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @return array
     */
    public function getErrorMessages(): array
    {
        return $this->errorMessages;
    }

    /**
     * @param $errorMessages
     * @return BaseHttpResponse
     */
    public function setErrorMessages($errorMessages): self
    {
        $this->errorMessages = $errorMessages;
        return $this;
    }

    /**
     * @return bool
     */
    public function isError(): bool
    {
        return $this->error;
    }

    /**
     * @param $error
     * @return BaseHttpResponse
     */
    public function setError(bool $error = true): self
    {
        $this->error = $error;
        return $this;
    }

    /**
     * @param $data
     * @return BaseHttpResponse
     */
    public function setErrorData($data): self
    {
        $this->errorData = $data;
        return $this;
    }

    /**
     * @return array
     */
    public function getErrorData()
    {
        return $this->errorData;
    }

    /**
     * @param array $additional
     * @return BaseHttpResponse
     */
    public function setAdditional(array $additional): self
    {
        $this->additional = $additional;

        return $this;
    }

    /**
     * @return BaseHttpResponse
     */
    public function setCookies(): self
    {
        $this->cookies = array_merge($this->cookies, func_get_args());

        return $this;
    }

    /**
     * @return BaseHttpResponse
     */
    public function setHeaders(): self
    {
        $headers = func_get_args();

        if (is_array($headers[0])) {
            $this->headers = array_merge($this->headers, $headers);
        } else {
            $this->headers[$headers[0]] = $headers[1];
        }

        return $this;
    }

    /**
     * @return BaseHttpResponse|RedirectResponse|JsonResource
     */
    public function toApiResponse()
    {
        if ($this->data instanceof JsonResource) {
            return $this->data->additional(array_merge([
                'error' => $this->error,
                'message' => $this->message,
            ], $this->additional));
        }

        return $this->toResponse(request());
    }

    /**
     * @param Request $request
     * @return BaseHttpResponse|JsonResponse|RedirectResponse
     */
    public function toResponse($request)
    {
        if ($request->expectsJson() || $request->get('_json') || preg_match("/api\//", $request->getRequestUri()) || $this->isReturnResponse) {

            // if (is_profiler()) {
            //     $this->setAdditional(['profiler' => \DB::getQueryLog()]);
            // }
            $result = [
                'data' => $this->data ?? [],
                'message' => empty($this->message) ? trans('base/acl::common.alert_success_msg') : $this->message,
                'success' => !$this->error,
            ];

            if (!empty($this->errorMessages)) {
                $result['data']['errorMessages'] = $this->errorMessages;
            }

            $response = response()
                ->json(array_merge($this->additional, $result), $this->code);

            if ($this->cookies) {
                foreach ($this->cookies as $cookie) {
                    $response->cookie(
                        $cookie[0],
                        $cookie[1],
                        $cookie[2] ?? (10 * 24 * 60),
                        '/',
                        null,
                        null,
                        false
                    );
                }
            }

            if (!empty($this->logData)) {
                request_log($this->logData, env('LOG_CATEGORY', 'soft'));
            }

            if ($this->headers) {
                foreach ($this->headers as $name => $header) {
                    $response->header($name, $header);
                }
            }

            return $response;
        }

        if ($request->input('submit') === 'save' && !empty($this->previousUrl)) {
            return $this->responseRedirect($this->previousUrl);
        } elseif (!empty($this->nextUrl)) {
            return $this->responseRedirect($this->nextUrl);
        }

        return $this->responseRedirect(URL::previous());
    }

    /**
     * @param string $url
     * @return RedirectResponse
     */
    protected function responseRedirect($url)
    {
        if ($this->withInput) {
            if (!isEmpty($this->errorData)) {
                return redirect()
                    ->to($url)
                    ->with($this->error ? 'error_msg' : 'success_msg', $this->message)
                    ->withErrors($this->errorData)
                    ->withInput();
            }

            return redirect()
                ->to($url)
                ->with($this->error ? 'error_msg' : 'success_msg', $this->message)
                ->withInput();
        }

        return redirect()
            ->to($url)
            ->with($this->error ? 'error_msg' : 'success_msg', $this->message);
    }
}
