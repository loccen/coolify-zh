# 2026-05-22 当前发布链事实快照

## 分支与保护状态

- 仓库：`loccen/coolify-zh`
- 本地工作区：`/Users/loccen/code/coolify-zh`
- 当前分支：`v4.x`
- GitHub `v4.x` 保护状态：`protected=false`
- `gh api repos/loccen/coolify-zh/branches/v4.x/protection` 返回 `Branch not protected`

## 版本与发布状态

- 源码树当前版本：
  - `config/constants.php` 中 `coolify.version = 4.1.2`
  - `versions.json` 中 `coolify.v4.version = 4.1.2`
- GitHub Release 最新仍是：`v4.1.1`

这说明“源码里的版本声明”和“GitHub Release 页面”当前并不同步，新会话需要先判断 4.1.2 是不是要作为正式 release 补齐，还是先回退源码版本声明再做流水线重构。

## 当前自动更新链路

- 检测更新：
  - `CheckForUpdatesJob` 请求 `config('constants.coolify.versions_url')`
  - 只读取 artifacts `versions.json` 中的 `coolify.v4.version`
- 自动升级：
  - `UpdateCoolifyJob` 先同步执行 `CheckForUpdatesJob`
  - 若 `new_version_available=true`，再调用 `UpdateCoolify::run(false)`
  - `UpdateCoolify` 再下载远端 `upgrade.sh` 并触发升级

结论：
- 当前“检测到有新版本”只代表 artifacts `versions.json` 已对外发布
- 不代表对应镜像 tag 已在 GHCR 可拉取

## 当前 workflow 触发结构

### 1. Production Image Build (v4)

- 文件：`.github/workflows/coolify-production-build.yml`
- 触发：`push` 到 `v4.x`
- 特征：使用 `paths-ignore`，不是显式 `paths`
- 问题：它和 artifacts 发布是并行触发，不会先于 artifacts 发布形成硬依赖

### 2. Publish Production Artifacts

- 文件：`.github/workflows/publish-production-artifacts.yml`
- 触发：
  - `push` 到 `v4.x` 且命中指定文件
  - `workflow_dispatch`
- 当前行为：
  - 直接复制仓库里的 `versions.json`
  - 通过 `gh api repos/${github.repository}/releases?per_page=30` 生成 `json/releases.json`
- 问题：
  - 没有等待镜像构建成功
  - 没有校验 GHCR manifest 是否存在

### 3. Generate Changelog

- 文件：`.github/workflows/generate-changelog.yml`
- 触发：
  - `push` 到 `v4.x`
  - `workflow_dispatch`
- 当前行为：
  - 生成 `CHANGELOG.md`
  - 直接 `git push ... v4.x`
- 问题：
  - 会制造 branch drift
  - 与“v4.x 只允许 PR 合入”的目标直接冲突

## 最近可用 run 证据

- `26239985977` — `Production Image Build (v4)` — success
- `26239986201` — `Publish Production Artifacts` — success
- `26239985955` — `Generate Changelog` — success

这些 run 说明当前主链在“现有设计下”可以工作，但不说明它满足“镜像已就绪后才对外发布版本元数据”的要求。

## 当前本地/远端边界

- 本地 `v4.x` 当前落后 `fork/v4.x` 一个自动 changelog 提交：`9b825db docs: update changelog`
- 实施新方案时，建议：
  - `git fetch fork --prune`
  - `git switch -c codex/release-pipeline-hardening fork/v4.x`

不要在当前本地 `v4.x` 上继续堆改动，以免把“自动 changelog 回写的漂移”混进方案实现。
