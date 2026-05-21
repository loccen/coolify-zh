# 发布流水线顺序化与 v4.x 保护分支改造

- Task ID: `202605220105-release-pipeline-hardening`
- Created At: `2026-05-22T01:25:35+08:00`
- Updated At: `2026-05-22T01:33:00+08:00`
- Status: `handoff-ready`

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
  - 当前源码树中的 `config/constants.php` 与 `versions.json` 已是 `4.1.2`。
  - `CheckForUpdatesJob` / `UpdateCoolifyJob` / `UpdateCoolify` 的现状已确认：检测更新只看 artifacts `versions.json`，升级时才真正去执行 `upgrade.sh` 与 `docker pull`。
  - `v4.x` 当前未保护，GitHub API 返回 `protected=false`。
  - 最近一轮生产发布成功证据已收集：
    - `Production Image Build (v4)`：`26239985977`
    - `Publish Production Artifacts`：`26239986201`
    - `Generate Changelog`：`26239985955`
  - 当前最新 GitHub Release 仍是 `v4.1.1`，与源码里的 `4.1.2` 之间存在差异，需要新会话先确认是否要把 4.1.2 作为正式 release 补齐。
- 未完成：
  - 发布链顺序化编排尚未落地。
  - `versions.json` 延后发布 / GHCR manifest 校验尚未落地。
  - `Generate Changelog` 的 PR 安全化尚未落地。
  - `v4.x` 保护分支尚未启用。
- 阻塞：
  - 当前本地 `v4.x` 相对 `fork/v4.x` 落后一个自动生成的 changelog 提交：`9b825db docs: update changelog`。新会话开始时不要直接在当前本地 `v4.x` 上硬做，先从 `fork/v4.x` 最新状态切新分支。

## 下一步

- 在新会话里按这个顺序执行：
  1. `git fetch fork --prune && git switch -c codex/release-pipeline-hardening fork/v4.x`
  2. 先改 `Generate Changelog`，去掉对 `v4.x` 的直接 push。
  3. 设计并落地 production release orchestrator，让 artifacts 发布显式依赖镜像 build 与 manifest verify。
  4. 让 artifacts 中的 `versions.json` 由 workflow 基于已验证版本生成，而不是直接复制仓库文件。
  5. 在 GitHub 上为 `v4.x` 打开保护分支，只允许 PR 合入。
  6. 用一次真实补丁版演练重新验证自动更新链路。
