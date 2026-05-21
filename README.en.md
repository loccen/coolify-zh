<div align="center">

# Coolify-Zh

A Chinese-maintained long-term fork of Coolify for personal operations and custom features.

[简体中文](./README.md) | [English](./README.en.md)

</div>

## About

Coolify-Zh is a long-term fork of [coollabsio/coolify](https://github.com/coollabsio/coolify), maintained with a Chinese-first documentation experience and room for personal workflow enhancements.

Coolify itself is an open-source and self-hostable alternative to platforms such as Heroku, Netlify, and Vercel. It lets you manage applications, databases, and related resources on your own servers with an SSH connection.

This fork currently focuses on two things:

- Ongoing Chinese localization, including README, UI copy, and follow-up UX details.
- Extra features that better match personal day-to-day operations workflows.

## Upstream Relationship

- Upstream repository: [`coollabsio/coolify`](https://github.com/coollabsio/coolify)
- This fork: [`loccen/coolify-zh`](https://github.com/loccen/coolify-zh)

This repository is not intended to be contributed back upstream, but upstream changes will be reviewed regularly and selectively synced when they are valuable for long-term personal maintenance.

The repository name is now `coolify-zh`, but the runtime branding, image namespace, install scripts, and in-app upgrade chain still mostly follow the upstream `Coolify` / `coollabsio` naming. That compatibility is kept on purpose to make upstream tracking easier.

## Installation

The default installation flow still uses the upstream script:

```bash
curl -fsSL https://cdn.coollabs.io/coolify/install.sh | bash
```

The source for the installer is still available in [scripts/install.sh](./scripts/install.sh).

> [!NOTE]
> The README is now Chinese-first, but the runtime install and upgrade chain has not been fully renamed to `coolify-zh` yet. A true self-maintained install and upgrade path will require coordinated changes across images, scripts, and the in-app upgrade entry.

## Docs and Support

- Installation docs: [coolify.io/docs/installation](https://coolify.io/docs/installation)
- Contact: [coolify.io/docs/contact](https://coolify.io/docs/contact)

If you want the Chinese version as the default documentation entry, use [README.md](./README.md).

## Cloud Version

If you do not want to self-host, the official Cloud offering is still available:

- Cloud app: [app.coolify.io](https://app.coolify.io)
- Website and pricing: [coolify.io](https://coolify.io)

## Maintenance Direction

- Chinese-first docs: the repository README defaults to Chinese, with English kept as an alternate entry.
- Personal operations first: workflows that help real deployment, upgrades, and image publishing are prioritized.
- Upstream tracking: upstream `v4.x` changes will be reviewed on a regular basis.
- Gradual replacement: repository-level naming and docs can move first, while runtime-level naming is migrated more carefully later.

## More Information

- Chinese README: [README.md](./README.md)
- Upstream repository: [coollabsio/coolify](https://github.com/coollabsio/coolify)
- Upstream full README with sponsorship and community sections: [coollabsio/coolify/README.md](https://github.com/coollabsio/coolify/blob/v4.x/README.md)
