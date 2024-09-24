<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;


use Meta;
use Flash;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        // if($e instanceof \PDOException)
        // {
        //  return response()->view('errors.sql');
        // }

        // if($e instanceof \MethodNotAllowedHttpException)
        // {
        //  return response()->view('errors.sql');
        // }


        // if($e instanceof \Cartalyst\Sentinel\Checkpoints\ThrottlingException)
        // {  
        //     Flash::error('Suspicious activity has occured on your IP,try after some time.');
        //     if(\Request::segment(1)=='student'){
        //         return response()->view('student/auth/login');    
        //     }
        //     if(\Request::segment(1)=='school_admin'){
        //         return response()->view('schooladmin/auth/login');    
        //     }
        //     if(\Request::segment(1)=='parent'){
        //         return response()->view('parent/auth/login');    
        //     }
        //     if(\Request::segment(1)=='professor'){
        //         return response()->view('professor/auth/login');    
        //     }
        //     if(\Request::segment(1)=='admin'){
        //         return response()->view('admin/auth/login');    
        //     }
            
        // }

       /* if ($e instanceof \Illuminate\Session\TokenMismatchException){

            \Flash::error("Token missmatch error.");
            return redirect(\Request::segment(1));
        } */
        
        if($this->isHttpException($e))
        {
          switch ($e->getStatusCode()) {
              
              case '404':
                  return response()->view('errors.404',[],404);
              break;
              case '500':
                  return response()->view('errors.500',[],500);    
              break;
              default:
                 // return $this->renderHttpException($e);
                   return response()->view('errors.sq');
              break;
              }
          }

        return parent::render($request, $e);
    }
}