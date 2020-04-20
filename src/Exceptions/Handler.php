<?php

namespace Krasnikov\EloquentJSON\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Exceptions\Handler as AbstractExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

/**
 * Class Handler
 * @package Krasnikov\EloquentJSON\Exceptions
 */
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
     * @param Throwable $exception
     * @return void
     * @throws Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Throwable $exception
     * @return Response|JsonResponse|SymfonyResponse
     * @throws Throwable
     */
    public function render($request, Throwable $exception)
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
