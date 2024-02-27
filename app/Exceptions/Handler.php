<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        //csrf expired handler
        $this->renderable(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, $request) {
            if ($e->getStatusCode() == 419) {
                if ($request->ajax()) {
                    return response()->json([
                        'error'     => 1,
                        'message'   => 'Session halaman telah habis. Mohon refresh halaman kembali',
                        'code'      => 'csrf'
                    ]);
                }

                return redirect()
                    ->back()
                    ->withInput($request->except("password"))
                    ->withMessage('Session halaman telah habis. Mohon coba kembali');
            }
        });

        //session expired handler
        $this->renderable(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->ajax()) {
                return response()->json([
                    'error'     => 1,
                    'message'   => 'Session telah habis. Mohon login kembali',
                    'code'      => 'expired'
                ]);
            }

            return redirect('/login');
            //->withMessage('Session telah habis. Mohon login kembali');
        });
    }
}
