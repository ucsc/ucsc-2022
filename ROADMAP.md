# UCSC 2022 Theme — Roadmap

_Compiled 2026-07-08 from a code audit of v4.5.2 (PHP, JS/SCSS, templates/patterns/theme.json) plus the open GitHub issue backlog. Updated 2026-10-08 to reflect v5.0.0–v5.2.0 and [PR 404](https://github.com/ucsc/ucsc-2022/pull/404). Priorities run P0 (fix now) → P3 (cleanup/backlog). Effort: S (hours), M (days), L (a dedicated effort)._

---

## Recently completed

| Date | Change | Commit / release | Roadmap item |
| --- | --- | --- | --- |
| 2026-07-08 | Fixed the broken `path` argument in the block-style loader, so small per-block stylesheets are inlined again and get RTL support | `638db61` · v5.0.0 | 3 |
| 2026-07-09 | Restored PHP linting (WPCS 3.3.0) so the security checks actually run | `44b7955` · v5.0.0 | 6 |
| 2026-07-09 | Repaired the template and template-part files whose attributes were broken by the v4.3.4 find/replace | `b565ae3` · v5.0.0 | 1 |
| 2026-07-09 | Stylelint can now lint Sass files | `d2ea0ef` · v5.0.0 | 10 (part) |
| 2026-07-10 | Mobile menu Phase 1: the menu scrolls and closes reliably; fixes [issue 355](https://github.com/ucsc/ucsc-2022/issues/355), [issue 393](https://github.com/ucsc/ucsc-2022/issues/393), [issue 394](https://github.com/ucsc/ucsc-2022/issues/394) | `7d4833f` ([PR 401](https://github.com/ucsc/ucsc-2022/pull/401)) · v5.0.0 | 2 (part) |
| 2026-07-13 | License set to GPL-2.0-or-later everywhere | `9c76e23` · v5.0.0 | 17 (part) |
| 2026-07-13 – 07-16 | Cover block background videos: play/pause and captions controls, reduced-motion support, and styling refinements; the captions button appears only when a captions file exists | `79bdb99`, `7a993df`, `b874c17`, `cc6b464`, `5e97f1a`, `13ac382` · v5.0.0–v5.2.0 | New |
| 2026-07-16 | The dashboard auto-update library runs only under specific conditions | `14a15a6` · v5.2.0 | New |
| 2026-07-10 | Theme CSS and `theme.js` are now versioned by file contents, so rebuilt assets aren't served from stale caches (part of the mobile menu PR) | `7d4833f` ([PR 401](https://github.com/ucsc/ucsc-2022/pull/401)) · v5.0.0 | 14 (part) |
| 2026-10-07 | Truss loads in the `<head>` from `truss.ucsc.edu`, with a `preconnect` hint | `5960f2f`, `b9dc62f` · PR 404 | 14 (part) |
| 2026-10-07 | `theme.json` upgraded to schema version 3 (closes [issue 345](https://github.com/ucsc/ucsc-2022/issues/345)); fixed the `serif serif` font stack; removed the rule for the non-existent `core/block-post` block; removed "(Do not use) Page no title with breadcrumbs" from the template picker | `3b865ee`, `5c5b41f` · PR 404 | 11 |
| 2026-10-07 | Replaced the footer curves ([issue 403](https://github.com/ucsc/ucsc-2022/issues/403)) | `431f692` · PR 404 | New |

**Deployment caveat for item 1:** sites where the header template part was ever saved in the Site Editor keep a database copy of the broken markup, which overrides the fixed file. Those sites need **Appearance → Editor → Template Parts → header → Clear customizations**. Include this in release notes.

---

## P0 — Critical bug fixes

| # | Item | Status | Effort | Details |
| --- | --- | --- | --- | --- |
| 1 | Repair malformed template-part JSON | ✅ Done (v5.0.0) | — | See caveat above. |
| 2 | Mobile main-nav rebuild | 🟡 Phase 1 done (v5.0.0); Phase 2 open | M | **Still open:** [issue 302](https://github.com/ucsc/ucsc-2022/issues/302) (toggle overlaps header content). **Phase 2:** make the open panel a fixed overlay anchored to the viewport (`position: fixed; overflow-y: auto; overscroll-behavior: contain`) so its height needs no JS; replace `src/js/utils/body-lock.js` with `html.main-nav--is-open { overflow: hidden }`; animate `transform`/`opacity` instead of `height`, so interrupted animations can't break state (`main-nav.js` still depends on `transitionend`); replace the resize chain with one `matchMedia('(min-width: 1060px)')` listener; fold in item 8. **Test on:** iOS Safari (long menus, mid-page scroll, double-tap, rotation), narrow Firefox, keyboard only, VoiceOver. |
| 3 | Fix the `path` argument in the block-style loader | ✅ Done (v5.0.0) | — | Optional follow-up: pass `'ver' => filemtime($path)` so per-block styles bust caches on change. |
| 4 | Breadcrumbs structured data fails Google validation — [issue 330](https://github.com/ucsc/ucsc-2022/issues/330) | 🔴 Open | S–M | `itemListElement` is missing the required `position` field in the microdata printed by the theme's breadcrumbs (`justintadlock/hybrid-breadcrumbs`, `Trail::render()` in `functions.php`). On news.ucsc.edu, Yoast's breadcrumb JSON-LD already includes `position`, and the checked pages carried no theme microdata, so the error likely comes from pages that use the theme's breadcrumbs block. Add `position`, or drop the microdata when Yoast is active so there's one breadcrumb source. |

---

## P1 — High priority

| # | Item | Status | Effort | Details |
| --- | --- | --- | --- | --- |
| 5 | Security hardening (one PR) | 🔴 Open | S | Escape the ACF `subtitle-copy` value with `esc_html()` in `ucsc_post_subtitle()`; escape `$should_use_logo` with `esc_attr()` in the `use-logo` attribute; add `defined('ABSPATH') \|\| exit;` to `lib/*.php`, `blocks/*/index.php`, `header-plugin.php` and `footer-plugin.php`; triage the 8 `EscapeOutput` lint findings (intentional `do_blocks()`/breadcrumb echoes get documented `// phpcs:ignore` notes). |
| 6 | Restore PHP linting | ✅ Done (v5.0.0) | — | Follow-up: run `composer lint-fix` as its own formatting-only PR (579 of 630 errors are auto-fixable). |
| 7 | PHP correctness fixes | 🔴 Open | S | `ucsc_post_subtitle()` reads `$subtitle` when ACF is inactive (undefined variable) and has an unused `global $post`; `lib/blocks.php` pattern removal uses `has_block()`, which never fires (one name also has a trailing space), so call `unregister_block_pattern()` directly; guard `[last-modified]` with `shortcode_exists()`; don't translate the ACF options `menu_slug` in `lib/acf.php`; move `remove_theme_support('core-block-patterns')` to `after_setup_theme`. |
| 8 | Nav accessibility (pairs with item 2) | 🔴 Open | M–L | Toggle button needs `aria-expanded`, `aria-controls` and `type="button"` (`blocks/main-nav/index.php`); add Escape to close, focus into the open menu and back on close; submenu toggles should set `aria-expanded` (as `sidebar-with-navigation.js` already does); injected submenu buttons need `type="button"` and a name. Related: [issue 359](https://github.com/ucsc/ucsc-2022/issues/359), [issue 358](https://github.com/ucsc/ucsc-2022/issues/358), [issue 325](https://github.com/ucsc/ucsc-2022/issues/325), [issue 317](https://github.com/ucsc/ucsc-2022/issues/317), [issue 332](https://github.com/ucsc/ucsc-2022/issues/332), [issue 338](https://github.com/ucsc/ucsc-2022/issues/338), [issue 339](https://github.com/ucsc/ucsc-2022/issues/339). |
| 9 | Null-safety in nav JS | 🔴 Open | S | `main-nav.js` and `sidebar-with-navigation.js` call methods on unchecked `querySelector()` results; one missing element stops all nav JS. Add guards and early returns. |

---

## P2 — Upgrades

| # | Item | Status | Effort | Details |
| --- | --- | --- | --- | --- |
| 10 | Build-tool modernization | 🟡 Stylelint fixed; rest open | M | Still on `@wordpress/scripts` 25.3, prettier-config 2.9, husky 8, lint-staged 13, Node 20.18. Order: (1) move Sass off deprecated `@import` (34 in `src/scss/style.scss`) to `@use`/`@forward`; (2) bump wp-scripts and prettier-config together and remove the stray `console.log()` in `webpack.config.js`; (3) husky 9 (drop `postinstall: husky install`) and lint-staged. About 100 existing Sass lint errors surfaced once linting worked; many fix with `npm run lint:style -- --fix`. |
| 11 | `theme.json` v2 → v3 — [issue 345](https://github.com/ucsc/ucsc-2022/issues/345) | ✅ Done (PR 404) | — | Generated CSS confirmed identical before and after. Follow-ups moved to item 19. |
| 12 | Modernize the two ACF blocks | 🔴 Open | M | Add `"apiVersion": 3` to `blocks/breadcrumbs/block.json` and `blocks/main-nav/block.json` (ACF 6.8 on WP 7.1 already registers them as v3, but don't rely on the default); the `style` handles `acf/main-nav-styles` and `acf/breadcrumb-styles` are registered nowhere, so register or remove them; ship the mobile-toggle JS as a `viewScript` on `main-nav` instead of globally. |
| 13 | Cut legacy JS weight | 🔴 Open | S–M | Remove `verge` (only used for `window.innerHeight`/`innerWidth` in `viewport-dims.js`); delete IE8-era `attachEvent`/`initEvent` code in `src/js/utils/events.js`; consolidate breakpoints defined in `options.js`, `_variables.scss` and `--desktop-breakpoint`; handle the `video.play()` promise in `lib/cover-block-video-controls.js`. |
| 14 | Performance and loading | 🟡 Partly done | M | **Done:** Truss in `<head>` from `truss.ucsc.edu` with `preconnect`; CSS and `theme.js` versioned by contents. **Open:** self-host Roboto with `theme.json` `fontFace` and remove Google Fonts (also stops sharing visitor IPs with Google, and only weights 400–700 are used); load dashicons only when needed; stop loading the full front-end bundle in wp-admin (`ucsc-admin-scripts`); check `blockName` first in the two `render_block` filters that run on every block; Truss is still printed directly instead of enqueued, with no version pin. |

---

## P3 — Cleanup and backlog

| # | Item | Status | Effort | Details |
| --- | --- | --- | --- | --- |
| 15 | Pattern hygiene | 🔴 Open | M | Hardcoded image URLs (sandbox and www uploads, Unsplash links, theme-relative paths with stale attachment IDs) break on new sites, so ship images in the theme; `contentSize: "1280px"` is hardcoded in 9 patterns instead of inheriting `80rem`; `featured-image.php` and `featured-video.php` use the unregistered `media` category; pattern copy isn't translatable. |
| 16 | Dead code removal | 🔴 Open | S | `index.php` holds a full HTML document a block theme never runs (keep a stub); orphan parts `site-title-with-affiliation.html` and `ucsc-logo.html`; `build/index.js` is built but never loaded (`package.json` `main` points to it); `wp-blocks/styles.js` passes an unsupported `style_handle` key; deprecated `"layout":{"inherit":true}` in 7 template/part files; `$GLOBALS['ucsc_plugin_content']` is set but unused. |
| 17 | Metadata and docs | 🟡 License done; rest open | S | Document plugin dependencies (`ucscblocks`, the `[last-modified]` provider, ACF PRO, Icon Block); add a release check that the zip contains `vendor/`; make strings translatable (`Last modified:`, ACF labels, 404 copy); change `http://` to `https://` in `style.css` Theme URI and Update URI. |
| 18 | Tracker enhancements | 🔴 Open | — | [issue 368](https://github.com/ucsc/ucsc-2022/issues/368) 3:2 image size · [issue 344](https://github.com/ucsc/ucsc-2022/issues/344) Media Coverage archive · [issue 341](https://github.com/ucsc/ucsc-2022/issues/341) Query Loop card pattern · [issue 385](https://github.com/ucsc/ucsc-2022/issues/385) vertical-menu spacing · [issue 380](https://github.com/ucsc/ucsc-2022/issues/380) figcaption styling · [issue 367](https://github.com/ucsc/ucsc-2022/issues/367) mobile-menu placement · [issue 357](https://github.com/ucsc/ucsc-2022/issues/357)/[issue 386](https://github.com/ucsc/ucsc-2022/issues/386) "do not use" template (removed from the picker in PR 404; announcement still needed) · [issue 343](https://github.com/ucsc/ucsc-2022/issues/343) word-break in columns · [issue 337](https://github.com/ucsc/ucsc-2022/issues/337) underline thickness · [issue 266](https://github.com/ucsc/ucsc-2022/issues/266) table cell alignment · [issue 303](https://github.com/ucsc/ucsc-2022/issues/303) TOC pattern · [issue 158](https://github.com/ucsc/ucsc-2022/issues/158) icon pack. |
| 19 | `theme.json` follow-ups (new) | 🔴 Open | S–M | Fix undefined tokens: `--wp--custom--color--black` (heading color) and `--wp--custom--color--link-decoration` (link underline in `_links.scss`); add a `layout.wideSize` so "Wide" alignment does something; move link styles into `styles.elements.link` ([issue 346](https://github.com/ucsc/ucsc-2022/issues/346)) and add caption/cite element styles; define the `is-style-ucsc-*` button styles as `theme.json` block style variations; add a default box shadow ([issue 349](https://github.com/ucsc/ucsc-2022/issues/349)); set `customGradient: false` to match `color.custom: false`; confirm `core/paragraph` should allow custom font sizes. |
| 20 | Block API v3 warnings in UCSC plugins (new) | 🔴 Open (other repos) | M | Editor console warnings come from `ucsc-gutenberg-blocks` (8 blocks registered in JS with no `apiVersion`, needs `useBlockProps()` and possibly deprecations) and `ucsc-custom-functionality` (9 `block.json` files without `apiVersion`). Track in those repos. |

---

## Agent visibility and machine readability

_Checked 2026-10-12 against news.ucsc.edu (theme v5.2.0, with Yoast SEO and Cloudflare in front), using the homepage and a news article. AI agents and crawlers mostly read the raw HTML and don't run JavaScript, so they depend on what the server sends. Most of the basics are already in place on production: Yoast outputs structured data and preview tags, articles have one `<h1>`, `<article>` and `<time>` markup, and Cloudflare serves a Markdown version of each page to agents that ask for one. The gaps below are mostly settings and plugin output rather than theme code; each row says who owns it._

| # | Item | Owner | Status | Effort | Details |
| --- | --- | --- | --- | --- | --- |
| 21 | Structured data gaps | Yoast settings | 🔴 Open | S | Yoast already outputs `WebSite` (with site search), `WebPage`, `Article`, `Person` and `BreadcrumbList` with `position`, so **the theme shouldn't add its own schema**. Missing: no `Organization` node, so articles and the site have no `publisher` (set Yoast's site representation to an organization, with UC Santa Cruz as the parent and a logo); posts are typed `Article` instead of `NewsArticle` (Yoast's default article type for posts); no Google News sitemap (`/news-sitemap.xml` returns 404; needs the Yoast News SEO add-on). |
| 22 | Descriptions and preview tags | Yoast settings | 🔴 Open | S | No page checked has a `meta description`, even though articles have excerpts; set Yoast's description template to use the excerpt. The homepage's preview tags are weak: `og:title` is just "UC Santa Cruz", `og:description` is "Explore stories by topic", and there's no `og:image`. |
| 23 | Global header and footer invisible without JavaScript | Truss + theme | 🔴 Open | M–L | Confirmed on production: `<trss-ucsc-header>` and `<trss-ucsc-footer>` arrive empty, so crawlers that don't run JS never see the campus-wide navigation, search or footer links. Options: pre-render Truss (Stencil supports declarative shadow DOM), or have the theme print plain fallback links inside the tags. Would also help [issue 332](https://github.com/ucsc/ucsc-2022/issues/332) (layout shift). |
| 24 | Dates agents can read | Theme + `ucsc-custom-functionality` | 🔴 Open | S | **Theme:** the "Last modified" footer (`ucsc_last_modified()` in `functions.php`) is plain text; wrap it in `<time datetime="…">`. **Plugin:** the homepage's featured news cards show no dates and have no `<time>` markup, so an agent reading the homepage can't tell which stories are new (article pages and "Related stories" cards do have dates). **Also:** the HTTP `Last-Modified` header on the checked article said Oct 12 while its real modified date was Oct 8, so the header appears to reflect when the page was regenerated, not edited. |
| 25 | Markdown version drops the title, date and byline | `ucsc-custom-functionality` + Cloudflare | 🔴 Open | S–M | Requesting a page with `Accept: text/markdown` returns clean Markdown (Cloudflare's converter, cached at the edge). That's a real advantage for agents, but on articles the `<h1>`, date and author are missing from the body; only the title survives, in the front matter. They all sit in the post header block's `<header>` element, which the converter appears to skip. Test whether changing that block's markup, or a Cloudflare setting, keeps them. "Related stories" also comes through as raw HTML (`<h3>` tags inside link text). |
| 26 | Image alt text | `ucsc-custom-functionality`, `ucsc-blocks` | 🔴 Open | S | The six outlet logos in the homepage "Media coverage" section have no `alt` attribute at all, so agents and screen readers get nothing or a file name; use the outlet name. On the local site, event images from the `ucsc-blocks` events block had empty `alt` text; not checked on production. |
| 27 | Crawler access policy | `ucsc-primary-sites` (`class-robots-txt.php`) | 🔴 Open (policy) | S | Production `robots.txt` gives every listed AI bot `Crawl-delay: 120`, one request every two minutes. It treats training crawlers (GPTBot, ClaudeBot) the same as search and user-requested fetchers (OAI-SearchBot, ChatGPT-User, Claude-SearchBot, Claude-User, PerplexityBot), which aren't listed, so they fall under Yoast's allow-all block. `Google-Extended` is a usage setting, not a crawler, so a delay does nothing for it. Requests sent with AI crawler user agents got the same page as a browser, but Cloudflare may treat real, verified bots differently; check its bot settings. How much access to allow is a university decision. |
| 28 | Worth evaluating, not committing to | Site plugin | ⚪ Evaluate | — | `llms.txt` returns 404; few agents use it yet. Read-only WordPress Abilities API actions (news by topic, events, directory search) would serve signed-in agents and staff tools, not public crawlers, because every abilities endpoint requires a signed-in user. |

---

## Suggested sequencing

| Order | Work | Items |
| --- | --- | --- |
| 1. Now (one release) | Small, mostly mechanical fixes; a `composer lint-fix` formatting pass can ride along as its own commit | 5, 7, 19, 24 (theme part) |
| 2. Next (dedicated effort) | Finish the mobile nav and fix the breadcrumb schema | 2 (Phase 2), 8, 9, 4 |
| Settings (no code) | Yoast configuration on news.ucsc.edu and other Yoast sites | 21, 22 |
| 3. Then | Build tools first, then blocks and performance | 10 → 12, 13, 14 |
| 4. Ongoing | Fold cleanup into PRs that touch nearby files | 15–18 |
| Cross-repo | Raise with the Truss, `ucsc-custom-functionality`, `ucsc-blocks` and `ucsc-primary-sites` owners | 20, 23, 24 (plugin part), 25, 26, 27 |
