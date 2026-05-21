<?php

it('uses fork-owned release infrastructure defaults', function () {
    expect(config('constants.coolify.image_namespace'))->toBe('loccen');
    expect(config('constants.coolify.app_image'))->toBe('ghcr.io/loccen/coolify');
    expect(config('constants.coolify.helper_image'))->toBe('ghcr.io/loccen/coolify-helper');
    expect(config('constants.coolify.realtime_image'))->toBe('ghcr.io/loccen/coolify-realtime');
    expect(config('constants.coolify.testing_host_image'))->toBe('ghcr.io/loccen/coolify-testing-host');
    expect(config('constants.coolify.artifact_base_url'))->toBe('https://loccen.github.io/coolify-zh-artifacts/coolify');
    expect(config('constants.coolify.nightly_artifact_base_url'))->toBe('https://loccen.github.io/coolify-zh-artifacts/coolify-nightly');
    expect(config('constants.coolify.releases_url'))->toBe('https://loccen.github.io/coolify-zh-artifacts/json/releases.json');
    expect(config('constants.services.official'))->toBe('https://loccen.github.io/coolify-zh-artifacts/coolify/service-templates-latest.json');
});

it('keeps production version metadata in sync', function () {
    $versions = json_decode(file_get_contents(base_path('versions.json')), true, flags: JSON_THROW_ON_ERROR);

    expect(data_get($versions, 'coolify.v4.version'))->toBe(config('constants.coolify.version'));
    expect(data_get($versions, 'coolify.helper.version'))->toBe(config('constants.coolify.helper_version'));
    expect(data_get($versions, 'coolify.realtime.version'))->toBe(config('constants.coolify.realtime_version'));
});

it('points install and upgrade scripts at the artifact repository', function () {
    $productionInstall = file_get_contents(base_path('scripts/install.sh'));
    $nightlyInstall = file_get_contents(base_path('other/nightly/install.sh'));
    $productionUpgrade = file_get_contents(base_path('scripts/upgrade.sh'));
    $nightlyUpgrade = file_get_contents(base_path('other/nightly/upgrade.sh'));

    expect($productionInstall)->toContain('DEFAULT_ARTIFACT_BASE_URL="https://loccen.github.io/coolify-zh-artifacts/coolify"');
    expect($nightlyInstall)->toContain('DEFAULT_ARTIFACT_BASE_URL="https://loccen.github.io/coolify-zh-artifacts/coolify-nightly"');
    expect($productionUpgrade)->toContain('DEFAULT_ARTIFACT_BASE_URL="https://loccen.github.io/coolify-zh-artifacts/coolify"');
    expect($nightlyUpgrade)->toContain('DEFAULT_ARTIFACT_BASE_URL="https://loccen.github.io/coolify-zh-artifacts/coolify-nightly"');

    expect($productionInstall)->toContain('$ARTIFACT_BASE_URL/upgrade.sh');
    expect($nightlyInstall)->toContain('$ARTIFACT_BASE_URL/upgrade.sh');
    expect($productionUpgrade)->toContain('${IMAGE_NAMESPACE}/coolify-helper:${LATEST_HELPER_VERSION}');
    expect($nightlyUpgrade)->toContain('${IMAGE_NAMESPACE}/coolify-helper:${LATEST_HELPER_VERSION}');
    expect($productionUpgrade)->toContain('$ARTIFACT_BASE_URL/restore-coolify-instance.sh');
    expect($nightlyUpgrade)->toContain('$ARTIFACT_BASE_URL/restore-coolify-instance.sh');
    expect($productionUpgrade)->toContain('/data/coolify/bin/restore-coolify-instance.sh');
    expect($nightlyUpgrade)->toContain('/data/coolify/bin/restore-coolify-instance.sh');
});

it('publishes artifacts to the dedicated pages repository', function () {
    $productionWorkflow = file_get_contents(base_path('.github/workflows/publish-production-artifacts.yml'));
    $nightlyWorkflow = file_get_contents(base_path('.github/workflows/publish-nightly-artifacts.yml'));

    expect($productionWorkflow)->toContain('repository: loccen/coolify-zh-artifacts');
    expect($productionWorkflow)->toContain('artifacts-repo/coolify/install.sh');
    expect($productionWorkflow)->toContain('artifacts-repo/coolify/restore-coolify-instance.sh');
    expect($productionWorkflow)->toContain('artifacts-repo/coolify/service-templates-latest.json');
    expect($productionWorkflow)->toContain('artifacts-repo/json/releases.json');
    expect($productionWorkflow)->toContain('docker buildx imagetools inspect');
    expect($productionWorkflow)->toContain('jq');

    expect($nightlyWorkflow)->toContain('repository: loccen/coolify-zh-artifacts');
    expect($nightlyWorkflow)->toContain('artifacts-repo/coolify-nightly/install.sh');
    expect($nightlyWorkflow)->toContain('artifacts-repo/coolify-nightly/restore-coolify-instance.sh');
    expect($nightlyWorkflow)->toContain('artifacts-repo/coolify-nightly/versions.json');
});

it('only releases production artifacts from an explicit release workflow', function () {
    $releaseWorkflow = file_get_contents(base_path('.github/workflows/production-release.yml'));
    $productionBuildWorkflow = file_get_contents(base_path('.github/workflows/coolify-production-build.yml'));
    $helperWorkflow = file_get_contents(base_path('.github/workflows/coolify-helper.yml'));
    $realtimeWorkflow = file_get_contents(base_path('.github/workflows/coolify-realtime.yml'));
    $artifactsWorkflow = file_get_contents(base_path('.github/workflows/publish-production-artifacts.yml'));
    $changelogWorkflow = file_get_contents(base_path('.github/workflows/generate-changelog.yml'));

    expect($releaseWorkflow)->toContain('release:');
    expect($releaseWorkflow)->toContain('published');
    expect($releaseWorkflow)->toContain('./.github/workflows/coolify-production-build.yml');
    expect($releaseWorkflow)->toContain('./.github/workflows/publish-production-artifacts.yml');
    expect($releaseWorkflow)->toContain('v${APP_VERSION}');

    expect($productionBuildWorkflow)->toContain('workflow_call:');
    expect($productionBuildWorkflow)->not->toContain('branches: ["v4.x"]');

    expect($helperWorkflow)->toContain('workflow_call:');
    expect($helperWorkflow)->not->toContain('branches: [ "v4.x" ]');

    expect($realtimeWorkflow)->toContain('workflow_call:');
    expect($realtimeWorkflow)->not->toContain('branches: [ "v4.x" ]');

    expect($artifactsWorkflow)->toContain('workflow_call:');
    expect($artifactsWorkflow)->not->toContain('workflow_dispatch:');
    expect($artifactsWorkflow)->toContain('coolify-helper:${{ inputs.helper_version }}');
    expect($artifactsWorkflow)->toContain('coolify-realtime:${{ inputs.realtime_version }}');

    expect($changelogWorkflow)->toContain('create-pull-request@v7');
    expect($changelogWorkflow)->not->toContain('git push https://${{ secrets.GITHUB_TOKEN }}@github.com/${GITHUB_REPOSITORY}.git v4.x');
});

it('does not keep upstream release infrastructure references in p0 files', function () {
    $files = [
        'config/constants.php',
        'scripts/install.sh',
        'scripts/upgrade.sh',
        'other/nightly/install.sh',
        'other/nightly/upgrade.sh',
        'docker-compose.prod.yml',
        'other/nightly/docker-compose.prod.yml',
        'docker-compose.windows.yml',
        'other/nightly/docker-compose.windows.yml',
        '.github/workflows/coolify-production-build.yml',
        '.github/workflows/coolify-staging-build.yml',
        '.github/workflows/coolify-helper.yml',
        '.github/workflows/coolify-helper-next.yml',
        '.github/workflows/coolify-realtime.yml',
        '.github/workflows/coolify-realtime-next.yml',
        '.github/workflows/coolify-testing-host.yml',
        '.github/workflows/production-release.yml',
        '.github/workflows/publish-production-artifacts.yml',
        '.github/workflows/publish-nightly-artifacts.yml',
        'app/Console/Commands/SyncBunny.php',
        'app/Actions/Server/CleanupDocker.php',
        'app/Jobs/CleanupHelperContainersJob.php',
        'app/Livewire/Settings/Index.php',
        'resources/views/components/version.blade.php',
        'resources/views/livewire/settings-dropdown.blade.php',
        'resources/views/livewire/upgrade.blade.php',
    ];

    $bannedSnippets = [
        'cdn.coollabs.io',
        'cdn.coolify.io',
        'coolify-cdn',
        'coollabsio/coolify-helper',
        'coollabsio/coolify-realtime',
        'ghcr.io/coollabsio/coolify',
        'docker.io/coollabsio/coolify',
        'github.com/coollabsio/coolify/releases/tag',
    ];

    foreach ($files as $file) {
        $contents = file_get_contents(base_path($file));

        foreach ($bannedSnippets as $snippet) {
            expect($contents)->not->toContain($snippet, "{$file} still contains {$snippet}");
        }
    }
});
