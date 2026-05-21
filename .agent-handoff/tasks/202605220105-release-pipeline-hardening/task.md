# 发布流水线顺序化与 v4.x 保护分支改造

- Task ID: `202605220105-release-pipeline-hardening`
- Created At: `2026-05-22T01:25:35+08:00`
- Updated At: `2026-05-22T02:03:58+08:00`
- Status: `active`

## 目标

- 要解决的问题：
  - 当前生产发布链是多条 `push -> v4.x` workflow 并行触发，`versions.json` 的发布时间可能早于 `coolify` / `coolify-helper` / `coolify-realtime` 对应镜像真正 build 完成。
  - `CheckForUpdatesJob` 只信任 artifacts 仓库的 `versions.json`，不校验 GHCR manifest 是否已经存在，因此“检测到新版本”和“镜像已可拉取”之间没有硬保证。
  - `Generate Changelog` 现在会把 `CHANGELOG.md` 直接 push 回 `v4.x`，这和“把 `v4.x` 设为保护分支、只允许 PR 合入”是直接冲突的。
  - `v4.x` 当前未启用保护分支，仍允许直接 push，这会让普通小改动也有机会直接触发生产发布。
- 完成后的可见结果：
  - `v4.x` 变成保护分支，只允许通过 PR 合入，禁止直接 push 触发生产发布。
  - 生产发布链变成顺序化编排：先构建镜像，再校验远端 manifest，最后才发布 artifacts 和 `versions.json`。
  - `versions.json` 中的 `coolify.v4.version`、`coolify.helper.version`、`coolify.realtime.version` 只会在对应镜像 tag 已经在 GHCR 可解析时才对外发布。
  - `Generate Changelog` 不再直接写回 `v4.x`，改成 PR 友好或 release 友好的模式，避免分支漂移。

## 范围

- 包含：
  - 重构 `.github/workflows/coolify-production-build.yml`、`.github/workflows/coolify-helper.yml`、`.github/workflows/coolify-realtime.yml` 与 `.github/workflows/publish-production-artifacts.yml` 的触发关系。
  - 处理 `.github/workflows/generate-changelog.yml` 与保护分支的冲突。
  - 通过 GitHub 分支保护规则把 `v4.x` 收紧为“仅 PR 合入”。
  - 如有必要，新增 PR 预检 workflow 或 release 编排 workflow，保证保护分支启用后仍能稳定发布。
  - 重新验证“版本 bump -> 镜像 build -> artifacts 发布 -> 服务器自动更新”的完整链路。
- 不包含：
  - 不在本任务里继续扩展实例备份/恢复业务能力本身。
  - 不以这次任务为目标重构 nightly 发布链到完全同构；若抽出共享 workflow，可顺手让 nightly 跟进，但生产链是主目标。

## 事实源

- .github/workflows/coolify-production-build.yml
- .github/workflows/publish-production-artifacts.yml
- .github/workflows/generate-changelog.yml
- app/Jobs/CheckForUpdatesJob.php
- app/Jobs/UpdateCoolifyJob.php
- app/Actions/Server/UpdateCoolify.php
- config/constants.php
- versions.json
- .agent-handoff/tasks/202605220105-release-pipeline-hardening/evidence/20260522-current-release-state.md
- .agent-handoff/tasks/202605220105-release-pipeline-hardening/evidence/20260522-instance-verification.md

## 关键决策

- 决策：先处理 `Generate Changelog` 的直推行为，再启用 `v4.x` 保护分支。
- 原因：如果先开保护分支，现有 changelog workflow 会因为直接 push 到 `v4.x` 而持续失败，甚至反向干扰正常发布。

- 决策：`versions.json` 在 artifacts 仓库中的副本必须由发布 workflow 生成或校验后再写入，不能继续简单 `cp versions.json`。
- 原因：当前自动更新检测只看 artifacts `versions.json`，所以只要 `versions.json` 先发，实例就可能在镜像未就绪时误判“有新版本”。

- 决策：生产发布应当由“显式版本 bump / 显式 release 意图”驱动，而不是泛化成“任意 push 到 v4.x 都可能发版”。
- 原因：即使启用 PR 合入，若 trigger 仍过宽，普通 README / 文案 / 运维小改动的 PR merge 依旧会触发生产镜像与 artifacts 发布。

- 决策：`coolify`、`coolify-helper`、`coolify-realtime` 三个版本字段要分别按需验证；不要求每次都重建全部镜像，但要求 artifacts 里出现的版本值都已经在 GHCR 可解析。
- 原因：`helper` / `realtime` 版本并不一定每次跟随主应用升级，但只要 `versions.json` 对外宣告了某个版本，就必须能被实例真实拉取。

## 验收标准

- `v4.x` 分支保护已启用，至少满足：
  - 只允许 PR 合入
  - 禁止直接 push
  - 禁止 force push
  - 禁止删除分支
- `Generate Changelog` 不再直接 push 到 `v4.x`。
- 生产 artifacts 发布不再并行独立于镜像构建，而是显式依赖镜像构建成功与 GHCR manifest 校验通过。
- `CheckForUpdatesJob` 依赖的 artifacts `versions.json` 中所有对外版本，都对应可成功 `docker buildx imagetools inspect` 的 GHCR tag。
- 做一次真实补丁版演练时，顺序应满足：
  - PR 合入 `v4.x`
  - 生产镜像构建成功
  - artifacts `versions.json` 发布到新版本
  - 实例检测到更新
  - 自动更新成功完成
- 发布链改造后，不再出现 “workflow 自己回写 changelog 导致本地/远端再触发一轮发布” 的 branch drift。

## 当前状态

- 已完成：
  - 当前工作分支已改为 `codex/release-pipeline-hardening`，基于本地 `v4.x` 最新提交 `8ffb5de`，没有继续沿用落后的 `fork/v4.x` 基底。
  - 当前源码树中的 `config/constants.php` 与 `versions.json` 已升到 `4.1.3`，准备用这次 workflow hardening 做真实补丁版演练。
  - `CheckForUpdatesJob` / `UpdateCoolifyJob` / `UpdateCoolify` 的现状已确认：检测更新只看 artifacts `versions.json`，升级时才真正去执行 `upgrade.sh` 与 `docker pull`。
  - `v4.x` 当前未保护，GitHub API 返回 `protected=false`。
  - 自动更新真实验证实例已确认：
    - 服务器：`172.233.75.42`
    - SSH：`root@172.233.75.42`（免密）
    - 当前访问入口：`http://172.233.75.42:8000`
    - 健康检查：`http://172.233.75.42:8000/api/health`
    - 当前仍是 IP 直连 HTTP，未配置域名和 HTTPS
    - 最近一次已验证自动更新：`4.1.1 -> 4.1.2`
  - 最近一轮生产发布成功证据已收集：
    - `Production Image Build (v4)`：`26239985977`
    - `Publish Production Artifacts`：`26239986201`
    - `Generate Changelog`：`26239985955`
  - workflow 改造已落地到当前分支：
    - 新增 `.github/workflows/production-release.yml`，只在 `release.published` 时编排生产发布。
    - `.github/workflows/coolify-production-build.yml`、`.github/workflows/coolify-helper.yml`、`.github/workflows/coolify-realtime.yml` 已改为 `workflow_call`，不再由 `push v4.x` 直接触发。
    - `.github/workflows/publish-production-artifacts.yml` 已改为仅供 release 编排调用，并在写入 artifacts 前显式校验三个 GHCR manifest。
    - `.github/workflows/generate-changelog.yml` 已改为 `workflow_dispatch` + `create-pull-request`，不再直接 push `v4.x`。
  - 本地验证已通过：
    - `actionlint` 已通过 6 个相关 workflow 文件。
    - `vendor/bin/pint --dirty --format agent` 已在容器内通过。
    - `tests/Feature/ReleaseArtifactConfigurationTest.php` 已在补装 `sockets` 扩展的容器内通过。
- 未完成：
  - 当前分支改动尚未推送、开 PR、合入 `v4.x`。
  - `v4.x` 保护分支尚未启用。
  - 真实 `v4.1.3` release 还未创建，GHCR manifest 与实例自动更新还未做最终演练。
- 阻塞：
  - 暂无硬阻塞；后续主要是 GitHub 侧合入、保护分支与真实 release 演练。

## 下一步

- 按这个顺序继续：
  1. 推送 `codex/release-pipeline-hardening` 并创建 PR，目标分支为 `v4.x`。
  2. 合入 PR 后，确认普通 merge 没有再触发生产 release workflow。
  3. 在 GitHub 上为 `v4.x` 打开保护分支，只允许 PR 合入，禁止 direct push / force push / delete。
  4. 创建正式 release `v4.1.3`，触发新的 `Production Release` workflow。
  5. 等 release workflow 完成后，核对 `ghcr.io/loccen/coolify:4.1.3`、`coolify-helper:1.0.14`、`coolify-realtime:1.0.15` 都能 `docker buildx imagetools inspect`。
  6. 在实例 `172.233.75.42` 上确认 `new_version_available=true` 后自动更新成功，再复查 `/api/health` 和容器镜像 tag。
