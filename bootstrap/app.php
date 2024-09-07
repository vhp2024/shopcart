<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

// ob_start(); var_dump(request()->segments()); $dump = ob_get_clean();
// echo "<pre>" . preg_replace("/\]\=\>\n(\s+)/m", "] => ", $dump) . "</pre>";
// die("2024-09-03 15:27:18");


// die("------2024-09-03 15:26:47------");
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectGuestsTo(fn (Request $request) => route('Auth::login') . '?last-url=' . base64_encode($request->url()));
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
