<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

use function Laravel\Prompts\confirm;

class SyncBunny extends Command
{
    protected $signature = 'sync:bunny {--templates} {--release} {--github-releases} {--github-versions} {--nightly}';

    protected $description = '';

    private string $artifactsRepository = 'loccen/coolify-zh-artifacts';

    private string $sourceRepository = 'loccen/coolify-zh';

    public function __construct()
    {
        parent::__construct();

        $this->setDescription(trans('console.sync_bunny.description', locale: app()->getLocale()));
    }

    public function handle(): int
    {
        $onlyTemplates = (bool) $this->option('templates');
        $releaseBundleOnly = (bool) $this->option('release');
        $releasesOnly = (bool) $this->option('github-releases');
        $versionsOnly = (bool) $this->option('github-versions');
        $nightly = (bool) $this->option('nightly');

        $tmpDir = $this->cloneArtifactsRepository();
        if ($tmpDir === null) {
            return self::FAILURE;
        }

        try {
            $this->touchNoJekyll($tmpDir);

            if ($onlyTemplates) {
                return $this->syncTemplates($tmpDir);
            }

            if ($releaseBundleOnly) {
                return $this->syncReleaseBundle($tmpDir, $nightly);
            }

            if ($releasesOnly) {
                return $this->syncReleases($tmpDir);
            }

            if ($versionsOnly) {
                return $this->syncVersions($tmpDir, $nightly);
            }

            return $this->syncArtifacts($tmpDir, $nightly);
        } finally {
            exec('rm -rf '.escapeshellarg($tmpDir));
        }
    }

    private function syncArtifacts(string $tmpDir, bool $nightly): int
    {
        $targetDirectory = $nightly ? 'coolify-nightly' : 'coolify';
        $files = $nightly ? $this->nightlyArtifactFiles() : $this->productionArtifactFiles();

        $this->info(trans('console.sync_bunny.info.preparing_artifacts', [
            'kind' => trans('console.sync_bunny.kinds.'.($nightly ? 'nightly' : 'production'), locale: app()->getLocale()),
            'repository' => $this->artifactsRepository,
        ], locale: app()->getLocale()));

        if (! confirm(trans('console.sync_bunny.confirm.sync_artifacts', locale: app()->getLocale()), default: true)) {
            return self::SUCCESS;
        }

        $this->copyFiles($tmpDir, $files, $targetDirectory);

        if (! $nightly) {
            $this->writeReleasesJson($tmpDir);
        }

        return $this->commitAndPush($tmpDir, 'chore: 同步 '.($nightly ? 'nightly' : 'production').' artifacts');
    }

    private function syncTemplates(string $tmpDir): int
    {
        $this->info(trans('console.sync_bunny.info.preparing_service_template_artifact', locale: app()->getLocale()));

        if (! confirm(trans('console.sync_bunny.confirm.sync_service_template', locale: app()->getLocale()), default: true)) {
            return self::SUCCESS;
        }

        $this->copyFiles($tmpDir, [
            base_path('templates/service-templates-latest.json') => 'service-templates-latest.json',
        ], 'coolify');

        return $this->commitAndPush($tmpDir, 'chore: 同步 service templates');
    }

    private function syncReleaseBundle(string $tmpDir, bool $nightly): int
    {
        $this->info(trans('console.sync_bunny.info.preparing_versions_and_releases_artifacts', locale: app()->getLocale()));

        if (! confirm(trans('console.sync_bunny.confirm.sync_versions_and_releases', locale: app()->getLocale()), default: true)) {
            return self::SUCCESS;
        }

        $this->writeVersionsJson($tmpDir, $nightly);
        $this->writeReleasesJson($tmpDir);

        return $this->commitAndPush($tmpDir, 'chore: 同步 release metadata');
    }

    private function syncReleases(string $tmpDir): int
    {
        $this->info(trans('console.sync_bunny.info.preparing_releases_artifact', locale: app()->getLocale()));

        if (! confirm(trans('console.sync_bunny.confirm.sync_releases_json', locale: app()->getLocale()), default: true)) {
            return self::SUCCESS;
        }

        $this->writeReleasesJson($tmpDir);

        return $this->commitAndPush($tmpDir, 'chore: 同步 releases metadata');
    }

    private function syncVersions(string $tmpDir, bool $nightly): int
    {
        $this->info(trans('console.sync_bunny.info.preparing_versions_artifact', locale: app()->getLocale()));

        if (! confirm(trans('console.sync_bunny.confirm.sync_versions_json', locale: app()->getLocale()), default: true)) {
            return self::SUCCESS;
        }

        $this->writeVersionsJson($tmpDir, $nightly);

        return $this->commitAndPush($tmpDir, 'chore: 同步 versions metadata');
    }

    private function cloneArtifactsRepository(): ?string
    {
        $tmpDir = sys_get_temp_dir().'/coolify-zh-artifacts-'.time();
        $output = [];

        exec('gh repo clone '.$this->artifactsRepository.' '.escapeshellarg($tmpDir).' -- --depth 1 2>&1', $output, $returnCode);

        if ($returnCode !== 0) {
            $this->error('克隆 artifacts 仓库失败: '.implode("\n", $output));

            return null;
        }

        return $tmpDir;
    }

    private function productionArtifactFiles(): array
    {
        return [
            base_path('docker-compose.yml') => 'docker-compose.yml',
            base_path('docker-compose.prod.yml') => 'docker-compose.prod.yml',
            base_path('.env.production') => '.env.production',
            base_path('scripts/install.sh') => 'install.sh',
            base_path('scripts/upgrade.sh') => 'upgrade.sh',
            base_path('versions.json') => 'versions.json',
            base_path('templates/service-templates-latest.json') => 'service-templates-latest.json',
        ];
    }

    private function nightlyArtifactFiles(): array
    {
        return [
            base_path('other/nightly/docker-compose.yml') => 'docker-compose.yml',
            base_path('other/nightly/docker-compose.prod.yml') => 'docker-compose.prod.yml',
            base_path('other/nightly/.env.production') => '.env.production',
            base_path('other/nightly/install.sh') => 'install.sh',
            base_path('other/nightly/upgrade.sh') => 'upgrade.sh',
            base_path('other/nightly/versions.json') => 'versions.json',
        ];
    }

    private function copyFiles(string $tmpDir, array $files, string $targetDirectory): void
    {
        $destinationRoot = $tmpDir.'/'.$targetDirectory;
        File::ensureDirectoryExists($destinationRoot);

        foreach ($files as $source => $relativeTarget) {
            if (! File::exists($source)) {
                throw new \RuntimeException("Missing source artifact: {$source}");
            }

            $destination = $destinationRoot.'/'.$relativeTarget;
            File::ensureDirectoryExists(dirname($destination));
            File::copy($source, $destination);
        }
    }

    private function writeVersionsJson(string $tmpDir, bool $nightly): void
    {
        $source = $nightly ? base_path('other/nightly/versions.json') : base_path('versions.json');
        $targetDirectory = $nightly ? 'coolify-nightly' : 'coolify';

        if (! File::exists($source)) {
            throw new \RuntimeException("Missing versions.json: {$source}");
        }

        File::ensureDirectoryExists($tmpDir.'/'.$targetDirectory);
        File::copy($source, $tmpDir.'/'.$targetDirectory.'/versions.json');
    }

    private function writeReleasesJson(string $tmpDir): void
    {
        $response = Http::timeout(30)->get("https://api.github.com/repos/{$this->sourceRepository}/releases", [
            'per_page' => 30,
        ]);

        if (! $response->successful()) {
            throw new \RuntimeException('Failed to fetch releases from GitHub: '.$response->status());
        }

        File::ensureDirectoryExists($tmpDir.'/json');
        File::put(
            $tmpDir.'/json/releases.json',
            json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
    }

    private function touchNoJekyll(string $tmpDir): void
    {
        File::put($tmpDir.'/.nojekyll', '');
    }

    private function commitAndPush(string $tmpDir, string $message): int
    {
        exec('cd '.escapeshellarg($tmpDir).' && git status --short 2>&1', $statusOutput, $statusCode);
        if ($statusCode !== 0) {
            $this->error('读取 artifacts 仓库状态失败: '.implode("\n", $statusOutput));

            return self::FAILURE;
        }

        if (! array_filter($statusOutput)) {
            $this->info(trans('console.sync_bunny.info.no_artifact_changes_detected', locale: app()->getLocale()));

            return self::SUCCESS;
        }

        $commands = [
            'git config user.name '.escapeshellarg('github-actions[bot]'),
            'git config user.email '.escapeshellarg('github-actions[bot]@users.noreply.github.com'),
            'git add .',
            'git commit -m '.escapeshellarg($message),
            'git push origin HEAD:main',
        ];

        $output = [];
        exec('cd '.escapeshellarg($tmpDir).' && '.implode(' && ', $commands).' 2>&1', $output, $returnCode);

        if ($returnCode !== 0) {
            $this->error('推送 artifacts 仓库失败: '.implode("\n", $output));

            return self::FAILURE;
        }

        $this->info(trans('console.sync_bunny.info.artifacts_pushed_successfully', [
            'repository' => $this->artifactsRepository,
        ], locale: app()->getLocale()));

        return self::SUCCESS;
    }
}
