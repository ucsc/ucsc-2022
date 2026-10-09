# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

Official UC Santa Cruz WordPress block theme (FSE, `theme.json` v3). Requires the ACF PRO, Icon Block, and [ucsc-custom-functionality](https://github.com/ucsc/ucsc-custom-functionality) plugins to be active.

## All work in this repository

-   Eliminate AI-speak; be direct, concise, and technically precise
-   Always work on a branch
-   If working on an issue, check out an issue branch

## Commands

-   `npm run build` — compiles `src/` (Sass + JS) into `build/`. `build/` is gitignored but `functions.php` enqueues `build/style-index.css` and `build/theme.js`, so rebuild after any change in `src/`. `npm start` watches.
-   `npm run lint:style` / `npm run lint:js` — stylelint / ESLint via wp-scripts.
-   `composer lint` / `composer lint-fix` — PHPCS (WordPress-Extra + WordPress-Docs, see `.phpcs.xml.dist`).
-   `npm test` runs lint-staged (it's the husky pre-commit hook, not a test suite). There are no automated tests.
-   Use Node from `.nvmrc` (20.18.0).

## Code conventions

-   Tabs for indentation everywhere except YAML (2 spaces). Prettier config is `@wordpress/prettier-config`.
-   Many existing files (most PHP, ~30 JS/CSS) don't yet pass Prettier/PHPCS. Don't reformat whole files as a side effect of an edit — keep diffs scoped. Don't run `npm run format` (it rewrites the entire repo).
-   PHP: prefix all globals with `ucsc`; i18n text domain is `ucsc-2022`.
-   Sass lives in `src/scss/` and is imported from `src/scss/style.scss`. Front-end JS entry is `src/theme.js`; `src/index.js` only imports the styles.
-   Per-block CSS lives in `wp-blocks/<block-name>.css` (plain CSS, not compiled) and is loaded only for blocks listed in `$styled_blocks` in `functions.php`. Block style variations are registered in `wp-blocks/styles.js`. See the `add-block-style` skill.
-   Patterns are `patterns/*.php` with a header comment (`Title`, `Slug: ucsc-2022/<name>`, `Categories`) followed by serialized block markup — keep the block comment attributes and HTML in sync.
-   Custom blocks in `blocks/` are ACF blocks (`acf/*`), registered in `lib/blocks.php`.
-   `header-plugin.php` / `footer-plugin.php` wrap block template parts for plugins that render classic PHP templates — template-part slug changes must be reflected there too.

## Git workflow

-   Conventional Commits with a gitmoji after the type, optional scope: `fix(core/cover): 🎨 Refine styles for cover controls`. Reference issues with `Fixes #123`.
-   Subject line <72 chars, imperative mood, no period at end
-   Body explains WHY, not WHAT; wrap at 72 chars
-   Never commit to main directly
-   Use atomic commits -- one logical change per commit
-   Always include a test commit with implementation commits
-   Update ROADMAP.md file to reflect work done on branch
-   Branch from and open PRs against `main`. Fill in `.github/PULL_REQUEST_TEMPLATE.md`.
-   PR Audience: Reviewer who didn't see our chat
-   Pushing to `develop` auto-deploys to the Dokku dev site — don't push there unless asked.
-   Never hand-edit the version in `package.json`/`style.css` or `CHANGELOG.md`; `npm run release` (commit-and-tag-version) handles both. Pushing a `v*.*.*` tag triggers the GitHub release build.
