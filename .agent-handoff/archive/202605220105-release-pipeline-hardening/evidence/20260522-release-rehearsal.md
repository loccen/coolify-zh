# 2026-05-22 生产发布链真实演练

## GitHub 侧结果

- workflow 改造 PR：`https://github.com/loccen/coolify-zh/pull/1`
- PR 状态：`MERGED`
- merge commit：`1a11b8a8c7db497fe1a6522af07b7513e70e16ba`
- `v4.x` 保护分支：已启用
  - `required_pull_request_reviews.required_approving_review_count = 0`
  - `allow_force_pushes = false`
  - `allow_deletions = false`
- release：`https://github.com/loccen/coolify-zh/releases/tag/v4.1.3`
- release workflow：
  - 名称：`Production Release`
  - run id：`26244657759`
  - 结果：`success`

## artifacts 验证

- `https://loccen.github.io/coolify-zh-artifacts/coolify/versions.json`
  - `coolify.v4.version = 4.1.3`
  - `coolify.helper.version = 1.0.14`
  - `coolify.realtime.version = 1.0.15`
- `https://loccen.github.io/coolify-zh-artifacts/json/releases.json`
  - 最新条目：`v4.1.3`

## GHCR manifest 验证

### `ghcr.io/loccen/coolify:4.1.3`

- manifest list digest：`sha256:7d75f3a3f908e99722425a9747c237d2e4200f1a958b44c175cd80cf5894bd25`
- 子 manifest：
  - `linux/amd64` -> `sha256:5acd1c027fbae9470e05ef84ebb965d8394b40027a1b596b6b895fba84b19906`
  - `linux/arm64` -> `sha256:3495f3fc174ea2841ab6bdf056a13985ec236bd918bb7ad08bc63227c6d19c15`

### `ghcr.io/loccen/coolify-helper:1.0.14`

- manifest list digest：`sha256:03e0149da14c1447ac4aec50599d80185717f9a27bf507a0c4f11df296d63c35`
- 子 manifest：
  - `linux/amd64` -> `sha256:95fded618a71dc8a17105618f9d4ce84b398801a376f44bcc0c9ec749ec37c76`
  - `linux/arm64` -> `sha256:e0d4d457aed68ddefbe024cbaf19a1a711d04991ff41ad8c928c7fb366876870`

### `ghcr.io/loccen/coolify-realtime:1.0.15`

- manifest list digest：`sha256:02a25686fccfb87b2374290e8f135b210211fe2b05b9354223766ea48aa1a7be`
- 子 manifest：
  - `linux/amd64` -> `sha256:bda5be426c2a8559684a9b36742e4eedd890fdbc27aed089b60ee9acf65fe4a7`
  - `linux/arm64` -> `sha256:4d267229639b69d30e59582c0cafe356ef1eb555eb0dfbac1b3264d49987dd08`

## 实例自动更新验证

- 验证实例：`172.233.75.42`
- 访问入口：`http://172.233.75.42:8000`
- 健康检查：`http://172.233.75.42:8000/api/health`

### 升级前

- `config('constants.coolify.version') = 4.1.2`
- `instanceSettings()->is_auto_update_enabled = true`
- `instanceSettings()->new_version_available = false`
- `docker ps` 中 `coolify` 镜像：`ghcr.io/loccen/coolify:4.1.2`

### 为了加速演练做的临时调整

- 原始频率：
  - `update_check_frequency = 0 * * * *`
  - `auto_update_frequency = 0 0 * * *`
- 临时改为：
  - `update_check_frequency = * * * * *`
  - `auto_update_frequency = * * * * *`
- 说明：实例的 `schedule:work` 长驻进程不会热加载新的 cron 表达式，因此通过进程重启 / 应用容器重启让 scheduler 重新读取更新后的配置；更新完成后再恢复原值。

### 自动更新发生后的观测

- 日志中连续出现：
  - `Running [App\\Jobs\\CheckForUpdatesJob]`
  - `Running [App\\Jobs\\UpdateCoolifyJob]`
  - `App\\Jobs\\UpdateCoolifyJob ... DONE`
- 升级后状态：
  - `config('constants.coolify.version') = 4.1.3`
  - `instanceSettings()->new_version_available = false`
  - `docker ps` 中 `coolify` 镜像：`ghcr.io/loccen/coolify:4.1.3`
  - `/api/health` 返回 `OK`

### 演练后恢复

- 已将频率恢复为：
  - `update_check_frequency = 0 * * * *`
  - `auto_update_frequency = 0 0 * * *`
- 已重启一次 `coolify` 容器，确认：
  - 容器健康
  - 当前版本仍为 `4.1.3`
  - `new_version_available = false`
