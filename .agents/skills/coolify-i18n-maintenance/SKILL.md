---
name: coolify-i18n-maintenance
description: "Use for ongoing Coolify internationalization work: untranslated pages, incorrect zh_CN copy, missing translation wiring, seed/demo text cleanup, locale flow fixes, and translation regression coverage. Covers scope triage, term rules, safe parallelization, and validation for continuing or correcting the project's i18n rollout."
license: MIT
metadata:
  author: codex
---

# Coolify I18n Maintenance

用于 Coolify 仓库后续所有“页面还没翻完”“翻译接线没做全”“中文翻错了”“开发示例数据还是英文”“通知/邮件渠道文案还漏翻”这类持续性国际化任务。

## 何时使用

出现以下任一情况就用：

- 用户指出某些页面仍是英文
- 用户指出中文翻译不准确、不自然，或技术术语翻错
- 需要继续补 `zh_CN` 翻译，不是从零设计，而是在现有国际化基础上继续做
- 需要判断某段文案该改 Blade、语言文件、seed 数据，还是通知/邮件渠道
- 需要为国际化改造补回归测试、清旧残留、做验收

不要用于：

- 与国际化无关的普通业务开发
- 纯视觉改版
- 只改英文原文措辞、完全不涉及 locale/翻译链路的任务

## 仓库约定

- 项目内国际化不是一次性任务，而是持续收敛过程。优先在现有结构上补齐，不要另起一套新机制。
- 本仓库国际化的稳定分层已经形成：
  - `T0/T2`: locale 解析、切换入口、`zh_CN` 默认与 `en` fallback
  - `T1/T3`: 翻译源清理、结构化语言文件
  - `T4`: 公共壳层与基础组件
  - `T5A-T5E`: 页面与前端交互接线
  - `T6`: 邮件、通知、命令、渠道文案
  - `T7`: `zh_CN` 人工校对
  - `T8`: 回归测试、清理、验收
- 继续做时不要回到“整仓盲扫”。先判断当前问题落在哪一层，再只动那一层。

## 先做分类

收到一个“没翻 / 翻错”的页面后，先把问题归到以下 5 类之一：

### 1. 静态页面没接翻译源

症状：

- Blade 里直接写英文或旧中文
- 页面里同一块区域有的翻了，有的没翻

优先改：

- 对应 `resources/views/...`
- 必要时补 `lang/en/*.php` / `lang/zh_CN/*.php`
- 如果是长句或遗留原文 key，再看 `lang/en.json` / `lang/zh_CN.json`

### 2. 已接翻译源，但 `zh_CN` 文案翻错

症状：

- 技术词误译
- 语气生硬
- 中英混排不统一

优先改：

- `lang/zh_CN/*.php`
- `lang/zh_CN.json`

不要顺手改业务逻辑。

### 3. 页面显示的是开发示例数据，不是翻译 key

症状：

- 示例项目名、服务器描述、存储描述仍是英文
- 只在本地开发环境中出现

优先改：

- `database/seeders/*`
- 与 seed 文案直接相关的测试

注意：

- 改 seed 文件不等于现有数据库会自动更新
- 如果用户需要立刻在本地环境看到变化，要同步更新当前开发库记录，或说明需要 reseed

### 4. 前端交互提示未翻

症状：

- toast、terminal、日志复制、下载、搜索提示仍是英文

优先改：

- `resources/js/*`
- 相关 Blade 内联脚本
- `window.coolifyI18n` 或 `@js(__('...'))` 载荷
- 相关语言文件，如 `lang/*/toast.php`、`lang/*/terminal.php`

### 5. 非页面链路未翻

症状：

- 邮件主题/正文仍是英文
- Slack / Telegram / Discord / Pushover / Webhook 渠道消息还没翻
- 命令行交互提示仍是英文

优先改：

- `app/Notifications/*`
- `resources/views/emails/*`
- `resources/views/vendor/mail/*`
- `app/Console/Commands/*`
- `lang/*/mail.php`
- `lang/*/notifications.php`
- `lang/*/console.php`

## 术语硬规则

这些是后续必须延续的统一写法：

- `Docker` 保持英文，绝不翻成“码头”
- `Docker Network` -> `Docker 网络`
- `Docker Tag` -> `Docker 标签`
- `Docker Compose` 保持英文
- `Preview Deployment` 保持英文
- `Swarm` 保持英文
- `Webhook` 保持英文
- `FQDN` 保持英文
- `SSH` 保持英文
- `SSL` 保持英文
- `Proxy` -> `代理`
- `Private Key` -> `私钥`
- `Health Check` -> `健康检查`
- `Source` -> `代码源`
- `Shared Variables` -> `共享变量`
- `Authentication` -> `认证`
- `Verification` -> `验证`
- `Two-Factor Authentication` -> `双重验证`

品牌与渠道名保持英文：

- `GitHub App`
- `Cloudflare`
- `Hetzner`
- `OAuth`
- `SMTP`
- `Resend`
- `Telegram`
- `Slack`
- `Discord`
- `Pushover`
- `MinIO`
- `Railpack`

## 翻译风格

- 优先短句
- 不要客服腔
- 不要机翻腔
- 面向运维 / 开发者 / 自托管用户，直接说明动作和状态
- 含 HTML 的帮助文案，只翻可见文本，不破坏标签、URL、占位符
- 命令、变量、路径、协议名、域名、端口、镜像名原样保留

## 结构选择规则

优先级：

1. 已有结构化 key，沿用
2. 同页面/同模块已有同类 key，按现有命名风格补
3. 只有长句、一次性帮助文案，留在 JSON

不要为了“看起来整齐”把所有长句强行拆成新命名空间。

## 并行策略

如果任务明显跨多个独立范围，可以并行，但每个子代理只做一个任务：

- `静态页面接线`
- `开发示例 seed 修正`
- `通知渠道补翻`
- `zh_CN` 某一组文件校对`

每个子代理都要：

- 独立 worktree
- 独立分支
- 独立 `ai-task`
- 完成后先提交，再由主代理验收并尽快合回主线
- 后续新子代理基于合并后的最新 HEAD 再派

## 最小工作流

### A. 修单页未翻译

1. `rg` 找页面里未接翻译源的英文
2. 补 `__()` 或已有 key
3. 如果缺语言文件 key，就补 `en` / `zh_CN`
4. 加最小断言测试
5. 跑定向测试和 `pint`

### B. 修误译

1. 定位 `lang/zh_CN.*`
2. 检查同一术语在其他文件中的现有译法
3. 统一改一批，不要一处一处散修
4. 跑相关定向测试

### C. 修本地开发示例数据

1. 定位 `database/seeders/*`
2. 改种子文案和受影响测试
3. 明确告知用户：
   - 代码已改
   - 现有开发库是否已同步更新
   - 是否需要 reseed

### D. 修通知/邮件渠道

1. 先看 `UserVisibleLocale` 这类 locale 固化链路是否已覆盖
2. 再补对应 `toSlack()` / `toDiscord()` / `toTelegram()` / `toWebhook()` / `toPushover()`
3. 更新 `mail.php` / `notifications.php` / `console.php`
4. 只跑与改动直接相关的最小测试

## 验证清单

优先跑最小必要测试，不要一上来全仓：

- locale 基础：`tests/Feature/LocaleSelectionTest.php`
- 公共壳层：`tests/Feature/ShellTranslationRenderingTest.php`
- 认证公开页：`tests/Feature/AuthPublicPageTranslationTest.php`
- 设置/Profile/Team：`tests/Feature/SettingsProfileTeamTranslationTest.php`
- 主业务页：`tests/Feature/CoreBusinessTranslationWiringTest.php`
- 安全/来源/订阅：`tests/Feature/T5dTranslationRenderingTest.php`
- 通知 locale：`tests/Unit/NotificationLocaleTest.php`
- 通知渠道文本：`tests/Unit/NotificationChannelTranslationTest.php`
- 开发 seed：`tests/Feature/DevelopmentSeedLocalizationTest.php`
- 静态页面补翻：`tests/Feature/StaticUiTranslationTest.php`

所有 PHP 改动完成后：

```bash
vendor/bin/pint --dirty --format agent
```

如果 worktree 没有本地 `vendor`，可用容器只读挂主仓 `vendor` 做验证；验证后不要把临时软链留在工作树。

## 旧残留检查

如果问题像是“怎么又回退成旧方案”，额外检查：

- `LocaleHtmlTranslationTest`
- `lang/zh-cn.json`
- 旧自动抽词脚本
- HTML 响应后替换逻辑

必要时直接用：

```bash
rg -n "LocaleHtmlTranslationTest|zh-cn\\.json|extract.*trans|auto.*trans|preg_replace.*__\\(" tests app resources lang scripts routes
```

## 完成定义

一个国际化修整任务只有同时满足以下条件才算完成：

- 问题已归类到正确层级
- 改动落在正确文件，不用业务逻辑绕过
- `zh_CN` 文案自然、术语正确
- 相关定向测试已跑
- `pint` 已跑
- 如果涉及 seed，已经明确说明是否需要 reseed，或已同步本地开发库
- 如果是多分支并行，主代理已验收并合回最新 HEAD

## 回答模板

最终向用户汇报时，优先说明 4 件事：

1. 这次修的是“静态页面没接翻译源”还是“翻译词条错误”还是“seed 数据是英文”
2. 已改哪些文件
3. 已跑哪些测试，结果如何
4. 是否还需要 reseed / 强刷 / 重启 dev server 才能在本地立刻看到变化
