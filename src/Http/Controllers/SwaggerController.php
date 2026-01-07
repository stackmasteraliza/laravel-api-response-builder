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
     * Get versioned OpenAPI specification.
     */
    public function versionedSpec(OpenApiGenerator $generator, string $version): JsonResponse
    {
        $spec = $generator->generate($version);

        return response()->json($spec);
    }

    /**
     * Get available API versions.
     */
    public function versions(OpenApiGenerator $generator): JsonResponse
    {
        $versions = $generator->getAvailableVersions();
        $defaultVersion = $generator->getDefaultVersion();

        return response()->json([
            'versions' => array_values($versions),
            'default' => $defaultVersion,
        ]);
    }

    /**
     * Get WebSocket endpoints configuration.
     */
    public function websockets(): JsonResponse
    {
        $wsConfig = config('api-response.openapi.websocket', []);

        if (! ($wsConfig['enabled'] ?? true)) {
            return response()->json(['endpoints' => []]);
        }

        $baseUrl = $wsConfig['url'] ?? config('app.url', 'http://localhost');
        $wsUrl = preg_replace('/^http/', 'ws', $baseUrl);

        return response()->json([
            'enabled' => true,
            'url' => $wsUrl,
            'endpoints' => $wsConfig['endpoints'] ?? [],
        ]);
    }

    /**
     * Get the Swagger UI HTML.
     */
    protected function getSwaggerUIHtml(string $title, string $specUrl, string $appName, ?string $appLogo): string
    {
        $themeColor = config('api-response.openapi.theme_color', '#10b981');
        $faviconUrl = $appLogo ?: 'https://static1.smartbear.co/swagger/media/assets/swagger_fav.png';
        $logoHtml = $appLogo
            ? "<img src=\"{$appLogo}\" alt=\"{$appName}\" class=\"app-logo\">"
            : "<div class=\"app-logo-placeholder\">" . strtoupper(substr($appName, 0, 2)) . "</div>";

        $rgb = $this->hexToRgb($themeColor);
        $primaryRgb = "{$rgb['r']}, {$rgb['g']}, {$rgb['b']}";
        $lighterRgb = $this->lightenColor($rgb, 0.3);
        $primaryLight = "rgb({$lighterRgb['r']}, {$lighterRgb['g']}, {$lighterRgb['b']})";
        $darkerRgb = $this->darkenColor($rgb, 0.15);
        $primaryDark = "rgb({$darkerRgb['r']}, {$darkerRgb['g']}, {$darkerRgb['b']})";

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title}</title>
    <link rel="icon" type="image/png" href="{$faviconUrl}">
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/swagger-ui-dist@5/swagger-ui.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: {$themeColor};
            --primary-rgb: {$primaryRgb};
            --primary-light: {$primaryLight};
            --primary-glow: rgba({$primaryRgb}, 0.4);
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

        /* Dark Theme (default) */
        [data-theme="dark"] {
            --bg-dark: #000000;
            --bg-card: #0d0d0d;
            --bg-elevated: #141414;
            --bg-input: #1a1a1a;
            --text-primary: #ffffff;
            --text-secondary: #71717a;
            --text-muted: #52525b;
            --border-color: #27272a;
            --border-subtle: #1f1f23;
            --primary-hover: rgba({$primaryRgb}, 0.08);
            --code-bg: #0d0d0d;
        }

        /* Light Theme */
        [data-theme="light"] {
            --bg-dark: #ffffff;
            --bg-card: #f8fafc;
            --bg-elevated: #f1f5f9;
            --bg-input: #e2e8f0;
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --border-subtle: #f1f5f9;
            --primary-hover: rgba({$primaryRgb}, 0.12);
            --primary-light: {$primaryDark};
            --code-bg: #f1f5f9;
        }

        /* Light theme code block overrides - keep dark background with white text */
        [data-theme="light"] .swagger-ui .highlight-code,
        [data-theme="light"] .swagger-ui pre,
        [data-theme="light"] .swagger-ui .highlight-code pre {
            background: #1e293b !important;
            background-color: #1e293b !important;
        }

        [data-theme="light"] .swagger-ui .microlight,
        [data-theme="light"] .swagger-ui code.microlight,
        [data-theme="light"] .swagger-ui pre.microlight {
            background: transparent !important;
            background-color: transparent !important;
            color: #ffffff !important;
        }

        [data-theme="light"] .swagger-ui .microlight span,
        [data-theme="light"] .swagger-ui .microlight * {
            background: transparent !important;
            background-color: transparent !important;
        }

        [data-theme="light"] .swagger-ui .microlight span[style],
        [data-theme="light"] .swagger-ui .microlight *[style] {
            background: transparent !important;
            background-color: transparent !important;
        }

        /* Code blocks always have dark backgrounds - need light text in both themes */
        /* Default: muted gray for punctuation/brackets */
        [data-theme="light"] .swagger-ui .highlight-code .microlight span,
        [data-theme="light"] .swagger-ui .example .microlight span,
        [data-theme="light"] .swagger-ui .example-value .microlight span,
        [data-theme="light"] .swagger-ui pre.microlight span,
        [data-theme="light"] .swagger-ui pre.example span,
        [data-theme="light"] .swagger-ui pre.example code span {
            color: #94a3b8 !important;
        }

        [data-theme="light"] .swagger-ui code.language-json span {
            color: #cbd5e1 !important;
        }

        /* Property names */
        [data-theme="light"] .swagger-ui .highlight-code .hljs-attr,
        [data-theme="light"] .swagger-ui .example .hljs-attr,
        [data-theme="light"] .swagger-ui pre.example .hljs-attr,
        [data-theme="light"] .swagger-ui code.language-json .hljs-attr {
            color: #10b981 !important;
        }

        /* String values */
        [data-theme="light"] .swagger-ui .highlight-code .hljs-string,
        [data-theme="light"] .swagger-ui .example .hljs-string,
        [data-theme="light"] .swagger-ui pre.example .hljs-string,
        [data-theme="light"] .swagger-ui code.language-json .hljs-string {
            color: #10b981 !important;
        }

        /* Numbers */
        [data-theme="light"] .swagger-ui .highlight-code .hljs-number,
        [data-theme="light"] .swagger-ui .example .hljs-number,
        [data-theme="light"] .swagger-ui pre.example .hljs-number,
        [data-theme="light"] .swagger-ui code.language-json .hljs-number {
            color: #3b82f6 !important;
        }

        /* Booleans and literals */
        [data-theme="light"] .swagger-ui .highlight-code .hljs-literal,
        [data-theme="light"] .swagger-ui .example .hljs-literal,
        [data-theme="light"] .swagger-ui pre.example .hljs-literal,
        [data-theme="light"] .swagger-ui code.language-json .hljs-literal {
            color: #3b82f6 !important;
        }

        /* Keep dark background for code blocks in light theme */
        [data-theme="light"] .swagger-ui .example-value,
        [data-theme="light"] .swagger-ui .example,
        [data-theme="light"] .swagger-ui .model-example-value {
            background: #1e293b !important;
            background-color: #1e293b !important;
        }

        [data-theme="light"] .swagger-ui .response-col_description__inner {
            color: #0f172a !important;
        }

        /* Light theme response text overrides */
        [data-theme="light"] .swagger-ui .response-col_description,
        [data-theme="light"] .swagger-ui .response-col_description p,
        [data-theme="light"] .swagger-ui .response-col_description span,
        [data-theme="light"] .swagger-ui .response-col_description__inner p,
        [data-theme="light"] .swagger-ui table.responses-table,
        [data-theme="light"] .swagger-ui table.responses-table td {
            color: #0f172a !important;
        }

        [data-theme="light"] .swagger-ui .response-col_links,
        [data-theme="light"] .swagger-ui .response-col_links i,
        [data-theme="light"] .swagger-ui .response-col_links .response-undocumented {
            color: #475569 !important;
        }

        /* Light theme schemas/models section */
        [data-theme="light"] .swagger-ui section.models h4,
        [data-theme="light"] .swagger-ui section.models h4 span {
            color: #0f172a !important;
        }

        [data-theme="light"] .swagger-ui .model-title,
        [data-theme="light"] .swagger-ui .model-title span {
            color: #0f172a !important;
        }

        [data-theme="light"] .swagger-ui .model,
        [data-theme="light"] .swagger-ui .model span {
            color: #475569 !important;
        }

        /* Light theme model arrows and toggles */
        [data-theme="light"] .swagger-ui .model-toggle::after,
        [data-theme="light"] .swagger-ui .model-toggle {
            color: #475569 !important;
        }

        [data-theme="light"] .swagger-ui .model-toggle:hover::after {
            color: var(--primary) !important;
        }

        [data-theme="light"] .swagger-ui .model .brace-open,
        [data-theme="light"] .swagger-ui .model .brace-close,
        [data-theme="light"] .swagger-ui .model .punctuation {
            color: #64748b !important;
        }

        [data-theme="light"] .swagger-ui .expand-operation svg,
        [data-theme="light"] .swagger-ui .model svg,
        [data-theme="light"] .swagger-ui .models svg {
            fill: #475569 !important;
        }

        [data-theme="light"] .swagger-ui .model-box-control svg {
            fill: #475569 !important;
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
            background: rgba(var(--primary-rgb), 0.1);
        }

        .auth-btn.authorized {
            border-color: var(--primary);
            background: rgba(var(--primary-rgb), 0.15);
            color: var(--primary-light);
        }

        .auth-btn svg {
            width: 16px;
            height: 16px;
        }

        /* Version Switcher */
        .version-switcher {
            position: relative;
            display: none;
        }

        .version-switcher.visible {
            display: block;
        }

        .version-btn {
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

        .version-btn:hover {
            border-color: var(--primary);
            background: rgba(var(--primary-rgb), 0.1);
        }

        .version-btn svg {
            width: 16px;
            height: 16px;
        }

        .version-btn .version-arrow {
            width: 12px;
            height: 12px;
            margin-left: 4px;
            transition: transform 0.2s;
        }

        .version-switcher.active .version-arrow {
            transform: rotate(180deg);
        }

        .version-badge {
            background: var(--primary);
            color: white;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .version-menu {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            min-width: 200px;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.2s;
            z-index: 1000;
            overflow: hidden;
        }

        .version-switcher.active .version-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .version-menu-header {
            padding: 12px 16px 8px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
        }

        .version-menu-list {
            padding: 0 8px 8px;
        }

        .version-menu-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding: 10px 12px;
            background: transparent;
            border: none;
            border-radius: 8px;
            color: var(--text-primary);
            font-family: 'Inter', sans-serif;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.15s;
            text-align: left;
        }

        .version-menu-item:hover {
            background: var(--bg-elevated);
        }

        .version-menu-item.active {
            border: 1px solid var(--primary);
            background: transparent;
        }

        .version-menu-item .version-name {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .version-menu-item .version-title {
            font-weight: 600;
            color: var(--text-primary);
        }

        .version-menu-item .version-desc {
            font-size: 11px;
            color: var(--text-secondary);
        }

        .version-menu-item .version-check {
            width: 16px;
            height: 16px;
            color: var(--primary);
            opacity: 0;
        }

        .version-menu-item.active .version-check {
            opacity: 1;
        }

        /* Theme Toggle */
        .theme-toggle {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: var(--bg-elevated);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-primary);
            cursor: pointer;
            transition: all 0.2s;
        }

        .theme-toggle:hover {
            border-color: var(--primary);
            background: rgba(var(--primary-rgb), 0.1);
        }

        .theme-toggle svg {
            width: 18px;
            height: 18px;
        }

        .theme-toggle .icon-sun,
        .theme-toggle .icon-moon {
            display: none;
        }

        .theme-toggle[data-mode="dark"] .icon-sun {
            display: block;
        }

        .theme-toggle[data-mode="light"] .icon-moon {
            display: block;
        }

        /* WebSocket Tester */
        .ws-btn {
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

        .ws-btn:hover {
            border-color: var(--primary);
            background: rgba(var(--primary-rgb), 0.1);
        }

        .ws-btn svg {
            width: 16px;
            height: 16px;
        }

        .ws-btn.connected {
            border-color: var(--success);
            background: rgba(var(--primary-rgb), 0.15);
            color: var(--success);
        }

        .ws-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(4px);
        }

        .ws-modal.active {
            display: flex;
        }

        .ws-modal-content {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            width: 90%;
            max-width: 800px;
            max-height: 90vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .ws-modal-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .ws-modal-header h3 {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .ws-modal-header h3 svg {
            width: 24px;
            height: 24px;
            color: var(--primary);
        }

        .ws-close-btn {
            background: transparent;
            border: none;
            color: var(--text-secondary);
            cursor: pointer;
            padding: 8px;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .ws-close-btn:hover {
            background: var(--bg-elevated);
            color: var(--text-primary);
        }

        .ws-modal-body {
            padding: 24px;
            overflow-y: auto;
            flex: 1;
        }

        .ws-connection-bar {
            display: flex;
            gap: 12px;
            margin-bottom: 20px;
        }

        .ws-url-input {
            flex: 1;
            padding: 12px 16px;
            background: var(--bg-input);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-primary);
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
        }

        .ws-url-input:focus {
            outline: none;
            border-color: var(--primary);
        }

        .ws-connect-btn {
            padding: 12px 24px;
            background: var(--primary);
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .ws-connect-btn:hover {
            filter: brightness(1.1);
        }

        .ws-connect-btn.disconnect {
            background: var(--danger);
        }

        .ws-status {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 500;
            margin-bottom: 20px;
        }

        .ws-status.disconnected {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
        }

        .ws-status.connected {
            background: rgba(var(--primary-rgb), 0.1);
            color: var(--success);
        }

        .ws-status.connecting {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning);
        }

        .ws-status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: currentColor;
        }

        .ws-panels {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .ws-panel {
            background: var(--bg-elevated);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            overflow: hidden;
        }

        .ws-panel-header {
            padding: 12px 16px;
            background: var(--bg-input);
            border-bottom: 1px solid var(--border-color);
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-secondary);
        }

        .ws-panel-body {
            padding: 16px;
        }

        .ws-message-input {
            width: 100%;
            min-height: 120px;
            padding: 12px;
            background: var(--bg-input);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-primary);
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            resize: vertical;
        }

        .ws-message-input:focus {
            outline: none;
            border-color: var(--primary);
        }

        .ws-send-btn {
            margin-top: 12px;
            width: 100%;
            padding: 10px;
            background: var(--primary);
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .ws-send-btn:hover:not(:disabled) {
            filter: brightness(1.1);
        }

        .ws-send-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .ws-messages {
            height: 300px;
            overflow-y: auto;
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
        }

        .ws-message {
            padding: 8px 12px;
            margin-bottom: 8px;
            border-radius: 6px;
            word-break: break-all;
        }

        .ws-message.sent {
            background: rgba(6, 182, 212, 0.1);
            border-left: 3px solid var(--info);
        }

        .ws-message.received {
            background: rgba(var(--primary-rgb), 0.1);
            border-left: 3px solid var(--success);
        }

        .ws-message.error {
            background: rgba(239, 68, 68, 0.1);
            border-left: 3px solid var(--danger);
        }

        .ws-message.system {
            background: rgba(113, 113, 122, 0.1);
            border-left: 3px solid var(--text-secondary);
            font-style: italic;
        }

        .ws-message-time {
            font-size: 10px;
            color: var(--text-muted);
            margin-bottom: 4px;
        }

        .ws-message-content {
            color: var(--text-primary);
        }

        .ws-presets {
            margin-bottom: 12px;
        }

        .ws-presets-label {
            font-size: 11px;
            color: var(--text-secondary);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .ws-preset-btns {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .ws-preset-btn {
            padding: 6px 12px;
            background: var(--bg-input);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            color: var(--text-secondary);
            font-size: 11px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .ws-preset-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: rgba(var(--primary-rgb), 0.1);
        }

        /* Export Dropdown */
        .export-dropdown {
            position: relative;
        }

        .export-btn {
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

        .export-btn:hover {
            border-color: var(--primary);
            background: rgba(var(--primary-rgb), 0.1);
        }

        .export-btn svg {
            width: 16px;
            height: 16px;
        }

        .export-btn .dropdown-arrow {
            width: 12px;
            height: 12px;
            margin-left: 4px;
            transition: transform 0.2s;
        }

        .export-dropdown.active .dropdown-arrow {
            transform: rotate(180deg);
        }

        .export-menu {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            width: 280px;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.2s;
            z-index: 1000;
            overflow: hidden;
        }

        .export-dropdown.active .export-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .export-menu-header {
            padding: 16px 16px 12px;
            font-size: 14px;
            font-weight: 600;
            color: var(--text-primary);
            border-bottom: 1px solid var(--border-color);
        }

        .export-menu-section {
            padding: 8px;
        }

        .export-menu-section-title {
            padding: 8px 12px 4px;
            font-size: 11px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .export-menu-item {
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
            padding: 10px 12px;
            background: transparent;
            border: none;
            border-radius: 8px;
            color: var(--text-primary);
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.15s;
            text-align: left;
        }

        .export-menu-item:hover {
            background: var(--bg-elevated);
        }

        .export-menu-item svg {
            width: 20px;
            height: 20px;
            color: var(--primary);
            flex-shrink: 0;
        }

        .export-item-content {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .export-item-title {
            font-size: 13px;
            font-weight: 500;
            color: var(--text-primary);
        }

        .export-item-desc {
            font-size: 11px;
            color: var(--text-muted);
        }

        /* Export Toast Notification */
        .export-toast {
            position: fixed;
            bottom: 24px;
            right: 24px;
            padding: 14px 20px;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);
            display: flex;
            align-items: center;
            gap: 12px;
            z-index: 10000;
            transform: translateY(100px);
            opacity: 0;
            transition: all 0.3s ease;
        }

        .export-toast.show {
            transform: translateY(0);
            opacity: 1;
        }

        .export-toast.success {
            border-color: var(--success);
        }

        .export-toast.error {
            border-color: var(--danger);
        }

        .export-toast svg {
            width: 20px;
            height: 20px;
        }

        .export-toast.success svg {
            color: var(--success);
        }

        .export-toast.error svg {
            color: var(--danger);
        }

        .export-toast-content {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .export-toast-title {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .export-toast-desc {
            font-size: 12px;
            color: var(--text-secondary);
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
            background: rgba(var(--primary-rgb), 0.1);
        }

        .auth-type-btn.active {
            background: transparent;
            border-color: var(--primary);
            color: var(--primary);
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
        }

        .auth-modal-btn-primary:hover {
            filter: brightness(0.9);
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
            background: var(--primary-hover) !important;
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
            background: var(--primary-hover) !important;
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
            border: none !important;
            color: white !important;
            padding: 12px 28px !important;
            box-shadow: none !important;
        }

        .swagger-ui .btn.execute:hover {
            filter: brightness(0.9) !important;
            border: none !important;
            transform: translateY(-2px);
            box-shadow: none !important;
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
            color: var(--text-primary) !important;
        }

        .swagger-ui .response-col_description p,
        .swagger-ui .response-col_description span,
        .swagger-ui .response-col_description__inner,
        .swagger-ui .response-col_description__inner p {
            color: var(--text-primary) !important;
        }

        .swagger-ui .response-col_links {
            color: var(--text-secondary) !important;
        }

        .swagger-ui .response-col_links .response-undocumented,
        .swagger-ui .response-col_links i {
            color: var(--text-muted) !important;
        }

        .swagger-ui table.responses-table {
            color: var(--text-primary) !important;
        }

        .swagger-ui table.responses-table td {
            color: var(--text-primary) !important;
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
        .swagger-ui pre {
            background: var(--code-bg) !important;
            border-radius: 8px !important;
            border: 1px solid var(--border-color) !important;
            font-family: 'JetBrains Mono', monospace !important;
            font-size: 13px !important;
            padding: 16px !important;
        }

        .swagger-ui .microlight {
            background: transparent !important;
            color: var(--text-primary) !important;
            font-family: 'JetBrains Mono', monospace !important;
        }

        .swagger-ui .highlight-code > .microlight,
        .swagger-ui pre > .microlight {
            background: transparent !important;
        }

        /* Response example code blocks */
        .swagger-ui .example-value,
        .swagger-ui .response-col_description__inner .highlight-code {
            background: var(--code-bg) !important;
        }

        .swagger-ui .example-value .microlight,
        .swagger-ui .model-example .microlight {
            background: transparent !important;
            color: #94a3b8 !important;
        }

        /* Microlight spans */
        .swagger-ui .microlight span {
            background: transparent !important;
        }

        /* JSON syntax highlighting */
        .swagger-ui .highlight-code .microlight,
        .swagger-ui .example .microlight,
        .swagger-ui .example-value .microlight,
        .swagger-ui pre.microlight,
        .swagger-ui pre.example,
        .swagger-ui pre.example code {
            background: transparent !important;
            color: #94a3b8 !important;
        }

        .swagger-ui code.language-json {
            background: transparent !important;
            color: #cbd5e1 !important;
        }

        /* Code block spans */
        .swagger-ui .highlight-code .microlight span,
        .swagger-ui .example .microlight span,
        .swagger-ui .example-value .microlight span,
        .swagger-ui pre.microlight span,
        .swagger-ui pre.example span,
        .swagger-ui pre.example code span,
        .swagger-ui code.language-json span,
        .swagger-ui .highlight-code span,
        .swagger-ui pre.example *,
        .swagger-ui code.language-json * {
            background: transparent !important;
            background-color: transparent !important;
        }

        /* Span text colors */
        .swagger-ui .highlight-code .microlight span,
        .swagger-ui .example .microlight span,
        .swagger-ui .example-value .microlight span,
        .swagger-ui pre.microlight span,
        .swagger-ui pre.example span,
        .swagger-ui pre.example code span {
            color: #94a3b8 !important;
        }

        .swagger-ui code.language-json span {
            color: #cbd5e1 !important;
        }

        /* Property names */
        .swagger-ui .highlight-code .microlight .hljs-attr,
        .swagger-ui .example .microlight .hljs-attr,
        .swagger-ui pre.example .hljs-attr,
        .swagger-ui code.language-json .hljs-attr {
            background: transparent !important;
            color: #10b981 !important;
        }

        /* String values */
        .swagger-ui .highlight-code .microlight .hljs-string,
        .swagger-ui .example .microlight .hljs-string,
        .swagger-ui pre.example .hljs-string,
        .swagger-ui code.language-json .hljs-string {
            background: transparent !important;
            color: #10b981 !important;
        }

        /* Numbers */
        .swagger-ui .highlight-code .microlight .hljs-number,
        .swagger-ui .example .microlight .hljs-number,
        .swagger-ui pre.example .hljs-number,
        .swagger-ui code.language-json .hljs-number {
            background: transparent !important;
            color: #3b82f6 !important;
        }

        /* Booleans and literals */
        .swagger-ui .highlight-code .microlight .hljs-literal,
        .swagger-ui .example .microlight .hljs-literal,
        .swagger-ui pre.example .hljs-literal,
        .swagger-ui code.language-json .hljs-literal {
            background: transparent !important;
            color: #3b82f6 !important;
        }

        /* Response body content */
        .swagger-ui .response-col_description__inner pre,
        .swagger-ui .response-col_description__inner code,
        .swagger-ui .model pre,
        .swagger-ui .model code {
            background: var(--code-bg) !important;
            color: var(--text-primary) !important;
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
            font-size: 16px !important;
            font-weight: 700 !important;
            padding: 16px 20px !important;
            background: var(--bg-elevated) !important;
            border-bottom: 1px solid var(--border-color) !important;
            margin: 0 !important;
        }

        .swagger-ui section.models h4 svg {
            fill: var(--primary) !important;
        }

        .swagger-ui section.models .models-control {
            background: var(--bg-card) !important;
            padding: 8px !important;
        }

        /* Schema items as inline list */
        .swagger-ui .model-container {
            background: transparent !important;
            border-radius: 0 !important;
            margin: 0 !important;
            border: none !important;
            border-bottom: 1px solid var(--border-subtle) !important;
        }

        .swagger-ui .model-container:last-child {
            border-bottom: none !important;
        }

        .swagger-ui .model-box {
            background: transparent !important;
            padding: 0 !important;
        }

        .swagger-ui .model {
            color: var(--text-secondary) !important;
            font-family: 'JetBrains Mono', monospace !important;
            font-size: 13px !important;
            line-height: 1.4 !important;
        }

        /* Schema row styling - collapsed view */
        .swagger-ui section.models .model-container {
            padding: 10px 16px !important;
            transition: background 0.15s !important;
            border-bottom: 1px solid var(--border-subtle) !important;
        }

        .swagger-ui section.models .model-container:last-child {
            border-bottom: none !important;
        }

        .swagger-ui section.models .model-container:hover {
            background: var(--bg-elevated) !important;
        }

        /* Schema name - use white */
        .swagger-ui .model-title {
            color: var(--text-primary) !important;
            font-family: 'JetBrains Mono', monospace !important;
            font-weight: 600 !important;
            font-size: 13px !important;
            padding: 0 !important;
            background: transparent !important;
            border: none !important;
            margin: 0 !important;
        }

        .swagger-ui .model-title__text {
            color: var(--text-primary) !important;
            font-weight: 600 !important;
        }

        /* Property types */
        .swagger-ui .prop-type {
            color: var(--primary) !important;
            font-weight: 500 !important;
            font-size: 12px !important;
            background: rgba(var(--primary-rgb), 0.1) !important;
            padding: 2px 6px !important;
            border-radius: 4px !important;
            margin-left: 8px !important;
        }

        .swagger-ui .prop-format {
            color: var(--text-muted) !important;
            font-style: normal !important;
            font-size: 11px !important;
        }

        /* Schema property rows - compact table style */
        .swagger-ui .model .property {
            padding: 0 !important;
            border: none !important;
        }

        .swagger-ui .model-box-control,
        .swagger-ui .model-box-control:first-of-type {
            padding: 0 !important;
            background: transparent !important;
        }

        /* Property name styling */
        .swagger-ui .model .property-name {
            color: var(--text-primary) !important;
            font-weight: 500 !important;
        }

        /* Schema expand/collapse button */
        .swagger-ui .model-toggle {
            background: transparent !important;
            border-radius: 4px !important;
            padding: 2px 6px !important;
            margin-left: 4px !important;
        }

        .swagger-ui .model-toggle::after {
            color: var(--text-muted) !important;
        }

        .swagger-ui .model-toggle:hover::after {
            color: var(--primary) !important;
        }

        /* Braces styling - more subtle */
        .swagger-ui .model .brace-open,
        .swagger-ui .model .brace-close {
            color: var(--text-muted) !important;
            font-weight: 400 !important;
        }

        /* Inner object - compact indentation */
        .swagger-ui .model .inner-object {
            padding-left: 12px !important;
            border-left: 2px solid var(--primary) !important;
            margin-left: 4px !important;
            margin-top: 4px !important;
            margin-bottom: 4px !important;
        }

        /* Expanded model content */
        .swagger-ui section.models .model-box {
            padding: 12px 16px !important;
            background: var(--bg-card) !important;
            border-radius: 0 !important;
            margin: 0 !important;
            border-top: 1px solid var(--border-subtle) !important;
        }

        /* Model table styling for properties */
        .swagger-ui .model table {
            margin: 0 !important;
        }

        .swagger-ui .model table td {
            padding: 6px 8px 6px 0 !important;
            vertical-align: top !important;
        }

        .swagger-ui .model table tr {
            border-bottom: 1px solid var(--border-subtle) !important;
        }

        .swagger-ui .model table tr:last-child {
            border-bottom: none !important;
        }

        /* Example values */
        .swagger-ui .model .model-example {
            background: var(--bg-input) !important;
            border-radius: 4px !important;
            padding: 6px 10px !important;
            margin-top: 4px !important;
        }

        .swagger-ui span.model-example-value,
        .swagger-ui .example {
            color: var(--primary-light) !important;
            font-style: normal !important;
        }

        /* Nullable badge */
        .swagger-ui .model span[style*="color"] {
            color: var(--text-muted) !important;
            font-size: 11px !important;
        }

        /* Required star */
        .swagger-ui .model .star {
            color: var(--danger) !important;
        }

        /* Description text in schema */
        .swagger-ui .model .renderedMarkdown p {
            margin: 4px 0 !important;
            color: var(--text-secondary) !important;
            font-size: 12px !important;
            font-family: 'Inter', sans-serif !important;
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
                <!-- Version Switcher -->
                <div class="version-switcher" id="version-switcher">
                    <button class="version-btn" onclick="toggleVersionDropdown()">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        <span class="version-badge" id="current-version">All</span>
                        <svg class="version-arrow" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="version-menu" id="version-menu">
                        <div class="version-menu-header">Select API Version</div>
                        <div class="version-menu-list" id="version-list">
                            <!-- Versions will be populated by JavaScript -->
                        </div>
                    </div>
                </div>

                <!-- Export Dropdown -->
                <div class="export-dropdown" id="export-dropdown">
                    <button class="export-btn" onclick="toggleExportDropdown()">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        <span>Export</span>
                        <svg class="dropdown-arrow" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="export-menu" id="export-menu">
                        <div class="export-menu-header">Export API Documentation</div>
                        <div class="export-menu-section">
                            <div class="export-menu-section-title">OpenAPI Specification</div>
                            <button class="export-menu-item" onclick="exportOpenApiJson()">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <div class="export-item-content">
                                    <span class="export-item-title">OpenAPI JSON</span>
                                    <span class="export-item-desc">Standard JSON format</span>
                                </div>
                            </button>
                            <button class="export-menu-item" onclick="exportOpenApiYaml()">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <div class="export-item-content">
                                    <span class="export-item-title">OpenAPI YAML</span>
                                    <span class="export-item-desc">Human-readable YAML format</span>
                                </div>
                            </button>
                        </div>
                        <div class="export-menu-section">
                            <div class="export-menu-section-title">API Clients</div>
                            <button class="export-menu-item" onclick="exportPostman()">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                    <circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="2"/>
                                    <path d="M8 12l2 2 4-4" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <div class="export-item-content">
                                    <span class="export-item-title">Postman Collection</span>
                                    <span class="export-item-desc">Import into Postman v2.1</span>
                                </div>
                            </button>
                            <button class="export-menu-item" onclick="exportInsomnia()">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <circle cx="12" cy="12" r="10" stroke-width="2"/>
                                    <path d="M12 6v6l4 2" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                                <div class="export-item-content">
                                    <span class="export-item-title">Insomnia Collection</span>
                                    <span class="export-item-desc">Import into Insomnia v4</span>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
                <button class="ws-btn" id="ws-btn" onclick="openWsModal()">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <span>WebSocket</span>
                </button>
                <button class="theme-toggle" id="theme-toggle" onclick="toggleTheme()" title="Toggle theme">
                    <svg class="icon-sun" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <svg class="icon-moon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>
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

    <!-- WebSocket Modal -->
    <div class="ws-modal" id="ws-modal">
        <div class="ws-modal-content">
            <div class="ws-modal-header">
                <h3>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    WebSocket Tester
                </h3>
                <button class="ws-close-btn" onclick="closeWsModal()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="ws-modal-body">
                <div class="ws-connection-bar">
                    <input type="text" class="ws-url-input" id="ws-url" placeholder="ws://localhost:6001/app/your-key">
                    <button class="ws-connect-btn" id="ws-connect-btn" onclick="toggleWsConnection()">Connect</button>
                </div>
                <div class="ws-status disconnected" id="ws-status">
                    <span class="ws-status-dot"></span>
                    <span id="ws-status-text">Disconnected</span>
                </div>
                <div class="ws-panels">
                    <div class="ws-panel">
                        <div class="ws-panel-header">Send Message</div>
                        <div class="ws-panel-body">
                            <div class="ws-presets">
                                <div class="ws-presets-label">Quick Templates</div>
                                <div class="ws-preset-btns">
                                    <button class="ws-preset-btn" onclick="setWsPreset('subscribe')">Subscribe</button>
                                    <button class="ws-preset-btn" onclick="setWsPreset('unsubscribe')">Unsubscribe</button>
                                    <button class="ws-preset-btn" onclick="setWsPreset('ping')">Ping</button>
                                    <button class="ws-preset-btn" onclick="setWsPreset('event')">Client Event</button>
                                </div>
                            </div>
                            <textarea class="ws-message-input" id="ws-message" placeholder='{"event": "subscribe", "channel": "my-channel"}'></textarea>
                            <button class="ws-send-btn" id="ws-send-btn" onclick="sendWsMessage()" disabled>Send Message</button>
                        </div>
                    </div>
                    <div class="ws-panel">
                        <div class="ws-panel-header">Messages</div>
                        <div class="ws-panel-body">
                            <div class="ws-messages" id="ws-messages"></div>
                        </div>
                    </div>
                </div>
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

        let availableVersions = [];
        let currentVersion = localStorage.getItem('api_docs_version') || null;
        const baseSpecUrl = '{$specUrl}';
        const versionsUrl = baseSpecUrl.replace('/openapi.json', '/versions');

        const themes = ['dark', 'light'];
        let currentTheme = localStorage.getItem('api_docs_theme') || getSystemTheme();

        document.addEventListener('DOMContentLoaded', function() {
            initTheme();
            if (authToken) {
                updateAuthButton(true);
            }
            loadVersions();
        });

        function initTheme() {
            applyTheme(currentTheme);
        }

        function getSystemTheme() {
            return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }

        function applyTheme(theme) {
            document.documentElement.setAttribute('data-theme', theme);
            localStorage.setItem('api_docs_theme', theme);
            currentTheme = theme;

            const toggleBtn = document.getElementById('theme-toggle');
            toggleBtn.setAttribute('data-mode', theme);
            toggleBtn.title = theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode';

            clearMicrolightBackgrounds();
        }

        function clearMicrolightBackgrounds() {
            document.querySelectorAll('.highlight-code .microlight span, .example .microlight span, .example-value .microlight span, pre.microlight span, pre.example span, pre.example code span, code.language-json span').forEach(function(el) {
                el.style.background = 'transparent';
                el.style.backgroundColor = 'transparent';

                const hasClass = el.classList.contains('hljs-attr') ||
                                 el.classList.contains('hljs-string') ||
                                 el.classList.contains('hljs-number') ||
                                 el.classList.contains('hljs-literal');

                if (hasClass) {
                    el.style.removeProperty('color');
                } else {
                    const isInLanguageJson = el.closest('code.language-json');
                    el.style.color = isInLanguageJson ? '#cbd5e1' : '#94a3b8';
                }
            });

            document.querySelectorAll('.highlight-code .microlight, .example .microlight, .example-value .microlight, pre.microlight, pre.example, pre.example code').forEach(function(el) {
                el.style.background = 'transparent';
                el.style.backgroundColor = 'transparent';
                el.style.color = '#94a3b8';
            });

            document.querySelectorAll('code.language-json').forEach(function(el) {
                el.style.background = 'transparent';
                el.style.backgroundColor = 'transparent';
                el.style.color = '#cbd5e1';
            });
        }

        function toggleTheme() {
            const nextTheme = currentTheme === 'dark' ? 'light' : 'dark';
            applyTheme(nextTheme);
        }

        async function loadVersions() {
            try {
                const response = await fetch(versionsUrl);
                const data = await response.json();

                if (data.versions && data.versions.length > 0) {
                    availableVersions = data.versions;
                    renderVersionMenu();
                    document.getElementById('version-switcher').classList.add('visible');

                    if (!currentVersion && data.default) {
                        currentVersion = data.default;
                        localStorage.setItem('api_docs_version', currentVersion);
                    }

                    updateVersionBadge();
                }
            } catch (error) {
                console.log('Versioning not enabled or no versions available');
            }
        }

        function renderVersionMenu() {
            const versionList = document.getElementById('version-list');
            let html = '';

            html += '<button class="version-menu-item ' + (!currentVersion ? 'active' : '') + '" onclick="switchVersion(null)">' +
                '<div class="version-name">' +
                    '<span class="version-title">All Versions</span>' +
                    '<span class="version-desc">Show all API endpoints</span>' +
                '</div>' +
                '<svg class="version-check" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">' +
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />' +
                '</svg>' +
            '</button>';

            for (const version of availableVersions) {
                html += '<button class="version-menu-item ' + (currentVersion === version.name ? 'active' : '') + '" onclick="switchVersion(\'' + version.name + '\')">' +
                    '<div class="version-name">' +
                        '<span class="version-title">' + (version.title || version.name.toUpperCase()) + '</span>' +
                        '<span class="version-desc">' + (version.description || '') + '</span>' +
                    '</div>' +
                    '<svg class="version-check" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">' +
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />' +
                    '</svg>' +
                '</button>';
            }

            versionList.innerHTML = html;
        }

        function toggleVersionDropdown() {
            const dropdown = document.getElementById('version-switcher');
            dropdown.classList.toggle('active');

            document.getElementById('export-dropdown').classList.remove('active');
        }

        function switchVersion(version) {
            currentVersion = version;
            localStorage.setItem('api_docs_version', version || '');

            updateVersionBadge();
            renderVersionMenu();

            document.getElementById('version-switcher').classList.remove('active');

            reloadSwaggerUI();
        }

        function updateVersionBadge() {
            const badge = document.getElementById('current-version');
            badge.textContent = currentVersion ? currentVersion.toUpperCase() : 'All';
        }

        function getSpecUrl() {
            if (currentVersion) {
                return baseSpecUrl.replace('/openapi.json', '/' + currentVersion + '/openapi.json');
            }
            return baseSpecUrl;
        }

        function reloadSwaggerUI() {
            const specUrl = getSpecUrl();

            window.ui = SwaggerUIBundle({
                url: specUrl,
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
                requestInterceptor: (req) => {
                    if (authToken) {
                        if (authType === 'bearer') {
                            req.headers['Authorization'] = 'Bearer ' + authToken;
                        } else {
                            req.headers[apiKeyHeader] = authToken;
                        }
                    }
                    return req;
                },
                onComplete: function() {
                    updateStats();

                    setTimeout(clearMicrolightBackgrounds, 100);

                    const observer = new MutationObserver(function(mutations) {
                        clearMicrolightBackgrounds();
                    });
                    observer.observe(document.getElementById('swagger-ui'), {
                        childList: true,
                        subtree: true
                    });
                }
            });
        }

        document.addEventListener('click', function(e) {
            const versionSwitcher = document.getElementById('version-switcher');
            if (versionSwitcher && !versionSwitcher.contains(e.target)) {
                versionSwitcher.classList.remove('active');
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

            localStorage.setItem('api_auth_token', authToken);
            localStorage.setItem('api_auth_type', authType);
            localStorage.setItem('api_auth_header', apiKeyHeader);

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

            tagSections.forEach(section => {
                const visibleOps = section.querySelectorAll('.opblock:not([style*="display: none"])');
                section.style.display = visibleOps.length > 0 || !query ? '' : 'none';
            });
        }

        function updateStats() {
            const specUrl = getSpecUrl();
            fetch(specUrl)
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

        window.onload = function() {
            const specUrl = getSpecUrl();

            const ui = SwaggerUIBundle({
                url: specUrl,
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
                    updateStats();

                    if (authToken) {
                        setTimeout(() => {
                            if (authType === 'bearer') {
                                window.ui.preauthorizeApiKey('bearerAuth', authToken);
                            }
                        }, 500);
                    }

                    setTimeout(clearMicrolightBackgrounds, 100);

                    const observer = new MutationObserver(function(mutations) {
                        clearMicrolightBackgrounds();
                    });
                    observer.observe(document.getElementById('swagger-ui'), {
                        childList: true,
                        subtree: true
                    });
                }
            });
            window.ui = ui;
        };

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAuthModal();
            }
        });

        document.getElementById('auth-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeAuthModal();
            }
        });


        let cachedSpec = null;
        let cachedSpecVersion = null;

        function toggleExportDropdown() {
            const dropdown = document.getElementById('export-dropdown');
            dropdown.classList.toggle('active');

            document.getElementById('version-switcher').classList.remove('active');
        }

        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('export-dropdown');
            if (!dropdown.contains(e.target)) {
                dropdown.classList.remove('active');
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.getElementById('export-dropdown').classList.remove('active');
            }
        });

        async function getOpenApiSpec() {
            if (cachedSpecVersion !== currentVersion) {
                cachedSpec = null;
            }

            if (cachedSpec) return cachedSpec;

            try {
                const specUrl = getSpecUrl();
                const response = await fetch(specUrl);
                cachedSpec = await response.json();
                cachedSpecVersion = currentVersion;
                return cachedSpec;
            } catch (error) {
                showExportToast('error', 'Failed to fetch API specification');
                throw error;
            }
        }

        function downloadFile(content, filename, mimeType) {
            const blob = new Blob([content], { type: mimeType });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }

        function showExportToast(type, title, description = '') {
            const existingToast = document.querySelector('.export-toast');
            if (existingToast) existingToast.remove();

            const toast = document.createElement('div');
            toast.className = 'export-toast ' + type;
            toast.innerHTML = type === 'success'
                ? '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>'
                : '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>';
            toast.innerHTML += '<div class="export-toast-content"><span class="export-toast-title">' + title + '</span>' +
                (description ? '<span class="export-toast-desc">' + description + '</span>' : '') + '</div>';

            document.body.appendChild(toast);
            setTimeout(() => toast.classList.add('show'), 10);
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        async function exportOpenApiJson() {
            try {
                const spec = await getOpenApiSpec();
                const json = JSON.stringify(spec, null, 2);
                downloadFile(json, 'openapi-spec.json', 'application/json');
                showExportToast('success', 'OpenAPI JSON exported', 'openapi-spec.json');
                document.getElementById('export-dropdown').classList.remove('active');
            } catch (error) {
                console.error('Export failed:', error);
            }
        }

        async function exportOpenApiYaml() {
            try {
                const spec = await getOpenApiSpec();
                const yaml = jsonToYaml(spec);
                downloadFile(yaml, 'openapi-spec.yaml', 'text/yaml');
                showExportToast('success', 'OpenAPI YAML exported', 'openapi-spec.yaml');
                document.getElementById('export-dropdown').classList.remove('active');
            } catch (error) {
                console.error('Export failed:', error);
            }
        }

        function jsonToYaml(obj, indent = 0) {
            const spaces = '  '.repeat(indent);
            let yaml = '';

            if (Array.isArray(obj)) {
                if (obj.length === 0) return '[]';
                for (const item of obj) {
                    if (typeof item === 'object' && item !== null) {
                        yaml += spaces + '-\\n' + jsonToYaml(item, indent + 1).split('\\n').map((line, i) => i === 0 ? spaces + '  ' + line.trim() : line).join('\\n') + '\\n';
                    } else {
                        yaml += spaces + '- ' + formatYamlValue(item) + '\\n';
                    }
                }
            } else if (typeof obj === 'object' && obj !== null) {
                for (const [key, value] of Object.entries(obj)) {
                    if (value === null || value === undefined) {
                        yaml += spaces + key + ': null\\n';
                    } else if (Array.isArray(value)) {
                        if (value.length === 0) {
                            yaml += spaces + key + ': []\\n';
                        } else {
                            yaml += spaces + key + ':\\n' + jsonToYaml(value, indent + 1);
                        }
                    } else if (typeof value === 'object') {
                        if (Object.keys(value).length === 0) {
                            yaml += spaces + key + ': {}\\n';
                        } else {
                            yaml += spaces + key + ':\\n' + jsonToYaml(value, indent + 1);
                        }
                    } else {
                        yaml += spaces + key + ': ' + formatYamlValue(value) + '\\n';
                    }
                }
            }
            return yaml;
        }

        function formatYamlValue(value) {
            if (typeof value === 'string') {
                if (value.includes('\\n') || value.includes(':') || value.includes('#') ||
                    value.includes('"') || value.includes("'") || value.match(/^[\\[\\]{}&*!|>%@]/) ||
                    value === '' || value === 'true' || value === 'false' || value === 'null' ||
                    !isNaN(value)) {
                    return '"' + value.replace(/\\\\/g, '\\\\\\\\').replace(/"/g, '\\\\"').replace(/\\n/g, '\\\\n') + '"';
                }
                return value;
            }
            if (typeof value === 'boolean') return value ? 'true' : 'false';
            if (typeof value === 'number') return String(value);
            return String(value);
        }

        async function exportPostman() {
            try {
                const spec = await getOpenApiSpec();
                const collection = convertToPostman(spec);
                const json = JSON.stringify(collection, null, 2);
                downloadFile(json, 'postman-collection.json', 'application/json');
                showExportToast('success', 'Postman Collection exported', 'postman-collection.json');
                document.getElementById('export-dropdown').classList.remove('active');
            } catch (error) {
                console.error('Export failed:', error);
            }
        }

        function convertToPostman(spec) {
            const collection = {
                info: {
                    name: spec.info?.title || 'API Collection',
                    description: spec.info?.description || '',
                    schema: 'https://schema.getpostman.com/json/collection/v2.1.0/collection.json',
                    version: spec.info?.version || '1.0.0'
                },
                item: [],
                variable: []
            };

            if (spec.servers && spec.servers.length > 0) {
                collection.variable.push({
                    key: 'baseUrl',
                    value: spec.servers[0].url,
                    type: 'string'
                });
            }

            const taggedItems = {};
            const untaggedItems = [];

            if (spec.paths) {
                for (const [path, methods] of Object.entries(spec.paths)) {
                    for (const [method, operation] of Object.entries(methods)) {
                        if (!['get', 'post', 'put', 'patch', 'delete', 'options', 'head'].includes(method)) continue;

                        const item = createPostmanItem(path, method, operation, spec);
                        const tags = operation.tags || ['Untagged'];

                        for (const tag of tags) {
                            if (!taggedItems[tag]) taggedItems[tag] = [];
                            taggedItems[tag].push(item);
                        }
                    }
                }
            }

            for (const [tag, items] of Object.entries(taggedItems)) {
                collection.item.push({
                    name: tag,
                    item: items
                });
            }

            return collection;
        }

        function createPostmanItem(path, method, operation, spec) {
            const item = {
                name: operation.summary || path,
                request: {
                    method: method.toUpperCase(),
                    header: [],
                    url: {
                        raw: '{{baseUrl}}' + path,
                        host: ['{{baseUrl}}'],
                        path: path.split('/').filter(p => p)
                    }
                },
                response: []
            };

            if (operation.description) {
                item.request.description = operation.description;
            }

            if (operation.parameters) {
                const queryParams = [];
                const pathVariables = [];

                for (const param of operation.parameters) {
                    if (param.in === 'query') {
                        queryParams.push({
                            key: param.name,
                            value: param.example || '',
                            description: param.description || '',
                            disabled: !param.required
                        });
                    } else if (param.in === 'path') {
                        pathVariables.push({
                            key: param.name,
                            value: param.example || ':' + param.name,
                            description: param.description || ''
                        });
                    } else if (param.in === 'header') {
                        item.request.header.push({
                            key: param.name,
                            value: param.example || '',
                            description: param.description || '',
                            disabled: !param.required
                        });
                    }
                }

                if (queryParams.length > 0) {
                    item.request.url.query = queryParams;
                }
                if (pathVariables.length > 0) {
                    item.request.url.variable = pathVariables;
                }
            }

            if (operation.requestBody) {
                const content = operation.requestBody.content;
                if (content && content['application/json']) {
                    item.request.header.push({
                        key: 'Content-Type',
                        value: 'application/json'
                    });
                    item.request.body = {
                        mode: 'raw',
                        raw: JSON.stringify(getSchemaExample(content['application/json'].schema, spec), null, 2),
                        options: {
                            raw: { language: 'json' }
                        }
                    };
                }
            }

            return item;
        }

        function getSchemaExample(schema, spec, visited = new Set()) {
            if (!schema) return {};

            if (schema['\$ref']) {
                const refPath = schema['\$ref'].replace('#/components/schemas/', '');
                if (visited.has(refPath)) return {};
                visited.add(refPath);
                const refSchema = spec.components?.schemas?.[refPath];
                if (refSchema) return getSchemaExample(refSchema, spec, visited);
                return {};
            }

            if (schema.example !== undefined) return schema.example;

            switch (schema.type) {
                case 'object':
                    const obj = {};
                    if (schema.properties) {
                        for (const [key, prop] of Object.entries(schema.properties)) {
                            obj[key] = getSchemaExample(prop, spec, visited);
                        }
                    }
                    return obj;
                case 'array':
                    return [getSchemaExample(schema.items, spec, visited)];
                case 'string':
                    if (schema.enum) return schema.enum[0];
                    if (schema.format === 'email') return 'user@example.com';
                    if (schema.format === 'date') return '2024-01-01';
                    if (schema.format === 'date-time') return '2024-01-01T00:00:00Z';
                    if (schema.format === 'uuid') return '550e8400-e29b-41d4-a716-446655440000';
                    return 'string';
                case 'integer':
                case 'number':
                    return schema.minimum || 0;
                case 'boolean':
                    return true;
                default:
                    return null;
            }
        }

        async function exportInsomnia() {
            try {
                const spec = await getOpenApiSpec();
                const collection = convertToInsomnia(spec);
                const json = JSON.stringify(collection, null, 2);
                downloadFile(json, 'insomnia-collection.json', 'application/json');
                showExportToast('success', 'Insomnia Collection exported', 'insomnia-collection.json');
                document.getElementById('export-dropdown').classList.remove('active');
            } catch (error) {
                console.error('Export failed:', error);
            }
        }

        function convertToInsomnia(spec) {
            const workspaceId = 'wrk_' + generateId();
            const baseEnvId = 'env_' + generateId();

            const resources = [
                {
                    _id: workspaceId,
                    _type: 'workspace',
                    name: spec.info?.title || 'API Workspace',
                    description: spec.info?.description || '',
                    scope: 'collection'
                },
                {
                    _id: baseEnvId,
                    _type: 'environment',
                    parentId: workspaceId,
                    name: 'Base Environment',
                    data: {
                        base_url: spec.servers?.[0]?.url || 'http://localhost'
                    }
                }
            ];

            const tagFolders = {};
            if (spec.tags) {
                for (const tag of spec.tags) {
                    const folderId = 'fld_' + generateId();
                    tagFolders[tag.name] = folderId;
                    resources.push({
                        _id: folderId,
                        _type: 'request_group',
                        parentId: workspaceId,
                        name: tag.name,
                        description: tag.description || ''
                    });
                }
            }

            if (spec.paths) {
                for (const [path, methods] of Object.entries(spec.paths)) {
                    for (const [method, operation] of Object.entries(methods)) {
                        if (!['get', 'post', 'put', 'patch', 'delete', 'options', 'head'].includes(method)) continue;

                        const tag = operation.tags?.[0] || 'Untagged';
                        let parentId = tagFolders[tag];

                        if (!parentId) {
                            const folderId = 'fld_' + generateId();
                            tagFolders[tag] = folderId;
                            resources.push({
                                _id: folderId,
                                _type: 'request_group',
                                parentId: workspaceId,
                                name: tag
                            });
                            parentId = folderId;
                        }

                        const request = createInsomniaRequest(path, method, operation, spec, parentId);
                        resources.push(request);
                    }
                }
            }

            return {
                _type: 'export',
                __export_format: 4,
                __export_date: new Date().toISOString(),
                __export_source: 'laravel-api-response-builder',
                resources: resources
            };
        }

        function createInsomniaRequest(path, method, operation, spec, parentId) {
            const request = {
                _id: 'req_' + generateId(),
                _type: 'request',
                parentId: parentId,
                name: operation.summary || path,
                description: operation.description || '',
                method: method.toUpperCase(),
                url: '{{ _.base_url }}' + path,
                headers: [],
                parameters: [],
                body: {}
            };

            if (operation.parameters) {
                for (const param of operation.parameters) {
                    if (param.in === 'query') {
                        request.parameters.push({
                            name: param.name,
                            value: param.example || '',
                            description: param.description || '',
                            disabled: !param.required
                        });
                    } else if (param.in === 'header') {
                        request.headers.push({
                            name: param.name,
                            value: param.example || '',
                            description: param.description || '',
                            disabled: !param.required
                        });
                    }
                }
            }

            if (operation.requestBody) {
                const content = operation.requestBody.content;
                if (content && content['application/json']) {
                    request.headers.push({
                        name: 'Content-Type',
                        value: 'application/json'
                    });
                    request.body = {
                        mimeType: 'application/json',
                        text: JSON.stringify(getSchemaExample(content['application/json'].schema, spec), null, 2)
                    };
                }
            }

            return request;
        }

        function generateId() {
            return Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
        }


        let wsConnection = null;
        let wsConnected = false;
        const wsEndpointsUrl = baseSpecUrl.replace('/openapi.json', '/websockets');

        const wsPresets = {
            subscribe: {
                event: 'pusher:subscribe',
                data: {
                    channel: 'my-channel'
                }
            },
            unsubscribe: {
                event: 'pusher:unsubscribe',
                data: {
                    channel: 'my-channel'
                }
            },
            ping: {
                event: 'pusher:ping',
                data: {}
            },
            event: {
                event: 'client-message',
                data: {
                    message: 'Hello from client!'
                }
            }
        };

        function openWsModal() {
            document.getElementById('ws-modal').classList.add('active');
            loadWsConfig();
        }

        function closeWsModal() {
            document.getElementById('ws-modal').classList.remove('active');
        }

        async function loadWsConfig() {
            try {
                const response = await fetch(wsEndpointsUrl);
                const data = await response.json();

                if (data.url) {
                    const savedUrl = localStorage.getItem('ws_url');
                    document.getElementById('ws-url').value = savedUrl || data.url;
                }
            } catch (error) {
                console.log('WebSocket config not available');
            }
        }

        function toggleWsConnection() {
            if (wsConnected) {
                disconnectWs();
            } else {
                connectWs();
            }
        }

        function connectWs() {
            const url = document.getElementById('ws-url').value.trim();

            if (!url) {
                addWsMessage('error', 'Please enter a WebSocket URL');
                return;
            }

            localStorage.setItem('ws_url', url);
            updateWsStatus('connecting');

            try {
                wsConnection = new WebSocket(url);

                wsConnection.onopen = function() {
                    wsConnected = true;
                    updateWsStatus('connected');
                    addWsMessage('system', 'Connected to ' + url);
                    document.getElementById('ws-send-btn').disabled = false;
                    document.getElementById('ws-btn').classList.add('connected');
                };

                wsConnection.onmessage = function(event) {
                    let data = event.data;
                    try {
                        data = JSON.stringify(JSON.parse(event.data), null, 2);
                    } catch (e) {}
                    addWsMessage('received', data);
                };

                wsConnection.onerror = function(error) {
                    addWsMessage('error', 'Connection error');
                };

                wsConnection.onclose = function(event) {
                    wsConnected = false;
                    updateWsStatus('disconnected');
                    addWsMessage('system', 'Disconnected (code: ' + event.code + ')');
                    document.getElementById('ws-send-btn').disabled = true;
                    document.getElementById('ws-btn').classList.remove('connected');
                };
            } catch (error) {
                addWsMessage('error', 'Failed to connect: ' + error.message);
                updateWsStatus('disconnected');
            }
        }

        function disconnectWs() {
            if (wsConnection) {
                wsConnection.close();
                wsConnection = null;
            }
        }

        function updateWsStatus(status) {
            const statusEl = document.getElementById('ws-status');
            const statusText = document.getElementById('ws-status-text');
            const connectBtn = document.getElementById('ws-connect-btn');

            statusEl.className = 'ws-status ' + status;

            switch (status) {
                case 'connected':
                    statusText.textContent = 'Connected';
                    connectBtn.textContent = 'Disconnect';
                    connectBtn.classList.add('disconnect');
                    break;
                case 'connecting':
                    statusText.textContent = 'Connecting...';
                    connectBtn.textContent = 'Connecting...';
                    break;
                default:
                    statusText.textContent = 'Disconnected';
                    connectBtn.textContent = 'Connect';
                    connectBtn.classList.remove('disconnect');
            }
        }

        function sendWsMessage() {
            if (!wsConnected || !wsConnection) {
                addWsMessage('error', 'Not connected');
                return;
            }

            const message = document.getElementById('ws-message').value.trim();

            if (!message) {
                addWsMessage('error', 'Please enter a message');
                return;
            }

            try {
                wsConnection.send(message);
                addWsMessage('sent', message);
            } catch (error) {
                addWsMessage('error', 'Failed to send: ' + error.message);
            }
        }

        function addWsMessage(type, content) {
            const messagesEl = document.getElementById('ws-messages');
            const time = new Date().toLocaleTimeString();

            const messageEl = document.createElement('div');
            messageEl.className = 'ws-message ' + type;
            messageEl.innerHTML = '<div class="ws-message-time">' + time + ' - ' + type.toUpperCase() + '</div>' +
                '<div class="ws-message-content">' + escapeHtml(content) + '</div>';

            messagesEl.appendChild(messageEl);
            messagesEl.scrollTop = messagesEl.scrollHeight;
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function setWsPreset(preset) {
            if (wsPresets[preset]) {
                document.getElementById('ws-message').value = JSON.stringify(wsPresets[preset], null, 2);
            }
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeWsModal();
            }
        });

        document.getElementById('ws-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeWsModal();
            }
        });
    </script>
</body>
</html>
HTML;
    }

    /**
     * Convert hex color to RGB array.
     */
    protected function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        return [
            'r' => hexdec(substr($hex, 0, 2)),
            'g' => hexdec(substr($hex, 2, 2)),
            'b' => hexdec(substr($hex, 4, 2)),
        ];
    }

    /**
     * Lighten a color by a given amount.
     */
    protected function lightenColor(array $rgb, float $amount): array
    {
        return [
            'r' => min(255, (int) ($rgb['r'] + (255 - $rgb['r']) * $amount)),
            'g' => min(255, (int) ($rgb['g'] + (255 - $rgb['g']) * $amount)),
            'b' => min(255, (int) ($rgb['b'] + (255 - $rgb['b']) * $amount)),
        ];
    }

    protected function darkenColor(array $rgb, float $amount): array
    {
        return [
            'r' => max(0, (int) ($rgb['r'] * (1 - $amount))),
            'g' => max(0, (int) ($rgb['g'] * (1 - $amount))),
            'b' => max(0, (int) ($rgb['b'] * (1 - $amount))),
        ];
    }
}
