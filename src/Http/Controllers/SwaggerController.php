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
        $appName = config('api-response.openapi.app_name', config('app.name', 'API'));
        $appLogo = config('api-response.openapi.app_logo', null);
        $specUrl = url('/api-docs/openapi.json');

        $html = $this->getSwaggerUIHtml($title, $specUrl, $appName, $appLogo);

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
    protected function getSwaggerUIHtml(string $title, string $specUrl, string $appName, ?string $appLogo): string
    {
        $themeColor = config('api-response.openapi.theme_color', '#10b981');
        $logoHtml = $appLogo
            ? "<img src=\"{$appLogo}\" alt=\"{$appName}\" class=\"app-logo\">"
            : "<div class=\"app-logo-placeholder\">" . strtoupper(substr($appName, 0, 2)) . "</div>";

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title}</title>
    <link rel="icon" type="image/png" href="https://static1.smartbear.co/swagger/media/assets/swagger_fav.png">
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/swagger-ui-dist@5/swagger-ui.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: {$themeColor};
            --primary-dark: #059669;
            --primary-light: #34d399;
            --primary-glow: rgba(16, 185, 129, 0.4);
            --bg-dark: #000000;
            --bg-card: #0d0d0d;
            --bg-elevated: #141414;
            --bg-input: #1a1a1a;
            --text-primary: #ffffff;
            --text-secondary: #71717a;
            --text-muted: #52525b;
            --border-color: #27272a;
            --border-subtle: #1f1f23;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #06b6d4;
            --get: #10b981;
            --post: #3b82f6;
            --put: #f59e0b;
            --delete: #ef4444;
            --patch: #8b5cf6;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        html {
            overflow-y: scroll;
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            background: var(--bg-dark);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            color: var(--text-primary);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Custom Header */
        .custom-header {
            background: var(--bg-dark);
            border-bottom: 1px solid var(--border-color);
            padding: 0;
            position: sticky;
            top: 0;
            z-index: 100;
            backdrop-filter: blur(12px);
        }

        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 16px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .app-logo {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            object-fit: contain;
        }

        .app-logo-placeholder {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 16px;
            color: white;
            box-shadow: 0 4px 20px var(--primary-glow);
        }

        .header-title {
            display: flex;
            flex-direction: column;
        }

        .header-title h1 {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-primary);
            letter-spacing: -0.3px;
        }

        .header-title span {
            font-size: 12px;
            color: var(--text-secondary);
            margin-top: 2px;
            font-weight: 400;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .badge {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: var(--bg-elevated);
            color: var(--text-secondary);
            border: 1px solid var(--border-color);
        }

        .badge-primary {
            background: rgba(16, 185, 129, 0.1);
            color: var(--primary-light);
            border-color: rgba(16, 185, 129, 0.2);
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(180deg, var(--bg-dark) 0%, var(--bg-card) 100%);
            padding: 48px 32px;
            text-align: center;
            border-bottom: 1px solid var(--border-color);
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, var(--primary-glow) 0%, transparent 70%);
            opacity: 0.3;
            pointer-events: none;
        }

        .hero-content {
            position: relative;
            z-index: 1;
            max-width: 700px;
            margin: 0 auto;
        }

        .hero-title {
            font-size: 32px;
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 12px;
            letter-spacing: -0.5px;
        }

        .hero-title span {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-description {
            font-size: 16px;
            color: var(--text-secondary);
            line-height: 1.6;
            margin-bottom: 24px;
        }

        .hero-stats {
            display: flex;
            justify-content: center;
            gap: 32px;
            flex-wrap: wrap;
        }

        .stat-item {
            text-align: center;
        }

        .stat-value {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary-light);
        }

        .stat-label {
            font-size: 12px;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 4px;
        }

        /* Swagger UI Overrides */
        #swagger-ui {
            background: var(--bg-dark) !important;
        }

        .swagger-ui,
        .swagger-ui .wrapper,
        .swagger-ui div,
        .swagger-ui section {
            background: var(--bg-dark) !important;
        }

        .swagger-ui {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif !important;
        }

        .swagger-ui .wrapper {
            max-width: 1400px;
            padding: 32px;
        }

        .swagger-ui .topbar,
        .swagger-ui .information-container {
            display: none !important;
        }

        /* Scheme Container */
        .swagger-ui .scheme-container {
            background: var(--bg-card) !important;
            box-shadow: none !important;
            border-radius: 12px;
            padding: 20px 24px !important;
            margin-bottom: 32px;
            border: 1px solid var(--border-color);
        }

        .swagger-ui .schemes-title,
        .swagger-ui label {
            color: var(--text-secondary) !important;
            font-family: 'Inter', sans-serif !important;
            font-size: 12px !important;
            font-weight: 500 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
        }

        .swagger-ui select {
            background: var(--bg-input) !important;
            color: var(--text-primary) !important;
            border: 1px solid var(--border-color) !important;
            border-radius: 8px !important;
            padding: 10px 14px !important;
            font-family: 'Inter', sans-serif !important;
            font-size: 13px !important;
            cursor: pointer;
            transition: border-color 0.2s;
        }

        .swagger-ui select:hover {
            border-color: var(--primary) !important;
        }

        /* Filter Input */
        .swagger-ui .filter-container,
        .swagger-ui .filter-wrapper,
        .swagger-ui .filter {
            background: var(--bg-card) !important;
            border-radius: 12px;
            padding: 16px 24px;
            margin-bottom: 32px;
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
            font-size: 14px !important;
            width: 100%;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .swagger-ui .filter input:focus,
        .swagger-ui .operation-filter-input:focus {
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 3px var(--primary-glow) !important;
            outline: none !important;
        }

        .swagger-ui .filter input::placeholder,
        .swagger-ui .operation-filter-input::placeholder {
            color: var(--text-muted) !important;
        }

        /* Operation Tags */
        .swagger-ui .opblock-tag-section {
            margin-bottom: 24px;
        }

        .swagger-ui .opblock-tag {
            color: var(--text-primary) !important;
            font-family: 'Inter', sans-serif !important;
            font-weight: 600 !important;
            font-size: 16px !important;
            border: none !important;
            padding: 16px 0 !important;
            background: transparent !important;
            transition: color 0.2s;
        }

        .swagger-ui .opblock-tag:hover {
            color: var(--primary-light) !important;
        }

        .swagger-ui .opblock-tag svg {
            fill: var(--text-secondary) !important;
            transition: fill 0.2s;
        }

        .swagger-ui .opblock-tag:hover svg {
            fill: var(--primary-light) !important;
        }

        .swagger-ui .opblock-tag small {
            color: var(--text-muted) !important;
            font-size: 12px !important;
        }

        /* Operation Blocks */
        .swagger-ui .opblock {
            background: var(--bg-card) !important;
            border: 1px solid var(--border-color) !important;
            border-radius: 12px !important;
            margin-bottom: 12px !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.4) !important;
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .swagger-ui .opblock:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.5) !important;
        }

        .swagger-ui .opblock .opblock-summary {
            border: none !important;
            padding: 16px 20px !important;
            background: var(--bg-card) !important;
        }

        .swagger-ui .opblock .opblock-summary-method {
            border-radius: 6px !important;
            font-family: 'Inter', sans-serif !important;
            font-weight: 700 !important;
            font-size: 11px !important;
            min-width: 70px !important;
            padding: 8px 0 !important;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .swagger-ui .opblock.opblock-get .opblock-summary-method {
            background: var(--get) !important;
        }
        .swagger-ui .opblock.opblock-post .opblock-summary-method {
            background: var(--post) !important;
        }
        .swagger-ui .opblock.opblock-put .opblock-summary-method {
            background: var(--put) !important;
        }
        .swagger-ui .opblock.opblock-delete .opblock-summary-method {
            background: var(--delete) !important;
        }
        .swagger-ui .opblock.opblock-patch .opblock-summary-method {
            background: var(--patch) !important;
        }

        .swagger-ui .opblock.opblock-get {
            border-left: 3px solid var(--get) !important;
        }
        .swagger-ui .opblock.opblock-post {
            border-left: 3px solid var(--post) !important;
        }
        .swagger-ui .opblock.opblock-put {
            border-left: 3px solid var(--put) !important;
        }
        .swagger-ui .opblock.opblock-delete {
            border-left: 3px solid var(--delete) !important;
        }
        .swagger-ui .opblock.opblock-patch {
            border-left: 3px solid var(--patch) !important;
        }

        .swagger-ui .opblock .opblock-summary-path {
            color: var(--text-primary) !important;
            font-family: 'JetBrains Mono', monospace !important;
            font-size: 14px !important;
            font-weight: 500 !important;
        }

        .swagger-ui .opblock .opblock-summary-description {
            color: var(--text-secondary) !important;
            font-family: 'Inter', sans-serif !important;
            font-size: 13px !important;
        }

        .swagger-ui .opblock-body {
            background: var(--bg-elevated) !important;
            padding: 20px !important;
        }

        .swagger-ui .opblock-section-header {
            background: var(--bg-card) !important;
            box-shadow: none !important;
            border-bottom: 1px solid var(--border-color) !important;
            padding: 12px 20px !important;
        }

        .swagger-ui .opblock-section-header h4 {
            color: var(--text-primary) !important;
            font-family: 'Inter', sans-serif !important;
            font-size: 13px !important;
            font-weight: 600 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
        }

        .swagger-ui .opblock-description-wrapper,
        .swagger-ui .opblock-description-wrapper p,
        .swagger-ui .opblock-external-docs-wrapper,
        .swagger-ui .opblock-title_normal {
            color: var(--text-secondary) !important;
            font-family: 'Inter', sans-serif !important;
            font-size: 13px !important;
        }

        /* Parameters */
        .swagger-ui .parameters-col_name {
            color: var(--text-primary) !important;
        }

        .swagger-ui .parameter__name {
            color: var(--text-primary) !important;
            font-family: 'JetBrains Mono', monospace !important;
            font-size: 13px !important;
            font-weight: 500 !important;
        }

        .swagger-ui .parameter__name.required::after {
            color: var(--danger) !important;
        }

        .swagger-ui .parameter__type {
            color: var(--primary-light) !important;
            font-family: 'JetBrains Mono', monospace !important;
            font-size: 12px !important;
        }

        .swagger-ui .parameter__in {
            color: var(--text-muted) !important;
            font-size: 11px !important;
        }

        .swagger-ui table tbody tr td {
            border-color: var(--border-color) !important;
            color: var(--text-secondary) !important;
            padding: 12px 0 !important;
        }

        .swagger-ui .parameters-col_description input,
        .swagger-ui .parameters-col_description textarea,
        .swagger-ui textarea,
        .swagger-ui .body-param textarea,
        .swagger-ui .body-param__text {
            background: var(--bg-input) !important;
            color: var(--text-primary) !important;
            border: 1px solid var(--border-color) !important;
            border-radius: 8px !important;
            font-family: 'JetBrains Mono', monospace !important;
            font-size: 13px !important;
            padding: 12px !important;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .swagger-ui .parameters-col_description input:focus,
        .swagger-ui .parameters-col_description textarea:focus,
        .swagger-ui textarea:focus {
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 3px var(--primary-glow) !important;
            outline: none !important;
        }

        /* Buttons */
        .swagger-ui .btn {
            font-family: 'Inter', sans-serif !important;
            font-weight: 600 !important;
            border-radius: 8px !important;
            transition: all 0.2s ease !important;
            font-size: 13px !important;
        }

        .swagger-ui .btn.execute {
            background: var(--primary) !important;
            border-color: var(--primary) !important;
            color: white !important;
            padding: 12px 28px !important;
            box-shadow: 0 4px 14px var(--primary-glow) !important;
        }

        .swagger-ui .btn.execute:hover {
            background: var(--primary-dark) !important;
            border-color: var(--primary-dark) !important;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px var(--primary-glow) !important;
        }

        .swagger-ui .btn.cancel {
            background: transparent !important;
            border: 1px solid var(--border-color) !important;
            color: var(--text-secondary) !important;
            padding: 12px 28px !important;
        }

        .swagger-ui .btn.cancel:hover {
            border-color: var(--danger) !important;
            color: var(--danger) !important;
        }

        .swagger-ui .btn-group .btn {
            padding: 8px 16px !important;
        }

        /* Response Section */
        .swagger-ui .responses-wrapper {
            background: transparent !important;
        }

        .swagger-ui .responses-inner {
            padding: 0 !important;
        }

        .swagger-ui .response-col_status {
            color: var(--text-primary) !important;
            font-family: 'JetBrains Mono', monospace !important;
            font-size: 13px !important;
            font-weight: 600 !important;
        }

        .swagger-ui .response-col_description {
            color: var(--text-secondary) !important;
        }

        .swagger-ui .response code {
            background: var(--bg-input) !important;
            color: var(--primary-light) !important;
            border-radius: 4px;
            padding: 2px 6px;
            font-family: 'JetBrains Mono', monospace !important;
        }

        /* Code Blocks */
        .swagger-ui .highlight-code,
        .swagger-ui .microlight,
        .swagger-ui pre {
            background: var(--bg-input) !important;
            border-radius: 8px !important;
            border: 1px solid var(--border-color) !important;
            font-family: 'JetBrains Mono', monospace !important;
            font-size: 13px !important;
            padding: 16px !important;
        }

        .swagger-ui .microlight {
            color: var(--primary-light) !important;
        }

        /* Models Section */
        .swagger-ui section.models {
            border: 1px solid var(--border-color) !important;
            border-radius: 12px !important;
            background: var(--bg-card) !important;
            margin-top: 32px;
        }

        .swagger-ui section.models h4 {
            color: var(--text-primary) !important;
            font-family: 'Inter', sans-serif !important;
            font-size: 14px !important;
            font-weight: 600 !important;
        }

        .swagger-ui .model-container {
            background: var(--bg-elevated) !important;
            border-radius: 8px;
            margin: 8px 0;
        }

        .swagger-ui .model {
            color: var(--text-secondary) !important;
            font-family: 'JetBrains Mono', monospace !important;
            font-size: 12px !important;
        }

        .swagger-ui .model-title {
            color: var(--text-primary) !important;
            font-family: 'Inter', sans-serif !important;
        }

        .swagger-ui .prop-type {
            color: var(--primary-light) !important;
        }

        .swagger-ui .prop-format {
            color: var(--text-muted) !important;
        }

        /* Tabs */
        .swagger-ui .tab li {
            color: var(--text-secondary) !important;
            font-family: 'Inter', sans-serif !important;
        }

        .swagger-ui .tab li.active {
            color: var(--text-primary) !important;
        }

        .swagger-ui .tab li button.tablinks {
            color: inherit !important;
            background: transparent !important;
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--bg-dark);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--border-color);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--text-muted);
        }

        /* Footer */
        .powered-by {
            text-align: center;
            padding: 32px;
            color: var(--text-muted);
            font-size: 13px;
            border-top: 1px solid var(--border-color);
            margin-top: 48px;
            background: var(--bg-card);
        }

        .powered-by a {
            color: var(--primary-light);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        .powered-by a:hover {
            color: var(--primary);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 16px;
                text-align: center;
                padding: 16px;
            }
            .header-left {
                flex-direction: column;
            }
            .hero-section {
                padding: 32px 16px;
            }
            .hero-title {
                font-size: 24px;
            }
            .hero-stats {
                gap: 24px;
            }
            .swagger-ui .wrapper {
                padding: 16px;
            }
        }

        /* Loading Animation */
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .loading {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>
</head>
<body>
    <div class="custom-header">
        <div class="header-content">
            <div class="header-left">
                {$logoHtml}
                <div class="header-title">
                    <h1>{$appName}</h1>
                    <span>API Documentation</span>
                </div>
            </div>
            <div class="header-right">
                <span class="badge badge-primary">OpenAPI 3.0</span>
                <span class="badge">v1.0.0</span>
            </div>
        </div>
    </div>

    <div class="hero-section">
        <div class="hero-content">
            <h2 class="hero-title">Welcome to <span>{$appName}</span> API</h2>
            <p class="hero-description">
                Explore and interact with our API endpoints. This documentation is auto-generated
                from your Laravel routes and provides a complete reference for all available endpoints.
            </p>
            <div class="hero-stats" id="api-stats">
                <div class="stat-item">
                    <div class="stat-value" id="stat-endpoints">-</div>
                    <div class="stat-label">Endpoints</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" id="stat-tags">-</div>
                    <div class="stat-label">Categories</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" id="stat-schemas">-</div>
                    <div class="stat-label">Schemas</div>
                </div>
            </div>
        </div>
    </div>

    <div id="swagger-ui"></div>

    <div class="powered-by">
        Built with <a href="https://github.com/stackmasteraliza/laravel-api-response-builder" target="_blank">Laravel API Response Builder</a>
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
                    theme: "monokai"
                },
                onComplete: function() {
                    // Fetch spec and update stats
                    fetch("{$specUrl}")
                        .then(response => response.json())
                        .then(spec => {
                            let endpoints = 0;
                            if (spec.paths) {
                                Object.values(spec.paths).forEach(path => {
                                    endpoints += Object.keys(path).filter(m =>
                                        ['get', 'post', 'put', 'delete', 'patch'].includes(m)
                                    ).length;
                                });
                            }
                            const tags = spec.tags ? spec.tags.length : 0;
                            const schemas = spec.components && spec.components.schemas
                                ? Object.keys(spec.components.schemas).length
                                : 0;

                            document.getElementById('stat-endpoints').textContent = endpoints;
                            document.getElementById('stat-tags').textContent = tags || '-';
                            document.getElementById('stat-schemas').textContent = schemas || '-';
                        })
                        .catch(() => {
                            document.getElementById('stat-endpoints').textContent = '-';
                        });
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
