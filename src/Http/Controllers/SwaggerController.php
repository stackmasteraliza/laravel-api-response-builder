<?php

namespace Stackmasteraliza\ApiResponse\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Stackmasteraliza\ApiResponse\OpenApi\OpenApiGenerator;

class SwaggerController extends Controller
{
    /**
     * Display the Swagger UI.
     */
    public function index(): Response
    {
        $title = config('api-response.openapi.title', config('app.name', 'API') . ' Documentation');
        $specUrl = url('/api-docs/openapi.json');

        $html = $this->getSwaggerUIHtml($title, $specUrl);

        return response($html)->header('Content-Type', 'text/html');
    }

    /**
     * Get the OpenAPI specification.
     */
    public function spec(OpenApiGenerator $generator): JsonResponse
    {
        $spec = $generator->generate();

        return response()->json($spec);
    }

    /**
     * Get the Swagger UI HTML.
     */
    protected function getSwaggerUIHtml(string $title, string $specUrl): string
    {
        $themeColor = config('api-response.openapi.theme_color', '#3b82f6');

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title}</title>
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/swagger-ui-dist@5/swagger-ui.css">
    <style>
        html { box-sizing: border-box; overflow-y: scroll; }
        *, *:before, *:after { box-sizing: inherit; }
        body { margin: 0; background: #fafafa; }
        .swagger-ui .topbar { display: none; }
        .swagger-ui .info .title { color: #1e293b; }
        .swagger-ui .scheme-container { background: #f8fafc; padding: 15px; }
        .swagger-ui .opblock.opblock-get .opblock-summary-method { background: #22c55e; }
        .swagger-ui .opblock.opblock-post .opblock-summary-method { background: {$themeColor}; }
        .swagger-ui .opblock.opblock-put .opblock-summary-method { background: #f59e0b; }
        .swagger-ui .opblock.opblock-delete .opblock-summary-method { background: #ef4444; }
        .swagger-ui .opblock.opblock-patch .opblock-summary-method { background: #8b5cf6; }
        .swagger-ui .btn.execute { background-color: {$themeColor}; border-color: {$themeColor}; }
        .swagger-ui .btn.execute:hover { background-color: #2563eb; border-color: #2563eb; }
        .custom-header {
            background: linear-gradient(135deg, {$themeColor} 0%, #1e40af 100%);
            color: white;
            padding: 20px 40px;
            margin-bottom: 0;
        }
        .custom-header h1 { margin: 0 0 5px 0; font-size: 24px; font-weight: 600; }
        .custom-header p { margin: 0; opacity: 0.9; font-size: 14px; }
        .powered-by {
            text-align: center;
            padding: 20px;
            color: #64748b;
            font-size: 12px;
            border-top: 1px solid #e2e8f0;
            margin-top: 40px;
        }
        .powered-by a { color: {$themeColor}; text-decoration: none; }
        .powered-by a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="custom-header">
        <h1>{$title}</h1>
        <p>Interactive API documentation - Try out endpoints directly from this page</p>
    </div>
    <div id="swagger-ui"></div>
    <div class="powered-by">
        Powered by <a href="https://github.com/stackmasteraliza/laravel-api-response-builder" target="_blank">Laravel API Response Builder</a>
    </div>
    <script src="https://unpkg.com/swagger-ui-dist@5/swagger-ui-bundle.js"></script>
    <script src="https://unpkg.com/swagger-ui-dist@5/swagger-ui-standalone-preset.js"></script>
    <script>
        window.onload = function() {
            const ui = SwaggerUIBundle({
                url: "{$specUrl}",
                dom_id: '#swagger-ui',
                deepLinking: true,
                presets: [
                    SwaggerUIBundle.presets.apis,
                    SwaggerUIStandalonePreset
                ],
                plugins: [
                    SwaggerUIBundle.plugins.DownloadUrl
                ],
                layout: "StandaloneLayout",
                defaultModelsExpandDepth: 1,
                defaultModelExpandDepth: 1,
                docExpansion: 'list',
                filter: true,
                showExtensions: true,
                showCommonExtensions: true,
                tryItOutEnabled: true,
                persistAuthorization: true
            });
            window.ui = ui;
        };
    </script>
</body>
</html>
HTML;
    }
}
