<?php

namespace Krasnikov\EloquentJSON\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as AbstractExceptionHandler;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends AbstractExceptionHandler implements ExceptionHandler
{

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception $exception
     * @return void
     * @throws Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof ModelNotFoundException && $request->expectsJson()) {
            return response()->json([
                'errors' => [
                    [
                        'detail' => __('common.not_found'),
                        'status' => Response::HTTP_NOT_FOUND
                    ]
                ],
            ], Response::HTTP_NOT_FOUND);
        }

        if ($exception instanceof NotFoundHttpException && $request->expectsJson()) {
            return response()->json([
                'errors' => [
                    [
                        'detail' => __('common.not_found'),
                        'status' => Response::HTTP_NOT_FOUND
                    ]
                ],
            ], Response::HTTP_NOT_FOUND);
        }

        if ($exception instanceof ValidationException && $request->expectsJson()) {
            $errors = [];
            foreach ($exception->errors() as $key => $error) {
                Collection::make($error)->each(function (string $message) use ($key, &$errors) {
                    $errors[] = [
                        'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                        'detail' => $message,
                        'source' => [
                            'parameter' => $key
                        ]
                    ];
                });
            };
            return response()->json(['errors' => $errors], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ($exception instanceof AuthenticationException && $request->expectsJson()) {
            return response()->json([
                'errors' => [
                    [
                        'detail' => __('auth.unauthenticated'),
                        'status' => Response::HTTP_UNAUTHORIZED
                    ]
                ],
            ], Response::HTTP_UNAUTHORIZED);
        }


        if ($exception instanceof Exception && $request->expectsJson()) {
            Log::error($exception->getTraceAsString());
            return response()->json([
                'errors' => [
                    [
                        'detail' => $exception->getMessage(),
                        'status' => Response::HTTP_INTERNAL_SERVER_ERROR
                    ]
                ],
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return parent::render($request, $exception);
    }
}
