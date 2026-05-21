# 2026-05-22 本地 workflow hardening 实施快照

## 当前分支与基底

- 当前工作分支：`codex/release-pipeline-hardening`
- 当前基底提交：`8ffb5de`
- 说明：最初按旧 handoff 从 `fork/v4.x` 起分支后，发现远端分支已落后，本地 `v4.x` 实际领先 3 个提交，因此已删除错误分支并从本地 `v4.x` 重新创建。

## 已落地改动

- 新增 `.github/workflows/production-release.yml`
  - 只在 `release.published` 时触发 production 发布
  - 校验 release tag 必须等于 `v${APP_VERSION}`
  - 依次调用 app/helper/realtime 镜像构建与 artifacts 发布
- 修改 `.github/workflows/coolify-production-build.yml`
  - 仅保留 `workflow_call`
  - 输出应用镜像版本供 orchestrator 使用
- 修改 `.github/workflows/coolify-helper.yml`
  - 仅保留 `workflow_call`
  - 输出 helper 镜像版本供 orchestrator 使用
- 修改 `.github/workflows/coolify-realtime.yml`
  - 仅保留 `workflow_call`
  - 输出 realtime 镜像版本供 orchestrator 使用
- 修改 `.github/workflows/publish-production-artifacts.yml`
  - 仅保留 `workflow_call`
  - 在发布 artifacts 前对 `coolify` / `coolify-helper` / `coolify-realtime` 逐一执行 `docker buildx imagetools inspect`
  - 使用 `jq` 基于验证后的版本值生成 artifacts `versions.json`
- 修改 `.github/workflows/generate-changelog.yml`
  - 改为 `workflow_dispatch`
  - 使用 `peter-evans/create-pull-request@v7`
  - 不再直接 push `v4.x`
- 修改 `config/constants.php` 与 `versions.json`
  - 版本从 `4.1.2` 升到 `4.1.3`
- 修改 `tests/Feature/ReleaseArtifactConfigurationTest.php`
  - 新增 production release 触发方式与版本同步断言

## 本地验证

### actionlint

```bash
docker run --rm -v "$PWD":/repo -w /repo rhysd/actionlint:latest -color \
  .github/workflows/coolify-production-build.yml \
  .github/workflows/coolify-helper.yml \
  .github/workflows/coolify-realtime.yml \
  .github/workflows/publish-production-artifacts.yml \
  .github/workflows/generate-changelog.yml \
  .github/workflows/production-release.yml
```

- 结果：通过

### Pint

```bash
docker run --rm -v "$PWD":/app -w /app ghcr.io/loccen/coolify:4.1.2 \
  vendor/bin/pint --dirty --format agent
```

- 结果：通过

### Pest

```bash
docker run --rm --user root -v "$PWD":/app -w /app ghcr.io/loccen/coolify:4.1.2 sh -lc '
  set -eux
  apk add --no-cache php84-sockets
  find /usr -name sockets.so
  echo extension=/usr/lib/php84/modules/sockets.so > /usr/local/etc/php/conf.d/docker-php-ext-sockets.ini
  php -m | grep -i sockets
  php artisan test --compact tests/Feature/ReleaseArtifactConfigurationTest.php
'
```

- 结果：通过，`6` 个用例全部通过

## 下一步

- 推送当前分支并创建到 `v4.x` 的 PR
- 合入后确认普通 merge 不会自动触发 production release
- 启用 `v4.x` 保护分支
- 创建 `v4.1.3` release，验证 GHCR manifest 与实例自动更新
