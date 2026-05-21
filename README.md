<div align="center">

# Coolify-Zh

Coolify 的中文长期维护版，面向个人运维与功能增强。

[简体中文](./README.md) | [English](./README.en.md)

</div>

## 项目说明

Coolify-Zh 基于 [coollabsio/coolify](https://github.com/coollabsio/coolify) 维护，是一个面向中文使用者的长期 fork。

Coolify 本身是一个开源、可自托管的平台，可以把它理解为 Heroku / Netlify / Vercel 一类平台的自建替代。你可以用自己的服务器管理应用、数据库和相关资源，只需要可用的 SSH 连接。

这个 fork 当前的重点有两类：

- 持续推进中文化，包括 README、界面文案与后续体验细节。
- 增加更适合个人日常运维和发布流程的能力。

## 与上游的关系

- 上游仓库：[`coollabsio/coolify`](https://github.com/coollabsio/coolify)
- 本仓库：[`loccen/coolify-zh`](https://github.com/loccen/coolify-zh)

本仓库不会回推上游，但会定期审视上游更新，挑选对个人长期运维有价值的内容同步进来。

当前仓库名已经调整为 `coolify-zh`，但应用内部品牌、镜像命名空间、安装脚本和在线升级链仍主要沿用上游 `Coolify` / `coollabsio` 命名。这是刻意保留的兼容状态，便于继续跟踪上游版本线。

## 安装

当前默认安装方式仍沿用上游脚本：

```bash
curl -fsSL https://cdn.coollabs.io/coolify/install.sh | bash
```

安装脚本源码仍在本仓库的 [scripts/install.sh](./scripts/install.sh)。

> [!NOTE]
> 目前 README 已切到中文默认，但运行时安装与升级链还没有整体换成 `coolify-zh` 命名。后续如果要做个人版一键安装与自升级，需要连同镜像、脚本和应用内升级入口一起调整。

## 文档与支持

- 安装文档：[coolify.io/docs/installation](https://coolify.io/docs/installation)
- 联系方式：[coolify.io/docs/contact](https://coolify.io/docs/contact)

如果你需要查看更接近上游原始表述的英文说明，可以直接阅读 [README.en.md](./README.en.md)。

## Cloud 版本

如果你不打算自托管，仍可以了解官方 Cloud 版本：

- Cloud 入口：[app.coolify.io](https://app.coolify.io)
- 官网与定价：[coolify.io](https://coolify.io)

## 这个 fork 的维护方向

- 中文优先：仓库文档默认中文，后续逐步补齐中英切换与界面汉化。
- 个人运维优先：优先保留对实际部署、升级、镜像发布有帮助的流程。
- 跟踪上游：定期评估上游 `v4.x` 更新，再决定是否吸收。
- 渐进替换：在不破坏兼容性的前提下，逐步把仓库层、安装层、升级层调整到更适合个人维护的状态。

## 更多信息

- 英文说明：[README.en.md](./README.en.md)
- 上游仓库：[coollabsio/coolify](https://github.com/coollabsio/coolify)
- 上游完整 README（含赞助、社区与活动信息）：[coollabsio/coolify/README.md](https://github.com/coollabsio/coolify/blob/v4.x/README.md)
