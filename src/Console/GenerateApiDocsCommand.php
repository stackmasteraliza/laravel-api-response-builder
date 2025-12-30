<?php

namespace Stackmasteraliza\ApiResponse\Console;

use Illuminate\Console\Command;
use Stackmasteraliza\ApiResponse\OpenApi\OpenApiGenerator;

class GenerateApiDocsCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'api:docs
                            {--output= : Output file path (default: public/api-docs/openapi.json)}
                            {--format=json : Output format (json or yaml)}';

    /**
     * The console command description.
     */
    protected $description = 'Generate OpenAPI/Swagger documentation from your API routes';

    /**
     * Execute the console command.
     */
    public function handle(OpenApiGenerator $generator): int
    {
        $this->info('Generating API documentation...');

        $outputPath = $this->option('output')
            ?? public_path('api-docs/openapi.json');

        // Ensure directory exists
        $directory = dirname($outputPath);
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $spec = $generator->generate();

        $format = $this->option('format');

        if ($format === 'yaml') {
            if (! function_exists('yaml_emit')) {
                $this->error('YAML extension not installed. Using JSON format instead.');
                $format = 'json';
            }
        }

        if ($format === 'yaml') {
            $content = yaml_emit($spec, YAML_UTF8_ENCODING);
            $outputPath = preg_replace('/\.json$/', '.yaml', $outputPath);
        } else {
            $content = json_encode($spec, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        }

        file_put_contents($outputPath, $content);

        $routeCount = count($spec['paths']);
        $this->info("Documentation generated successfully!");
        $this->info("  - Routes documented: {$routeCount}");
        $this->info("  - Output: {$outputPath}");
        $this->newLine();
        $this->info("View your docs at: " . url('/api-docs'));

        return Command::SUCCESS;
    }
}
