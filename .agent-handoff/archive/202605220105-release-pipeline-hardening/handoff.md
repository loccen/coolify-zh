# 发布流水线顺序化与 v4.x 保护分支改造 Handoff

## 已验证基线

- 当前工作分支是 `codex/release-pipeline-hardening`，基于本地 `v4.x` 最新提交 `8ffb5de`，不是落后的 `fork/v4.x`。
- 当前源码版本值已经升到 `4.1.3`，但最新 GitHub Release 仍是 `v4.1.1`。
- 自动更新检测逻辑只看 artifacts 仓库 `versions.json`，不看 GHCR tag 是否存在。
- 自动更新已在真实实例上验证过一次：
  - 验证实例：`172.233.75.42`
  - SSH：`root@172.233.75.42`（免密）
  - 访问入口：`http://172.233.75.42:8000`
  - 健康检查：`http://172.233.75.42:8000/api/health`
  - 当前是 IP 直连 HTTP，未配置域名/HTTPS
  - 已验证版本切换：`4.1.1 -> 4.1.2`
- 最近一轮成功的 production 证据：
  - `Production Image Build (v4)`：`26239985977`
  - `Publish Production Artifacts`：`26239986201`
  - `Generate Changelog`：`26239985955`
- `v4.x` 当前未保护，GitHub API 返回 `protected=false`。
- 当前分支已经完成的改动：
  - 新增 `Production Release` workflow，只在 `release.published` 时触发生产发布。
  - 原来的 app/helper/realtime build workflow 已改为 `workflow_call`，不再由 `push v4.x` 直接触发。
  - `publish-production-artifacts.yml` 改为先校验三个 GHCR manifest，再生成并发布 artifacts `versions.json`。
  - `generate-changelog.yml` 改为手动触发并创建 PR，不再直接 push `v4.x`。
- 本地验证已通过：
  - `actionlint`
  - `vendor/bin/pint --dirty --format agent`
  - `tests/Feature/ReleaseArtifactConfigurationTest.php`
- 最终结果：
  - PR `#1` 已合入 `v4.x`
  - `v4.x` 已保护
  - `v4.1.3` release 与 `Production Release` workflow 已成功
  - 验证实例已自动升级到 `4.1.3`

## 未完成

- 无，任务已完成。

## 第一步该做什么

- 不需要继续执行。
- 若要复核结果，先看：
  - `.agent-handoff/archive/202605220105-release-pipeline-hardening/evidence/20260522-release-rehearsal.md`
  - `.agent-handoff/archive/202605220105-release-pipeline-hardening/evidence/20260522-local-release-hardening.md`

## 先看哪些文件/命令

- `AGENTS.md`
- `.agent-handoff/ACTIVE.md`
- `.agent-handoff/tasks/202605220105-release-pipeline-hardening/task.md`
- `.agent-handoff/tasks/202605220105-release-pipeline-hardening/evidence/20260522-local-release-hardening.md`
- `.agent-handoff/tasks/202605220105-release-pipeline-hardening/evidence/20260522-current-release-state.md`
- `.agent-handoff/tasks/202605220105-release-pipeline-hardening/evidence/20260522-instance-verification.md`
- `.github/workflows/production-release.yml`
- `.github/workflows/coolify-production-build.yml`
- `.github/workflows/coolify-helper.yml`
- `.github/workflows/coolify-realtime.yml`
- `.github/workflows/publish-production-artifacts.yml`
- `.github/workflows/generate-changelog.yml`
- `tests/Feature/ReleaseArtifactConfigurationTest.php`

## 风险

- 无待处理风险；保留历史证据供复盘。
