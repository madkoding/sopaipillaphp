<?php

declare(strict_types=1);

namespace App;

use Sopaipilla\Routing\Attributes\Get;
use Sopaipilla\Http\ApiController;

/**
 * Core application controller.
 * Handles the HTML home page and the /api/health endpoint.
 */
class AppController extends ApiController
{
    /** Render a simple HTML index page with links to available API endpoints. */
    #[Get('/')]
    public function index()
    {
        header('Content-Type: text/html; charset=utf-8');
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <title>SopaipillaPHP App</title>
            <meta charset="utf-8">
        </head>
        <body>
            <h1>SopaipillaPHP Application</h1>
            <ul>
                <li><a href="/api/health">API Health</a></li>
                <li><a href="/api/users">Users API</a></li>
            </ul>
        </body>
        </html>';
    }
    
    /** Return application status, name and current timestamp. */
    #[Get('/api/health')]
    public function health()
    {
        return $this->json([
            'data' => [
                'status'    => 'ok',
                'app'       => 'SopaipillaPHP App',
                'timestamp' => date('c'),
            ],
        ]);
    }
}