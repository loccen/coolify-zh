# 发布流水线顺序化与 v4.x 保护分支改造 Handoff

## 已验证基线

- 当前源码版本值是 `4.1.2`，但最新 GitHub Release 仍是 `v4.1.1`。
- 自动更新检测逻辑只看 artifacts 仓库 `versions.json`，不看 GHCR tag 是否存在。
- 最近一轮成功的 production 证据：
  - `Production Image Build (v4)`：`26239985977`
  - `Publish Production Artifacts`：`26239986201`
  - `Generate Changelog`：`26239985955`
- `v4.x` 当前未保护，GitHub API 返回 `protected=false`。

## 未完成

- 把 production 发布链改成“先镜像、后 artifacts、最后 versions.json 对外可见”。
- 去掉 `Generate Changelog` 对 `v4.x` 的直接 push。
- 给 `v4.x` 打开保护分支，只允许 PR 合入。
- 做一次完整版本演练，证明“检测到更新”时，镜像已在 GHCR 可拉取。

## 第一步该做什么

- 不要直接继续当前本地 `v4.x`。先执行：
  - `git fetch fork --prune`
  - `git switch -c codex/release-pipeline-hardening fork/v4.x`
  然后优先改 `Generate Changelog`，因为保护分支启用前必须先清掉它的直推行为。

## 先看哪些文件/命令

- `AGENTS.md`
- `.agent-handoff/ACTIVE.md`
- `.agent-handoff/tasks/202605220105-release-pipeline-hardening/task.md`
- `.agent-handoff/tasks/202605220105-release-pipeline-hardening/evidence/20260522-current-release-state.md`
- `.github/workflows/coolify-production-build.yml`
- `.github/workflows/publish-production-artifacts.yml`
- `.github/workflows/generate-changelog.yml`
- `app/Jobs/CheckForUpdatesJob.php`
- `app/Jobs/UpdateCoolifyJob.php`

## 风险

- 如果先开 `v4.x` 保护分支，再改 changelog workflow，现有 workflow 会直接失败。
- 只要 artifacts 继续直接复制仓库里的 `versions.json`，就无法保证“检测到新版本时镜像已就绪”。
- 当前本地 `v4.x` 落后于 `fork/v4.x` 一个自动 changelog 提交，新会话若忽略这点，会把 branch drift 和方案实现混在一起。
