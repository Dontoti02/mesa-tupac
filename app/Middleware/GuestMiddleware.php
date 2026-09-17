<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;

class GuestMiddleware
{
    public function handle(Request $request, Response $response): void
    {
        if (Session::has('user')) {
            $response->redirect('/dashboard');
        }
    }
}
