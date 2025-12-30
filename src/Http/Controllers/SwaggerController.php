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
        $themeColor = config('api-response.openapi.theme_color', '#10b981');

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title}</title>
    <link rel="icon" type="image/png" href="https://static1.smartbear.co/swagger/media/assets/swagger_fav.png">
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/swagger-ui-dist@5/swagger-ui.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: {$themeColor};
            --primary-dark: #059669;
            --primary-light: #047857;
            --bg-light: #ffffff;
            --bg-card: #f8fafc;
            --bg-input: #f1f5f9;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --border-color: #e2e8f0;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #3b82f6;
            --purple: #8b5cf6;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        html {
            overflow-y: scroll;
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            background: var(--bg-light);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            color: var(--text-primary);
            min-height: 100vh;
        }

        /* Custom Header */
        .custom-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border-bottom: 1px solid var(--border-color);
            padding: 0;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-content {
            max-width: 1460px;
            margin: 0 auto;
            padding: 16px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .swagger-logo {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.1);
        }

        .swagger-logo svg {
            width: 24px;
            height: 24px;
            fill: white;
        }

        .header-title {
            display: flex;
            flex-direction: column;
        }

        .header-title h1 {
            font-size: 20px;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: -0.5px;
        }

        .header-title span {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.8);
            margin-top: 2px;
        }

        .header-badge {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-version {
            background: rgba(255, 255, 255, 0.2);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .badge-oas {
            background: rgba(255, 255, 255, 0.2);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        /* Swagger UI Overrides - Light Theme with Green */
        /* Force white background everywhere */
        #swagger-ui,
        .swagger-ui,
        .swagger-ui *:not(.opblock-summary-method):not(.btn):not(.badge):not(.swagger-logo):not(.custom-header):not(.custom-header *) {
            background-color: transparent;
        }

        #swagger-ui {
            background: var(--bg-light) !important;
        }

        .swagger-ui,
        .swagger-ui .wrapper,
        .swagger-ui .opblock-tag-section,
        .swagger-ui .opblock-tag-section .opblock,
        .swagger-ui .operation-tag-content,
        .swagger-ui .swagger-container,
        .swagger-ui div,
        .swagger-ui section {
            background: var(--bg-light) !important;
        }

        .swagger-ui {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif !important;
            background: var(--bg-light) !important;
        }

        .swagger-ui .wrapper {
            max-width: 1460px;
            padding: 0 24px;
            background: var(--bg-light) !important;
        }

        .swagger-ui .topbar { display: none !important; }

        /* Force white background on all major containers */
        .swagger-ui .opblock-tag-section,
        .swagger-ui .no-margin,
        .swagger-ui .operations-wrapper,
        .swagger-ui .operation-tag-content,
        .swagger-ui .swagger-container {
            background: var(--bg-light) !important;
        }

        .swagger-ui .information-container {
            background: var(--bg-card) !important;
            border-radius: 12px;
            margin: 24px auto !important;
            padding: 32px !important;
            border: 1px solid var(--border-color);
            max-width: 800px;
            text-align: center;
        }

        .swagger-ui .info {
            margin: 0 auto !important;
        }

        .swagger-ui .info .title {
            font-family: 'Inter', sans-serif !important;
            color: var(--text-primary) !important;
            font-size: 28px !important;
            font-weight: 700 !important;
        }

        .swagger-ui .info .title small {
            background: var(--primary) !important;
            color: white !important;
            border-radius: 6px;
            padding: 4px 8px;
            font-size: 12px;
            vertical-align: middle;
            margin-left: 10px;
        }

        .swagger-ui .info .description,
        .swagger-ui .info .description p {
            font-family: 'Inter', sans-serif !important;
            color: var(--text-secondary) !important;
            font-size: 14px !important;
            line-height: 1.6 !important;
            text-align: center;
        }

        .swagger-ui .info a {
            color: var(--primary) !important;
        }

        .swagger-ui .info a:hover {
            color: var(--primary-dark) !important;
        }

        .swagger-ui .scheme-container {
            background: var(--bg-card) !important;
            box-shadow: none !important;
            border-radius: 12px;
            padding: 16px 24px !important;
            margin-bottom: 24px;
            border: 1px solid var(--border-color);
        }

        .swagger-ui .schemes-title,
        .swagger-ui label {
            color: var(--text-secondary) !important;
            font-family: 'Inter', sans-serif !important;
        }

        .swagger-ui select {
            background: var(--bg-input) !important;
            color: var(--text-primary) !important;
            border: 1px solid var(--border-color) !important;
            border-radius: 8px !important;
            padding: 8px 12px !important;
            font-family: 'Inter', sans-serif !important;
        }

        /* Filter Input */
        .swagger-ui .filter-container,
        .swagger-ui .filter-wrapper,
        .swagger-ui .filter {
            background: var(--bg-card) !important;
            border-radius: 12px;
            padding: 16px 24px;
            margin-bottom: 24px;
            border: 1px solid var(--border-color);
        }

        .swagger-ui .filter input,
        .swagger-ui .operation-filter-input {
            background: var(--bg-input) !important;
            color: var(--text-primary) !important;
            border: 1px solid var(--border-color) !important;
            border-radius: 8px !important;
            padding: 12px 16px !important;
            font-family: 'Inter', sans-serif !important;
            width: 100%;
        }

        .swagger-ui .filter input::placeholder,
        .swagger-ui .operation-filter-input::placeholder {
            color: var(--text-secondary) !important;
        }

        /* Operation Tags */
        .swagger-ui .opblock-tag {
            color: var(--text-primary) !important;
            font-family: 'Inter', sans-serif !important;
            font-weight: 600 !important;
            border-bottom: 1px solid var(--border-color) !important;
            padding: 16px 0 !important;
            background: var(--bg-light) !important;
        }

        .swagger-ui .opblock-tag:hover {
            background: rgba(16, 185, 129, 0.05) !important;
        }

        .swagger-ui .opblock-tag svg {
            fill: var(--text-secondary) !important;
        }

        .swagger-ui .opblock-tag small {
            color: var(--text-secondary) !important;
        }

        /* Operation Blocks */
        .swagger-ui .opblock {
            background: var(--bg-light) !important;
            border: 1px solid var(--border-color) !important;
            border-radius: 12px !important;
            margin-bottom: 12px !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05) !important;
            overflow: hidden;
        }

        .swagger-ui .opblock .opblock-summary {
            border: none !important;
            padding: 12px 16px !important;
        }

        .swagger-ui .opblock .opblock-summary-method {
            border-radius: 6px !important;
            font-family: 'Inter', sans-serif !important;
            font-weight: 600 !important;
            font-size: 12px !important;
            min-width: 70px !important;
            padding: 8px 0 !important;
            text-align: center;
        }

        .swagger-ui .opblock.opblock-get .opblock-summary-method {
            background: var(--success) !important;
        }
        .swagger-ui .opblock.opblock-post .opblock-summary-method {
            background: var(--info) !important;
        }
        .swagger-ui .opblock.opblock-put .opblock-summary-method {
            background: var(--warning) !important;
        }
        .swagger-ui .opblock.opblock-delete .opblock-summary-method {
            background: var(--danger) !important;
        }
        .swagger-ui .opblock.opblock-patch .opblock-summary-method {
            background: var(--purple) !important;
        }

        .swagger-ui .opblock.opblock-get {
            border-left: 4px solid var(--success) !important;
        }
        .swagger-ui .opblock.opblock-post {
            border-left: 4px solid var(--info) !important;
        }
        .swagger-ui .opblock.opblock-put {
            border-left: 4px solid var(--warning) !important;
        }
        .swagger-ui .opblock.opblock-delete {
            border-left: 4px solid var(--danger) !important;
        }
        .swagger-ui .opblock.opblock-patch {
            border-left: 4px solid var(--purple) !important;
        }

        .swagger-ui .opblock .opblock-summary-path {
            color: var(--text-primary) !important;
            font-family: 'JetBrains Mono', monospace !important;
            font-size: 14px !important;
        }

        .swagger-ui .opblock .opblock-summary-description {
            color: var(--text-secondary) !important;
            font-family: 'Inter', sans-serif !important;
            font-size: 13px !important;
        }

        .swagger-ui .opblock-body {
            background: var(--bg-card) !important;
        }

        .swagger-ui .opblock-section-header {
            background: var(--bg-input) !important;
            box-shadow: none !important;
        }

        .swagger-ui .opblock-section-header h4 {
            color: var(--text-primary) !important;
            font-family: 'Inter', sans-serif !important;
        }

        .swagger-ui .opblock-description-wrapper,
        .swagger-ui .opblock-description-wrapper p {
            color: var(--text-secondary) !important;
            font-family: 'Inter', sans-serif !important;
        }

        /* Parameters */
        .swagger-ui .parameters-col_name {
            color: var(--text-primary) !important;
        }

        .swagger-ui .parameter__name {
            color: var(--text-primary) !important;
            font-family: 'JetBrains Mono', monospace !important;
        }

        .swagger-ui .parameter__type {
            color: var(--primary) !important;
            font-family: 'JetBrains Mono', monospace !important;
        }

        .swagger-ui .parameter__in {
            color: var(--text-secondary) !important;
        }

        .swagger-ui table tbody tr td {
            border-color: var(--border-color) !important;
            color: var(--text-secondary) !important;
        }

        .swagger-ui .parameters-col_description input,
        .swagger-ui .parameters-col_description textarea {
            background: var(--bg-light) !important;
            color: var(--text-primary) !important;
            border: 1px solid var(--border-color) !important;
            border-radius: 6px !important;
        }

        /* Buttons */
        .swagger-ui .btn {
            font-family: 'Inter', sans-serif !important;
            font-weight: 600 !important;
            border-radius: 8px !important;
            transition: all 0.2s ease !important;
        }

        .swagger-ui .btn.execute {
            background: var(--primary) !important;
            border-color: var(--primary) !important;
            color: white !important;
            padding: 10px 24px !important;
            font-size: 13px !important;
        }

        .swagger-ui .btn.execute:hover {
            background: var(--primary-dark) !important;
            border-color: var(--primary-dark) !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3) !important;
        }

        .swagger-ui .btn.cancel {
            background: transparent !important;
            border: 1px solid var(--border-color) !important;
            color: var(--text-secondary) !important;
        }

        .swagger-ui .btn.cancel:hover {
            border-color: var(--danger) !important;
            color: var(--danger) !important;
        }

        /* Response Section */
        .swagger-ui .responses-wrapper {
            background: transparent !important;
        }

        .swagger-ui .responses-inner {
            padding: 16px !important;
        }

        .swagger-ui .response-col_status {
            color: var(--text-primary) !important;
            font-family: 'JetBrains Mono', monospace !important;
        }

        .swagger-ui .response-col_description {
            color: var(--text-secondary) !important;
        }

        .swagger-ui .response code {
            background: var(--bg-input) !important;
            color: var(--primary) !important;
            border-radius: 4px;
            padding: 2px 6px;
        }

        /* Code Blocks and Textareas */
        .swagger-ui .highlight-code,
        .swagger-ui .microlight {
            background: #1e293b !important;
            border-radius: 8px !important;
            border: 1px solid var(--border-color) !important;
            font-family: 'JetBrains Mono', monospace !important;
            font-size: 13px !important;
        }

        .swagger-ui .microlight {
            color: #34d399 !important;
        }

        /* Request body textarea */
        .swagger-ui textarea,
        .swagger-ui .body-param textarea,
        .swagger-ui .body-param__text,
        .swagger-ui textarea.body-param__text {
            background: #1e293b !important;
            color: #e2e8f0 !important;
            border: 1px solid var(--border-color) !important;
            border-radius: 8px !important;
            font-family: 'JetBrains Mono', monospace !important;
            font-size: 13px !important;
            padding: 12px !important;
        }

        .swagger-ui .body-param {
            background: var(--bg-card) !important;
        }

        /* Example value and model displays */
        .swagger-ui .example,
        .swagger-ui .model-example {
            background: #1e293b !important;
            color: #e2e8f0 !important;
        }

        /* Rendred markdown in descriptions */
        .swagger-ui .markdown p,
        .swagger-ui .markdown {
            color: var(--text-secondary) !important;
        }

        /* Models Section */
        .swagger-ui section.models {
            border: 1px solid var(--border-color) !important;
            border-radius: 12px !important;
            background: var(--bg-card) !important;
        }

        .swagger-ui section.models h4 {
            color: var(--text-primary) !important;
            font-family: 'Inter', sans-serif !important;
        }

        .swagger-ui .model-container {
            background: var(--bg-input) !important;
            border-radius: 8px;
            margin: 8px 0;
        }

        .swagger-ui .model {
            color: var(--text-secondary) !important;
            font-family: 'JetBrains Mono', monospace !important;
        }

        .swagger-ui .model-title {
            color: var(--text-primary) !important;
            font-family: 'Inter', sans-serif !important;
        }

        .swagger-ui .prop-type {
            color: var(--primary) !important;
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--bg-card);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--border-color);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--text-secondary);
        }

        /* Footer */
        .powered-by {
            text-align: center;
            padding: 24px;
            color: var(--text-secondary);
            font-size: 13px;
            border-top: 1px solid var(--border-color);
            margin-top: 48px;
            background: var(--bg-card);
        }

        .powered-by a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        .powered-by a:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 12px;
                text-align: center;
            }
            .header-left {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="custom-header">
        <div class="header-content">
            <div class="header-left">
                <div class="swagger-logo">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm0 22c-5.523 0-10-4.477-10-10S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10zm-1-15v8l6-4-6-4z"/>
                    </svg>
                </div>
                <div class="header-title">
                    <h1>{$title}</h1>
                    <span>Interactive API Documentation</span>
                </div>
            </div>
            <div class="header-badge">
                <span class="badge badge-version">v1.0.0</span>
                <span class="badge badge-oas">OAS 3.0</span>
            </div>
        </div>
    </div>
    <div id="swagger-ui"></div>
    <div class="powered-by">
        Powered by <a href="https://github.com/stackmasteraliza/laravel-api-response-builder" target="_blank">Laravel API Response Builder</a> &bull; Built with Swagger UI
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
                persistAuthorization: true,
                syntaxHighlight: {
                    activate: true,
                    theme: "agate"
                }
            });
            window.ui = ui;
        };
    </script>
</body>
</html>
HTML;
    }
}
