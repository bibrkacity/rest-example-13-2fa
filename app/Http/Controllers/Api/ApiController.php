<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use OpenApi\Attributes as OA;

#[
    OA\Server(
        url: '/api/v1',
        description: 'REST API'
    ),
    OA\Info(
        version: '1.0.0',
        description: 'REST API',
        title: 'REST API'
    ),
    OA\SecurityScheme(
        securityScheme: 'bearerAuth',
        type: 'http',
        name: 'Bearer authorization',
        in: 'header',
        scheme: 'bearer'
    ),
]
abstract class ApiController extends Controller
{
}
