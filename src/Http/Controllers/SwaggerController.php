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
            background: var(--primary);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 16px;
            color: white;
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
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: var(--bg-elevated);
            color: var(--text-secondary);
            border: 1px solid var(--border-color);
        }

        .badge-primary {
            background: var(--primary);
            color: white;
            border: none;
        }

        /* Auth Button */
        .auth-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            background: var(--bg-elevated);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-primary);
            font-family: 'Inter', sans-serif;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }

        .auth-btn:hover {
            border-color: var(--primary);
            background: rgba(16, 185, 129, 0.1);
        }

        .auth-btn.authorized {
            border-color: var(--primary);
            background: rgba(16, 185, 129, 0.15);
            color: var(--primary-light);
        }

        .auth-btn svg {
            width: 16px;
            height: 16px;
        }

        /* Hero Section */
        .hero-section {
            background: var(--bg-card);
            padding: 48px 32px;
            text-align: center;
            border-bottom: 1px solid var(--border-color);
            position: relative;
            overflow: hidden;
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
            color: var(--primary);
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

        /* Custom Search Bar */
        .search-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 24px 32px;
        }

        .search-box {
            position: relative;
            max-width: 500px;
        }

        .search-box svg {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            color: var(--text-muted);
            pointer-events: none;
        }

        .search-input {
            width: 100%;
            padding: 14px 16px 14px 48px;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            color: var(--text-primary);
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            transition: all 0.2s;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-glow);
        }

        .search-input::placeholder {
            color: var(--text-muted);
        }

        /* Authorization Modal */
        .auth-modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.8);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(4px);
        }

        .auth-modal-overlay.active {
            display: flex;
        }

        .auth-modal {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 32px;
            width: 100%;
            max-width: 480px;
            margin: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
        }

        .auth-modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .auth-modal-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .auth-modal-close {
            background: none;
            border: none;
            color: var(--text-secondary);
            cursor: pointer;
            padding: 8px;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .auth-modal-close:hover {
            background: var(--bg-elevated);
            color: var(--text-primary);
        }

        .auth-type-selector {
            display: flex;
            gap: 8px;
            margin-bottom: 20px;
        }

        .auth-type-btn {
            flex: 1;
            padding: 12px;
            background: var(--bg-elevated);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-secondary);
            font-family: 'Inter', sans-serif;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }

        .auth-type-btn:hover {
            border-color: var(--primary);
        }

        .auth-type-btn.active {
            background: rgba(16, 185, 129, 0.15);
            border-color: var(--primary);
            color: var(--primary-light);
        }

        .auth-input-group {
            margin-bottom: 20px;
        }

        .auth-input-label {
            display: block;
            font-size: 12px;
            font-weight: 500;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .auth-input {
            width: 100%;
            padding: 12px 16px;
            background: var(--bg-input);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-primary);
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
            transition: all 0.2s;
        }

        .auth-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-glow);
        }

        .auth-input::placeholder {
            color: var(--text-muted);
            font-family: 'Inter', sans-serif;
        }

        .auth-hint {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 8px;
        }

        .auth-modal-actions {
            display: flex;
            gap: 12px;
            margin-top: 24px;
        }

        .auth-modal-btn {
            flex: 1;
            padding: 12px 20px;
            border-radius: 8px;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .auth-modal-btn-primary {
            background: var(--primary);
            border: none;
            color: white;
            box-shadow: 0 4px 14px var(--primary-glow);
        }

        .auth-modal-btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }

        .auth-modal-btn-secondary {
            background: transparent;
            border: 1px solid var(--border-color);
            color: var(--text-secondary);
        }

        .auth-modal-btn-secondary:hover {
            border-color: var(--danger);
            color: var(--danger);
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
            padding: 0 32px 32px;
        }

        /* Hide default Swagger elements */
        .swagger-ui .topbar,
        .swagger-ui .information-container,
        .swagger-ui .scheme-container,
        .swagger-ui .filter-container,
        .swagger-ui .filter {
            display: none !important;
        }

        /* Operation Tags - Category Headers */
        .swagger-ui .opblock-tag-section {
            margin-bottom: 32px;
            background: var(--bg-card) !important;
            border-radius: 16px !important;
            border: 1px solid var(--border-color) !important;
            overflow: hidden;
        }

        .swagger-ui .opblock-tag {
            color: var(--text-primary) !important;
            font-family: 'Inter', sans-serif !important;
            font-weight: 700 !important;
            font-size: 18px !important;
            border: none !important;
            padding: 20px 24px !important;
            background: var(--bg-elevated) !important;
            border-bottom: 1px solid var(--border-color) !important;
            transition: all 0.2s;
            margin: 0 !important;
        }

        .swagger-ui .opblock-tag:hover {
            background: rgba(16, 185, 129, 0.1) !important;
        }

        .swagger-ui .opblock-tag svg {
            fill: var(--primary) !important;
            transition: fill 0.2s;
        }

        .swagger-ui .opblock-tag:hover svg {
            fill: var(--primary-light) !important;
        }

        .swagger-ui .opblock-tag small {
            color: var(--text-muted) !important;
            font-size: 12px !important;
            background: var(--bg-input) !important;
            padding: 4px 10px !important;
            border-radius: 20px !important;
            margin-left: 12px !important;
        }

        .swagger-ui .opblock-tag-section .operation-tag-content {
            padding: 16px !important;
            background: var(--bg-card) !important;
        }

        /* Operation Blocks */
        .swagger-ui .opblock {
            background: var(--bg-elevated) !important;
            border: 1px solid var(--border-color) !important;
            border-radius: 10px !important;
            margin-bottom: 10px !important;
            box-shadow: none !important;
            overflow: hidden;
            transition: all 0.2s;
        }

        .swagger-ui .opblock:hover {
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 1px var(--primary), 0 4px 12px rgba(16, 185, 129, 0.15) !important;
        }

        .swagger-ui .opblock:last-child {
            margin-bottom: 0 !important;
        }

        .swagger-ui .opblock .opblock-summary {
            border: none !important;
            padding: 14px 16px !important;
            background: var(--bg-elevated) !important;
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
            background: var(--bg-elevated) !important;
        }
        .swagger-ui .opblock.opblock-post {
            border-left: 3px solid var(--post) !important;
            background: var(--bg-elevated) !important;
        }
        .swagger-ui .opblock.opblock-put {
            border-left: 3px solid var(--put) !important;
            background: var(--bg-elevated) !important;
        }
        .swagger-ui .opblock.opblock-delete {
            border-left: 3px solid var(--delete) !important;
            background: var(--bg-elevated) !important;
        }
        .swagger-ui .opblock.opblock-patch {
            border-left: 3px solid var(--patch) !important;
            background: var(--bg-elevated) !important;
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

        .swagger-ui select {
            background: var(--bg-input) !important;
            color: var(--text-primary) !important;
            border: 1px solid var(--border-color) !important;
            border-radius: 8px !important;
            padding: 10px 14px !important;
            font-family: 'Inter', sans-serif !important;
            font-size: 13px !important;
        }

        .swagger-ui label {
            color: var(--text-secondary) !important;
            font-family: 'Inter', sans-serif !important;
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

        /* Models/Schemas Section */
        .swagger-ui section.models {
            border: 1px solid var(--border-color) !important;
            border-radius: 16px !important;
            background: var(--bg-card) !important;
            margin-top: 32px;
            overflow: hidden;
        }

        .swagger-ui section.models h4 {
            color: var(--text-primary) !important;
            font-family: 'Inter', sans-serif !important;
            font-size: 18px !important;
            font-weight: 700 !important;
            padding: 20px 24px !important;
            background: var(--bg-elevated) !important;
            border-bottom: 1px solid var(--border-color) !important;
            margin: 0 !important;
        }

        .swagger-ui section.models h4 svg {
            fill: var(--primary) !important;
        }

        .swagger-ui section.models .models-control {
            background: var(--bg-card) !important;
        }

        .swagger-ui .model-container {
            background: var(--bg-elevated) !important;
            border-radius: 12px !important;
            margin: 16px !important;
            border: 1px solid var(--border-color) !important;
            overflow: hidden;
        }

        .swagger-ui .model-box {
            background: var(--bg-elevated) !important;
            padding: 20px !important;
        }

        .swagger-ui .model {
            color: var(--text-secondary) !important;
            font-family: 'JetBrains Mono', monospace !important;
            font-size: 13px !important;
            line-height: 1.8 !important;
        }

        .swagger-ui .model-title {
            color: var(--text-primary) !important;
            font-family: 'Inter', sans-serif !important;
            font-weight: 600 !important;
            font-size: 15px !important;
            padding: 16px 20px !important;
            background: var(--bg-elevated) !important;
            border-bottom: 1px solid var(--border-color) !important;
            margin: 0 !important;
            display: block !important;
        }

        .swagger-ui .model-title__text {
            color: var(--primary-light) !important;
            font-weight: 600 !important;
        }

        .swagger-ui .prop-type {
            color: var(--primary-light) !important;
            font-weight: 500 !important;
        }

        .swagger-ui .prop-format {
            color: var(--text-muted) !important;
            font-style: italic !important;
        }

        /* Schema property rows */
        .swagger-ui .model .property {
            padding: 8px 0 !important;
            border-bottom: 1px solid var(--border-subtle) !important;
        }

        .swagger-ui .model .property:last-child {
            border-bottom: none !important;
        }

        .swagger-ui .model .property-name {
            color: var(--text-primary) !important;
            font-weight: 500 !important;
        }

        /* Schema expand/collapse button */
        .swagger-ui .model-toggle {
            background: var(--bg-input) !important;
            border-radius: 4px !important;
            padding: 2px 6px !important;
        }

        .swagger-ui .model-toggle::after {
            color: var(--primary) !important;
        }

        /* Inner braces styling */
        .swagger-ui .model .brace-open,
        .swagger-ui .model .brace-close {
            color: var(--text-muted) !important;
        }

        .swagger-ui .model .inner-object {
            padding-left: 20px !important;
            border-left: 2px solid var(--border-color) !important;
            margin-left: 8px !important;
        }

        /* Example values */
        .swagger-ui .model .model-example {
            background: var(--bg-input) !important;
            border-radius: 6px !important;
            padding: 12px !important;
            margin-top: 8px !important;
        }

        .swagger-ui span.model-example-value,
        .swagger-ui .example {
            color: var(--primary-light) !important;
            font-style: italic !important;
        }

        /* Required star */
        .swagger-ui .model .star {
            color: var(--danger) !important;
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
            .search-container {
                padding: 16px;
            }
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
                <button class="auth-btn" id="auth-btn" onclick="openAuthModal()">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <span id="auth-btn-text">Authorize</span>
                </button>
                <span class="badge badge-primary">OpenAPI 3.0</span>
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

    <!-- Custom Search Bar -->
    <div class="search-container">
        <div class="search-box">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input type="text" class="search-input" id="api-search" placeholder="Search APIs by path, method, or description..." oninput="filterOperations(this.value)">
        </div>
    </div>

    <div id="swagger-ui"></div>

    <div class="powered-by">
        Built with <a href="https://github.com/stackmasteraliza/laravel-api-response-builder" target="_blank">Laravel API Response Builder</a>
    </div>

    <!-- Authorization Modal -->
    <div class="auth-modal-overlay" id="auth-modal">
        <div class="auth-modal">
            <div class="auth-modal-header">
                <h3 class="auth-modal-title">Authorization</h3>
                <button class="auth-modal-close" onclick="closeAuthModal()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="auth-type-selector">
                <button class="auth-type-btn active" data-type="bearer" onclick="selectAuthType('bearer')">Bearer Token</button>
                <button class="auth-type-btn" data-type="apikey" onclick="selectAuthType('apikey')">API Key</button>
            </div>

            <div id="bearer-auth" class="auth-content">
                <div class="auth-input-group">
                    <label class="auth-input-label">Bearer Token</label>
                    <input type="text" class="auth-input" id="bearer-token" placeholder="Enter your access token">
                    <p class="auth-hint">Token will be sent as: Authorization: Bearer &lt;token&gt;</p>
                </div>
            </div>

            <div id="apikey-auth" class="auth-content" style="display: none;">
                <div class="auth-input-group">
                    <label class="auth-input-label">Header Name</label>
                    <input type="text" class="auth-input" id="apikey-header" placeholder="X-API-Key" value="X-API-Key">
                </div>
                <div class="auth-input-group">
                    <label class="auth-input-label">API Key</label>
                    <input type="text" class="auth-input" id="apikey-value" placeholder="Enter your API key">
                </div>
            </div>

            <div class="auth-modal-actions">
                <button class="auth-modal-btn auth-modal-btn-secondary" onclick="clearAuth()">Clear</button>
                <button class="auth-modal-btn auth-modal-btn-primary" onclick="applyAuth()">Apply</button>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/swagger-ui-dist@5/swagger-ui-bundle.js"></script>
    <script src="https://unpkg.com/swagger-ui-dist@5/swagger-ui-standalone-preset.js"></script>
    <script>
        let currentAuthType = 'bearer';
        let authToken = localStorage.getItem('api_auth_token') || '';
        let authType = localStorage.getItem('api_auth_type') || 'bearer';
        let apiKeyHeader = localStorage.getItem('api_auth_header') || 'X-API-Key';

        // Initialize auth button state
        document.addEventListener('DOMContentLoaded', function() {
            if (authToken) {
                updateAuthButton(true);
            }
        });

        function openAuthModal() {
            document.getElementById('auth-modal').classList.add('active');
            if (authToken) {
                if (authType === 'bearer') {
                    document.getElementById('bearer-token').value = authToken;
                } else {
                    document.getElementById('apikey-value').value = authToken;
                    document.getElementById('apikey-header').value = apiKeyHeader;
                }
                selectAuthType(authType);
            }
        }

        function closeAuthModal() {
            document.getElementById('auth-modal').classList.remove('active');
        }

        function selectAuthType(type) {
            currentAuthType = type;
            document.querySelectorAll('.auth-type-btn').forEach(btn => {
                btn.classList.toggle('active', btn.dataset.type === type);
            });
            document.getElementById('bearer-auth').style.display = type === 'bearer' ? 'block' : 'none';
            document.getElementById('apikey-auth').style.display = type === 'apikey' ? 'block' : 'none';
        }

        function applyAuth() {
            if (currentAuthType === 'bearer') {
                authToken = document.getElementById('bearer-token').value;
            } else {
                authToken = document.getElementById('apikey-value').value;
                apiKeyHeader = document.getElementById('apikey-header').value;
            }
            authType = currentAuthType;

            // Save to localStorage
            localStorage.setItem('api_auth_token', authToken);
            localStorage.setItem('api_auth_type', authType);
            localStorage.setItem('api_auth_header', apiKeyHeader);

            // Update Swagger UI authorization
            if (window.ui && authToken) {
                if (authType === 'bearer') {
                    window.ui.preauthorizeApiKey('bearerAuth', authToken);
                } else {
                    window.ui.preauthorizeApiKey('apiKey', authToken);
                }
            }

            updateAuthButton(!!authToken);
            closeAuthModal();
        }

        function clearAuth() {
            authToken = '';
            localStorage.removeItem('api_auth_token');
            localStorage.removeItem('api_auth_type');
            localStorage.removeItem('api_auth_header');

            document.getElementById('bearer-token').value = '';
            document.getElementById('apikey-value').value = '';

            updateAuthButton(false);
            closeAuthModal();
        }

        function updateAuthButton(authorized) {
            const btn = document.getElementById('auth-btn');
            const text = document.getElementById('auth-btn-text');
            if (authorized) {
                btn.classList.add('authorized');
                text.textContent = 'Authorized';
            } else {
                btn.classList.remove('authorized');
                text.textContent = 'Authorize';
            }
        }

        function filterOperations(query) {
            query = query.toLowerCase();
            const opblocks = document.querySelectorAll('.swagger-ui .opblock');
            const tagSections = document.querySelectorAll('.swagger-ui .opblock-tag-section');

            opblocks.forEach(block => {
                const path = block.querySelector('.opblock-summary-path')?.textContent?.toLowerCase() || '';
                const method = block.querySelector('.opblock-summary-method')?.textContent?.toLowerCase() || '';
                const desc = block.querySelector('.opblock-summary-description')?.textContent?.toLowerCase() || '';

                const matches = !query || path.includes(query) || method.includes(query) || desc.includes(query);
                block.style.display = matches ? '' : 'none';
            });

            // Hide empty tag sections
            tagSections.forEach(section => {
                const visibleOps = section.querySelectorAll('.opblock:not([style*="display: none"])');
                section.style.display = visibleOps.length > 0 || !query ? '' : 'none';
            });
        }

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
                filter: false,
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

                    // Apply saved authorization
                    if (authToken) {
                        setTimeout(() => {
                            if (authType === 'bearer') {
                                window.ui.preauthorizeApiKey('bearerAuth', authToken);
                            }
                        }, 500);
                    }
                }
            });
            window.ui = ui;
        };

        // Close modal on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAuthModal();
            }
        });

        // Close modal on overlay click
        document.getElementById('auth-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeAuthModal();
            }
        });
    </script>
</body>
</html>
HTML;
    }
}
