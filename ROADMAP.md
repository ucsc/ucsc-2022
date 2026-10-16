# Roadmap

Work planned for the theme, ordered by priority. Items combine three sources: the 2026-07-08 audit of v4.5.2 plus the GitHub issue backlog, the 2026-10-09 audit against `main` at `b9dc62f` (v5.2.0), and the 2026-10-12 agent-visibility audit. When you finish an item on a branch, move it to [Done](#done) in the same PR, with its commit or release.

Priority key:

- **P1**: broken behavior, security, or accessibility failure. Fix next.
- **P2**: incorrect or fragile, but there is a workaround or the impact is limited.
- **P3**: cleanup, consistency, maintainability.

## P1: Bugs, security, accessibility

1. **Plugin-rendered pages reference template parts that don't exist.** `header-plugin.php` renders `ucsc-header`, `ucsc-footer`, and `content-plugin` ([header-plugin.php:15-17](header-plugin.php#L15-L17)). None of them are in `parts/`. Logged-in users see "Template part has been deleted or is unavailable".
   1. Render `header`/`footer` with the same wrappers the block templates use (`header-region`, `breadcrumbs-region`, `footer-region`).
   2. Remove `$GLOBALS['ucsc_plugin_content']`, which is assigned and never used.
2. **Mobile nav throws on plugin-rendered pages.** `initMainNav()` caches `.header-region` as the sticky header. `header-plugin.php` doesn't output that class, so `IntersectionObserver.observe(null)` throws inside `hideMenu()` and nav init stops ([src/js/components/main-nav.js:171](src/js/components/main-nav.js#L171), [:146](src/js/components/main-nav.js#L146), [:100](src/js/components/main-nav.js#L100)). More generally, `main-nav.js` calls methods on unchecked `querySelector()` results, so one missing element stops all nav JS.
   1. Guard the observer call and the `querySelector()` results in `main-nav.js`.
   2. Fix the wrapper as described in the previous item.
3. **Sidebar navigation JS crashes when the current page is a top-level item.** `currentItem.closest( 'li.wp-block-navigation-submenu' )` returns `null` for a top-level `.current-menu-item`, so the `querySelector` call throws. `bindEvents()` and `fixAriaAttributes()` then never run ([src/js/block-patterns/sidebar-with-navigation.js:140-141](src/js/block-patterns/sidebar-with-navigation.js#L140-L141)). `init()` also calls `initBlockOnPageLoad()` a second time with no argument ([:153](src/js/block-patterns/sidebar-with-navigation.js#L153)).
   1. Add a null guard for the top-level case.
   2. Remove the redundant `initBlockOnPageLoad()` call.
4. **Nested `<main>` landmarks on archive, category, and blog pages.** `parts/post-query.html` sets `"tagName":"main"` on the Query block ([parts/post-query.html:1](parts/post-query.html#L1)). It is placed inside templates whose wrapper group is already `<main>` (`archive.html`, `category.html`, `home.html`). Change the Query `tagName` to `div` or `section`. Pairs with "Article semantics and content boundaries" under agent visibility.
5. **Footer navigation is hardcoded to database IDs.** `wp:navigation {"ref":1938}`, `1942`, and `1945` ([parts/site-footer.html:8-16](parts/site-footer.html#L8-L16)) only exist on one site. Every other install renders empty or fallback menus. Footer social links point to relative URLs (`"url":"twitter.com"`), which resolve against the current site.
   1. Remove the hardcoded `ref`s so editors choose menus per site.
   2. Replace the social links with real URLs, or remove them.
6. **Core block patterns are not actually removed.** `remove_theme_support( 'core-block-patterns' )` runs on `init` priority 10 ([lib/blocks.php:9](lib/blocks.php#L9)). Core's `_register_core_block_patterns_and_categories` is hooked to `init` priority 10 earlier, from `default-filters.php`, so core patterns are already registered by then. The workaround below it uses `has_block()`, which inspects post content, not the pattern registry, so it never fires. One slug also has a trailing space ([lib/blocks.php:12-17](lib/blocks.php#L12-L17)).
   1. Move the call to `after_setup_theme`.
   2. Delete the workaround once the hook is fixed.
7. **The main nav toggle has no accessible state.** The hamburger `<button>` has a server-rendered `aria-label` (`6ff650d`), but it never gets `aria-expanded` or `aria-controls` ([blocks/main-nav/index.php:1-5](blocks/main-nav/index.php#L1-L5)). The JS-generated submenu toggles have no label and no state either ([main-nav.js:249](src/js/components/main-nav.js#L249)). The `<nav>` has no `aria-label`, which makes it ambiguous next to the Truss header nav.
   1. Add `aria-expanded`/`aria-controls` to the hamburger toggle.
   2. Give the submenu toggles a label, `aria-expanded`/`aria-controls`, and `type="button"`.
   3. Add an `aria-label` to the `<nav>`.
   4. Update the state in `showMenu()`/`hideMenu()`/`activateMenuItem()`.
   5. Close the menu on Escape.
   6. Move focus into the open menu, and back to the toggle on close.

   Related tracker items: [issue 359](https://github.com/ucsc/ucsc-2022/issues/359) (header z-index), [issue 358](https://github.com/ucsc/ucsc-2022/issues/358) (hover state), [issue 325](https://github.com/ucsc/ucsc-2022/issues/325) (44px targets), [issue 317](https://github.com/ucsc/ucsc-2022/issues/317) (ribbon contrast), [issue 332](https://github.com/ucsc/ucsc-2022/issues/332) (CLS), [issue 338](https://github.com/ucsc/ucsc-2022/issues/338)/[issue 339](https://github.com/ucsc/ucsc-2022/issues/339) (sidebar toggles).
8. **Mobile menu Phase 2.** Phase 1 (v5.0.0) fixed the deadlocks; [issue 302](https://github.com/ucsc/ucsc-2022/issues/302) (toggle overlaps header content) is still open and the state machine still depends on `transitionend`.
   1. Make the open panel a fixed overlay anchored to the viewport (`position: fixed; overflow-y: auto; overscroll-behavior: contain`) so its height needs no JS.
   2. Replace `src/js/utils/body-lock.js` with `html.main-nav--is-open { overflow: hidden }`.
   3. Animate `transform`/`opacity` instead of `height` so interrupted animations can't break state.
   4. Replace the resize chain with one `matchMedia('(min-width: 1060px)')` listener.
   5. Fold in the toggle-accessibility item above.
   6. Test on iOS Safari (long menus, mid-page scroll, double-tap, rotation), narrow Firefox, keyboard only, and VoiceOver.
9. **Breadcrumbs structured data fails Google validation** ([issue 330](https://github.com/ucsc/ucsc-2022/issues/330)). `itemListElement` is missing the required `position` field in the microdata printed by the theme's breadcrumbs (`justintadlock/hybrid-breadcrumbs`, `Trail::render()` in `functions.php`). On news.ucsc.edu, Yoast's breadcrumb JSON-LD already includes `position`, and the checked pages carried no theme microdata, so the error likely comes from pages that use the theme's breadcrumbs block. Add `position`, or drop the microdata when Yoast is active so there's one breadcrumb source.

## P2: Correctness and fragility

### theme.json and presets

1. **The minimum WordPress version is wrong everywhere.** theme.json v3 requires WP 6.6. `style.css` says `Requires at least: 6.0`, `readme.txt` says `5.8`, and `README.md` says `6.7`. The PHP minimum is also inconsistent (7.4 / 7.0 / ">8.0"). `readme.txt` is the untouched starter template ("Theme Name", "Short description…").
   1. Choose one supported WordPress and PHP floor and set it in all three files.
   2. Fill in `readme.txt` or delete it.
2. **Undefined CSS custom properties.** These are referenced but never defined, so the declarations fall back to `inherit` or the initial value:
   - `--wp--custom--color--black`: core/heading text color ([theme.json:466](theme.json#L466))
   - `--wp--custom--color--link-decoration` ([src/scss/elements/\_links.scss:3](src/scss/elements/_links.scss#L3))
   - `--wp--custom--color--default-text`, `--wp--custom--color--light-gray` ([src/scss/pages/\_posts-pages.scss:4,23,30](src/scss/pages/_posts-pages.scss#L4))

   Point them at `--wp--preset--color--*` or define them under `settings.custom.color`.
3. **Undefined font-size slugs in markup.** `"fontSize":"medium"` ([templates/404.html:16](templates/404.html#L16), `patterns/media-with-text-fixed-width.php`) and `"fontSize":"xx-large"` (`patterns/example-page-home.php`, `patterns/example-page-topic.php`) output `has-medium-font-size` / `has-xx-large-font-size` classes that have no matching CSS. Map them to existing slugs (`one`…`eight`).
4. **The `ucsc/image/sizes` filter has no effect.** `register_sizes()` loops over `$this->sizes` directly, and the filtered result from `get_sizes()` is discarded ([lib/image_sizes.php:58-62](lib/image_sizes.php#L58-L62), [functions.php:418](functions.php#L418)).
   1. Loop over `$this->get_sizes()` in `register_sizes()`.
   2. Remove the stray `get_sizes()` call.
5. **Duplicate template.** `templates/page-no-title-with-breadcrumbs.html` is byte-identical to `page-no-title.html`, which already includes breadcrumbs, and it isn't declared in `customTemplates`. PR 404 already removed it from the template picker.
   1. Delete it, or make the two templates differ and declare both.
   2. Announce the removal ([issue 357](https://github.com/ucsc/ucsc-2022/issues/357), [issue 386](https://github.com/ucsc/ucsc-2022/issues/386)).
6. **Legacy layout attributes.** `"layout":{"inherit":true}` was superseded by `{"type":"constrained"}` in WP 6.1.
   1. Replace it in `templates/404.html`, `archive.html`, `category.html`, `home.html`, `parts/site-footer.html`, and `parts/site-header.html`.
   2. Move the deprecated Query `displayLayout` in `parts/post-query.html` to `post-template` `layout: {type: "grid"}`.
   3. Remove the hand-added `is-layout-constrained` class from `parts/site-header.html`; core already generates it.

### PHP

1. **ACF registration runs on the wrong hook.** `lib/acf.php` is included on `after_setup_theme` and calls `acf_add_local_field_group()` / `acf_add_options_page()` immediately. ACF 6.1+ expects `acf/init` and logs `_doing_it_wrong` for early options pages. The options page `menu_slug` is also passed through `esc_html__()`, which makes the slug translatable and breaks the field group's location rule in any non-English locale ([lib/acf.php:53-56](lib/acf.php#L53-L56)).
   1. Move the registration calls to `acf/init`.
   2. Stop passing the options page `menu_slug` through `esc_html__()`.
2. **Unescaped attribute in the Truss header.** `$should_use_logo` is printed into an attribute without escaping, and `search-action="/"` is hardcoded, which breaks subdirectory installs ([functions.php:454-457](functions.php#L454-L457)).
   1. Wrap `$should_use_logo` in `esc_attr()`.
   2. Replace `search-action="/"` with `esc_url( home_url( '/' ) )`.
3. **Missing direct-access guards and unescaped-output triage.** No theme PHP file has a `defined( 'ABSPATH' ) || exit;` guard. PHPCS reports 8 `EscapeOutput.OutputNotEscaped` findings (5 in `functions.php`, 1 each in `header-plugin.php`, `footer-plugin.php`, and `blocks/breadcrumbs/index.php`). (The subtitle XSS was separate — it was a filter return, which this sniff doesn't flag.)
   1. Add the guard to `lib/*.php`, `blocks/*/index.php`, `header-plugin.php`, and `footer-plugin.php`.
   2. Fix the real escaping finding (the Truss attribute above).
   3. Give the intentional `do_blocks()`/breadcrumb-trail echoes documented `// phpcs:ignore` annotations so the baseline reads zero.
4. **The last-modified footer prints a raw shortcode when the plugin is inactive.** `ucsc_last_modified()` always runs `do_shortcode( '[last-modified]' )` ([functions.php:481-486](functions.php#L481-L486)).
   1. Gate it on `shortcode_exists()`.
   2. Do the same for `[site-search]` in [templates/search.html](templates/search.html), which has no fallback when the plugin is off.
5. **The front-end bundle loads on every admin screen.** `ucsc_add_admin_scripts()` enqueues `build/theme.js` (nav, sticky header, sidebar behaviors) on all `admin_enqueue_scripts` ([functions.php:236-247](functions.php#L236-L247)). Nothing in it targets wp-admin. Remove it.
6. **Breadcrumbs fatal if Composer deps are missing.** `ucsc_breadcrumbs_constructor()` calls `Hybrid\Breadcrumbs\Trail` unconditionally ([functions.php:403](functions.php#L403)). That's fine for release zips, but a fresh clone without `composer install` white-screens.
   1. Add a `class_exists()` guard.
   2. Add a release check that the zip contains `vendor/`.
7. **The custom skip link adds an unlabeled navigation landmark.** The replacement skip link is wrapped in `<div role="navigation">` and assigns an undeclared global, `skipLinkContainer` ([functions.php:572-573](functions.php#L572-L573)). The core removal works on 6.9 (verified), but the replacement creates an extra landmark for one link. Drop the `role` or the custom implementation.

### Performance

1. **Google Fonts loads 7 Roboto weights; markup and CSS use 4.** Only 400, 500, 600, and 700 appear. 100, 300, and 800 are unused, unless Truss needs them.
   1. Confirm whether Truss needs the 100, 300, and 800 weights.
   2. Drop the unused weights. Better: self-host Roboto through `settings.typography.fontFamilies[].fontFace` in theme.json. That removes the third-party request and the `fonts.googleapis.com`/`gstatic` preconnects (and stops sharing visitor IPs with Google), and lets the editor load the font too.
2. **Dashicons loads on every front-end page** ([functions.php:200](functions.php#L200)) for one use, in `wp-blocks/details.css`.
   1. Replace that use with an inline SVG or `mask-image`.
   2. Drop the enqueue.
3. **Truss is printed with raw `echo` in `wp_head`, without versioning** ([functions.php:467-477](functions.php#L467-L477)).
   1. Use `wp_enqueue_script_module()` (WP 6.5+) and `wp_enqueue_style()` so caching plugins and dependency handling see it.
   2. Pin a version instead of tracking the evergreen deploy.
4. **`render_block` filters do work before checking the block name.** `ucsc_post_subtitle()` calls `is_single()` and `get_field()` for every block rendered on single posts, and `ucsc_post_author_link()` (priority 8) runs on every block site-wide. Check `$block['blockName']` first, or switch to targeted `render_block_core/post-title` filters.

## P3: Cleanup and maintainability

1. **Unused template parts:** `parts/ucsc-logo.html` and `parts/site-title-with-affiliation.html` aren't referenced anywhere. `parts/header.html` and `parts/footer.html` only wrap `site-header`/`site-footer`, adding a layer of indirection. Only `header` and `footer` are declared in `templateParts`, so the other parts appear as "Uncategorized" in the Site Editor.
2. **`index.php` is a stale classic/block hybrid** that would print raw block comments if it were ever used.
   1. Reduce it to the standard `// Silence is golden.` stub.
   2. Remove the related dead output: `build/index.js` is built but never enqueued, and `package.json` `main` points at it.
3. **Patterns ship site-specific media.** Attachment IDs (`"id":1373`, `"mediaId":52`, …) and image URLs on `sandbox.wordpress.ucsc.edu`, `www-next.ucsc.edu`, and Unsplash download links are hardcoded in about 10 patterns. Inserted patterns show broken images or attach to the wrong media on other sites.
   1. Move placeholder images into `images/` and reference them with `get_theme_file_uri()`.
   2. Remove the hardcoded `contentSize: "1280px"` from 9 patterns so they inherit the global `80rem`.
   3. Fix the unregistered `media` pattern category used by `featured-image.php`/`featured-video.php`.
4. **Patterns and strings aren't translatable.**
   1. Wrap pattern strings in `esc_html_e()`/`esc_attr_e()`; no pattern uses them.
   2. Move template HTML strings (404 heading, "News & Events" in `home.html`) into patterns if i18n matters.
   3. Make `Last modified:` and its aria-label in `functions.php`, and the ACF labels in `lib/acf.php`, translatable.
   4. Use `wp.i18n` / `wp_set_script_translations()` for the hardcoded English strings in `main-nav.js`, `cover-block-video-controls.js`, and `wp-blocks/styles.js`.
5. **Pattern metadata inconsistencies.**
   1. Make file names and slugs agree (`card-yellow.php` → `card-yellow-cta`, `post-query-card.php` → `query-loop-card`, `example-page-*` → `*-page`).
   2. Replace the nonexistent block type `core/featured-image` in `post-query-card.php` with `core/post-featured-image`.
6. **Register block styles in PHP.** `wp-blocks/styles.js` uses client-side `registerBlockStyle()` with `style_handle`, which is a PHP-only argument and is ignored in JS. Server-side `register_block_style()` makes the styles visible in Global Styles and in REST.
7. **Finish modernizing the two ACF blocks.** Ship the mobile-toggle JS as a `viewScript` on `main-nav` instead of in the global bundle. Both blocks already declare ACF Blocks V3 (see [Done](#done)).
8. **theme.json tidy-up.** Steps 7–11 are follow-ups from the v3 upgrade.
   1. Resolve `settings.custom.spacing`, which duplicates `spacingSizes` with a second scale.
   2. Resolve the conflict between `custom.border.radius: 0` and the button radius of `100px`.
   3. Set `customGradient: false` to match `color.custom: false`.
   4. Remove `spacingScale.steps: 0`, which is redundant with `defaultSpacingSizes: false` in v3.
   5. Align `$schema` (`wp/7.1`) with `Tested up to` (6.9).
   6. Bring the `theme-color` meta (`#00458c`) into the palette; the closest is Primary Blue `#003c6c`.
   7. Add `layout.wideSize` so "Wide" alignment does something.
   8. Move link styles into `styles.elements.link` ([issue 346](https://github.com/ucsc/ucsc-2022/issues/346)) and add caption/cite element styles.
   9. Define the `is-style-ucsc-*` button styles as block style variations.
   10. Add a default box shadow ([issue 349](https://github.com/ucsc/ucsc-2022/issues/349)).
   11. Confirm `core/paragraph` should allow custom font sizes.
9. **`header-plugin.php`** hardcodes `<html lang="en-US">`; use `language_attributes()`.
10. **Dead JS.**
    1. Remove the `attachEvent` / `createEvent` IE fallbacks in `src/js/utils/events.js`.
    2. Remove `state.is_tablet`/`is_mobile`/`*_initialized`, which are written but never read.
    3. Remove the stray `console.log()` in `webpack.config.js`.
    4. Replace `verge` and `lodash` with `window.innerWidth` and a small debounce; `lodash` is only a transitive dependency and isn't declared in `package.json`.
    5. Consolidate the breakpoints defined three times (`options.js`, `_variables.scss`, `--desktop-breakpoint`).
    6. Handle the promise `video.play()` returns in `lib/cover-block-video-controls.js`.
11. **Sass `@import` is deprecated** in Dart Sass and will be removed in 3.0. Migrate `src/scss/style.scss` (34 imports) to `@use`/`@forward`. Do this before the `@wordpress/scripts` upgrade below — newer bundled Dart Sass warns on every import.
12. **Metadata and docs.**
    1. Document the plugin dependencies the code assumes (`ucscblocks`, the `[last-modified]` provider, ACF PRO, Icon Block) — README lists some, not all.
    2. Change `http://` to `https://` in the `style.css` Theme URI and Update URI.
13. **Tracker enhancements** (schedule opportunistically):
    1. [issue 368](https://github.com/ucsc/ucsc-2022/issues/368) 3:2 image size
    2. [issue 344](https://github.com/ucsc/ucsc-2022/issues/344) Media Coverage archive
    3. [issue 341](https://github.com/ucsc/ucsc-2022/issues/341) Query Loop card pattern
    4. [issue 385](https://github.com/ucsc/ucsc-2022/issues/385) vertical-menu spacing
    5. [issue 380](https://github.com/ucsc/ucsc-2022/issues/380) figcaption styling
    6. [issue 367](https://github.com/ucsc/ucsc-2022/issues/367) mobile-menu placement
    7. [issue 343](https://github.com/ucsc/ucsc-2022/issues/343) word-break in columns
    8. [issue 337](https://github.com/ucsc/ucsc-2022/issues/337) underline thickness
    9. [issue 266](https://github.com/ucsc/ucsc-2022/issues/266) table cell alignment
    10. [issue 303](https://github.com/ucsc/ucsc-2022/issues/303) TOC pattern
    11. [issue 158](https://github.com/ucsc/ucsc-2022/issues/158) icon pack
14. **Block API v3 warnings in UCSC plugins (other repos).** Editor console warnings come from two plugins. Track in those repos.
    1. `ucsc-gutenberg-blocks`: 8 blocks registered in JS with no `apiVersion`; needs `useBlockProps()` and possibly deprecations.
    2. `ucsc-custom-functionality`: 9 `block.json` files without `apiVersion`.

## Tooling and CI

1. **`npm run lint:js` crashes** with `prettier.resolveConfig.sync is not a function`. `@wordpress/prettier-config@2.9` hoists Prettier 3, but `eslint-plugin-prettier@3` (from `@wordpress/scripts@25`) needs Prettier 2. JS is effectively unlinted. Upgrade `@wordpress/scripts`; 25.x is from early 2023. Order the work:
   1. Migrate Sass `@import` (see P3).
   2. Upgrade wp-scripts and prettier-config together.
   3. Upgrade husky to 9 and update lint-staged.
2. **Stylelint reports 100 errors**: 92 `no-descending-specificity`, plus whitespace. **PHPCS reports 664 errors and 93 warnings** (re-measured 2026-10-14) across 39 files; 613 can be fixed automatically, and 8 are `EscapeOutput.OutputNotEscaped`.
   1. Fix the escaping findings first (listed under P1/P2).
   2. Do the auto-fixable formatting in one dedicated commit (`composer lint-fix`, `npm run lint:style -- --fix`), not mixed with other changes.
3. **No lint job on pull requests.** The only workflows are Dokku deploy and release. Add a PR workflow that runs `lint:style`, `lint:js`, and `composer lint` on changed files.
4. **Outdated Actions in `pipeline-dokku.yml`.**
   1. Update `actions/checkout@v2`.
   2. Update `actions/setup-node@v2`.
   3. Replace the deprecated `::set-output` syntax.
5. **Node version drift.**
   1. Align `.nvmrc` (pins 20.18.0) with the shared release workflow (defaults to Node 24).
   2. Replace Husky 8's `husky install` postinstall, which is deprecated in Husky 9.

## Agent visibility and machine readability

_The original 2026-10-12 production audit sampled the homepage and one article (theme v5.2.0, Yoast SEO, Cloudflare). It recorded structured data, preview tags, article landmarks and dates, plus Markdown content negotiation. These observations describe the sample, not every page. The follow-up reviewed theme source, official guidance, and the homepage text. Direct raw-HTML and header requests failed because shell DNS was unavailable. The items marked "proposed" are proposed work, not confirmed production defects._

**Goal:** help agents discover stories, extract accurate facts, and cite stable URLs. JavaScript support varies across crawlers and browser agents. Essential content must remain available in server-rendered HTML. Google requires no special AI markup or AI text file for its AI search features. Standard search practices remain the foundation. Each item names the theme, plugin, editorial, or hosting owner.

1. **Structured data gaps.** _Owner: Yoast settings + site plugin._ The original sample contains `WebSite`, `WebPage`, `Article`, `Person` and `BreadcrumbList`, but no `Organization` or `publisher`. Keep Yoast as the schema owner.
   1. Configure the publisher identity and logo.
   2. Use `NewsArticle` for news posts, with accurate authors, images, `datePublished` and `dateModified`. Google also supports `Article`.
   3. Make sure that the graph matches visible content.
   4. Audit sitemap discovery before adding a news sitemap through Yoast News SEO or another compatible plugin. A 404 at `/news-sitemap.xml` does not establish that no news sitemap exists elsewhere. A news sitemap aids recent-news discovery, not guaranteed inclusion.
2. **Descriptions and preview tags.** _Owner: Yoast settings._
   1. Set Yoast's description template to use the excerpt. No page checked has a `meta description`, even though articles have excerpts.
   2. Strengthen the homepage's preview tags: `og:title` is just "UC Santa Cruz", `og:description` is "Explore stories by topic", and there's no `og:image`.
3. **Global header and footer invisible without JavaScript.** _Owner: Truss + theme._ Confirmed on production: `<trss-ucsc-header>` and `<trss-ucsc-footer>` arrive empty, so crawlers that don't run JS never see the campus-wide navigation, search or footer links. Options: pre-render Truss (Stencil supports declarative shadow DOM), or have the theme print plain fallback links inside the tags. Would also help [issue 332](https://github.com/ucsc/ucsc-2022/issues/332) (layout shift).
4. **Dates agents can read.** _Owner: theme + `ucsc-custom-functionality`._
   1. **Theme:** the "Last modified" footer (`ucsc_last_modified()` in `functions.php`) is plain text; wrap it in `<time datetime="…">`.
   2. **Plugin:** the homepage's featured news cards show no dates and have no `<time>` markup, so an agent reading the homepage can't tell which stories are new (article pages and "Related stories" cards do have dates).
   3. **Also:** the HTTP `Last-Modified` header on the checked article said Oct 12 while its real modified date was Oct 8, so the header appears to reflect when the page was regenerated, not edited.
5. **Markdown version drops the title, date and byline.** _Owner: `ucsc-custom-functionality` + Cloudflare._ Requesting a page with `Accept: text/markdown` returns clean Markdown (Cloudflare's converter, cached at the edge). That's a real advantage for agents, but on articles the `<h1>`, date and author are missing from the body; only the title survives, in the front matter. They all sit in the post header block's `<header>` element, which the converter appears to skip.
   1. Test whether changing that block's markup, or a Cloudflare setting, keeps them.
   2. Fix "Related stories", which comes through as raw HTML (`<h3>` tags inside link text).
6. **Image alt text.** _Owner: `ucsc-custom-functionality`, `ucsc-blocks`._
   1. Add `alt` text to the six outlet logos in the homepage "Media coverage" section, using the outlet name. They have no `alt` attribute at all, so agents and screen readers get nothing or a file name.
   2. Check event images from the `ucsc-blocks` events block on production. They had empty `alt` text on the local site.
7. **Crawler access policy.** _Owner: `ucsc-primary-sites` (`class-robots-txt.php`) + hosting._ The original audit recorded `Crawl-delay: 120` for listed AI bots. Support for this directive varies, so it is not an enforced rate limit.
   1. Separate search discovery, training, and user-requested access in the university policy. OpenAI documents independent controls for OAI-SearchBot and GPTBot. ChatGPT-User requests can fall outside robots.txt rules. Googlebot controls Google's AI search access. Google-Extended controls other uses.
   2. Make sure that robots rules, `noindex`, snippet restrictions, and Cloudflare settings agree with the approved policy.
   3. Use provider IP verification and hosting logs for access evidence. A spoofed user agent does not establish access for a verified bot.
8. **Optional agent interfaces (evaluate; P3).** _Owner: site plugin._ The original audit recorded a 404 for `llms.txt`. Google requires no AI text file. Public discovery does not require an agent API.
   1. Treat `llms.txt` as an experiment with a named consumer and measurable use. Keep any index generated from canonical public content.
   2. Evaluate WordPress Abilities API actions separately for staff tools, with endpoint permissions and authentication defined first.
9. **Article semantics and content boundaries (proposed; P1).** _Owner: theme + news template plugin._ `templates/single.html` has `<main>`, a title, date and byline, but no `<article>` wrapper around the story. The nested-`<main>` defect is tracked under P1 above.
   1. Audit database template overrides on news.ucsc.edu before changing files.
   2. Put the title, byline, dates and body inside one article region. Keep comments and related stories outside that region.
   3. Audit heading levels in templates and patterns.
10. **Crawlable archives, search and feeds (proposed; P2).** _Owner: theme + site plugins._
    1. Preserve the title links, dates and numbered pagination already in `parts/post-query.html`.
    2. Make sure that topic archives and older stories remain reachable through ordinary `<a href>` links without JavaScript.
    3. Audit the provider behind `[site-search]`, which `templates/search.html` depends on. Add a usable search form and results fallback where necessary.
    4. Make RSS feeds discoverable through visible links and feed metadata.
    5. Audit canonical URLs, redirects and sitemap coverage in the SEO plugin.
    6. Avoid an unlimited crawl space from search filters.
11. **Sources and author identity (proposed; P2).** _Owner: theme patterns + editorial + SEO plugin._ These aids support attribution, not guaranteed citations.
    1. Add an optional story pattern for a short summary, descriptive section headings, source links, and correction notes.
    2. For research stories, link the paper or DOI and researcher profiles.
    3. Use visible captions, image credits, and transcripts for substantive audio or video.
    4. Make sure that each visible author has a stable profile link and a matching schema identity, including guest authors. `ucsc_post_author_link()` supports Co-Authors Plus.
12. **HTML and Markdown consistency (proposed; P2, pairs with the Markdown item above).** _Owner: Cloudflare + site plugins._
    1. Make sure that both formats use the same public content and preserve the same story facts, source links, canonical URL and editorial dates.
    2. For negotiated Markdown, require `Content-Type: text/markdown` and `Vary: Accept`.
    3. Exercise HTML and Markdown requests in both orders through the cache. A normal browser must always receive HTML.
    4. Keep login pages and private previews outside public conversion and caching.
    5. Do not use HTTP cache timestamps as editorial dates.
13. **Machine-readability release checks (proposed; P1).** _Owner: theme + plugin owners._ Use the acceptance checks below. These checks cover output across repositories, not only PHP source.
    1. Add rendered-page integration checks for article, homepage, topic archive, page-two archive, search and 404 responses.
    2. Cover both theme defaults and deployed news templates.
    3. Include JavaScript-disabled HTML and negotiated Markdown.
    4. Exercise missing ACF, absent shortcode providers, and Co-Authors Plus guest authors.
14. **Agent access and referral measurement (proposed; P2).** _Owner: hosting + analytics + editorial._
    1. Establish a baseline for verified crawler requests, status codes, challenges, response time, and AI referral visits.
    2. Compare after releases and policy changes.
    3. Track attributed visits separately from crawler access. Search Console includes Google's AI features in overall Web performance. Do not report it as a separate AI referral channel.
    4. Sample citations manually for representative UCSC topics. Record the prompt and date because answers vary.
15. **Controls browser agents can operate (proposed; P1, pairs with the nav-toggle accessibility item).** _Owner: theme + Truss + search provider._ This work helps agents identify controls and helps visitors navigate.
    1. Give menu buttons accessible names, expanded state, and keyboard behavior.
    2. Label search inputs and submit buttons.
    3. Preserve visible focus and reliable links.
    4. Include these controls in accessibility checks and a browser-agent smoke test.

### Acceptance checks

1. **Content extraction:** An unauthenticated HTML request contains the story title, summary where available, byline, publication date, and full body. One `<main>` contains the page content. One `<article>` identifies the story. Related content and comments remain distinct.
2. **Dates and attribution:** Visible dates use `<time datetime>` with ISO 8601 values. Publication and meaningful update dates have separate labels. Each visible author appears in Yoast's graph. Theme changes must not reset editorial dates. On archives, omit a post-specific modification date unless its meaning is explicit.
3. **Discovery:** A JavaScript-disabled visitor can reach a topic, its second archive page, and an older story through links. The final story URL returns HTTP 200 and has one canonical target. Sitemap URLs match canonical URLs. A news sitemap includes news metadata only for articles from the last two days.
4. **Extraction formats:** HTML and Markdown preserve the same story facts and links. Title, byline and dates survive conversion (see the Markdown item). Each request receives the correct content type after cache hits in either request order.
5. **Schema and controls:** The rendered graph passes syntax and applicable rich-result checks without conflicting theme schema. Navigation and search work with a keyboard. Controls have accessible names. Header fallback links remain useful before Truss loads. Fallback markup must work with Truss hydration and must not duplicate accessible controls.
6. **Access evidence:** Approved search crawlers receive content without a login or bot challenge. Hosting logs establish verified bot access. A local request with a copied user agent is only a preliminary diagnostic.

### Guidance and scope

- [Google: AI features and your website](https://developers.google.com/search/docs/appearance/ai-features) — established search requirements, visible text, internal links, snippet controls, and reporting limits.
- [Google: Article structured data](https://developers.google.com/search/docs/appearance/structured-data/article) — supported article types, editorial dates, images, and author identity.
- [Google: Crawlable links](https://developers.google.com/search/docs/crawling-indexing/links-crawlable) and [news sitemaps](https://developers.google.com/search/docs/crawling-indexing/sitemaps/news-sitemap) — discovery through links and recent-news metadata.
- [OpenAI: crawler controls](https://developers.openai.com/api/docs/bots) — separate search, training, and user-requested access.
- [Cloudflare: Markdown for Agents](https://developers.cloudflare.com/fundamentals/reference/markdown-for-agents/) — content negotiation and cache separation.

**Theme boundary:** presentation, semantics, accessible controls, fallback content, and rendered-output checks belong here. Publisher schema, canonical URLs, feeds and sitemaps belong in persistent site plugins. Bot access belongs in hosting and university policy. Editorial summaries and source links need newsroom ownership. Prioritize the Truss fallback, readable dates, article semantics, release checks, and operable controls for theme work; pair them with the breadcrumb schema, nav accessibility, and performance items. Evaluate `llms.txt` and agent APIs after the public content works reliably.

## Suggested sequencing

1. **Now (one release):** core-pattern removal, shortcode guards, ACF hook/slug fix, undefined theme.json tokens and font-size slugs, and `<time>` markup for the last-modified footer — small and mostly mechanical. Run the auto-fix formatting passes (`composer lint-fix`, `lint:style --fix`) as their own commits, and add the PR lint workflow so it stays clean.
2. **Next (dedicated effort):** mobile menu Phase 2 together with the nav-toggle accessibility and null-safety items (one component, one test pass); breadcrumb structured data; operable controls for browser agents.
3. **SEO settings and plugins:** Yoast descriptions and publisher identity; schema and sitemap audit.
4. **Then:** build tooling (Sass `@use` migration → wp-scripts/prettier → husky/Node), then the ACF block modernization, dead-JS cleanup, and the performance items.
5. **Agent visibility:** server-rendered Truss fallback, readable dates, article semantics, and release checks (P1); archives, sources, format consistency, and measurement (P2).
6. **Ongoing:** fold P3 cleanup into PRs that touch nearby files.
7. **Cross-repo:** raise the shared items with the Truss, `ucsc-custom-functionality`, `ucsc-blocks`, `ucsc-primary-sites`, hosting, and newsroom owners.

## Done

1. **Escaped the ACF subtitle field and stopped empty subtitles from rendering.** `ucsc_post_subtitle()` now wraps the field in `esc_html()` (closes the stored-XSS finding), initializes `$subtitle` so it's defined when ACF is inactive, tests with `!empty()` so unset fields no longer append an empty `.post-subtitle` div, and drops the unused `global $post` — `f3f8529`, fixes [issue 405](https://github.com/ucsc/ucsc-2022/issues/405). The remaining filter-ordering point moved to Performance.
2. **Repaired the malformed template and template-part JSON** broken by the v4.3.4 find/replace — `b565ae3` · v5.0.0. **Deployment caveat:** sites where the header part was ever saved in the Site Editor keep a database copy of the broken markup that overrides the fixed file. Those sites need **Appearance → Editor → Template Parts → header → Clear customizations**. Include this in release notes.
3. **Mobile menu Phase 1:** the menu scrolls and closes reliably; panel anchored to the header's measured bottom edge; body-lock width fix; header lifted above Truss internals while open. Fixes [issue 355](https://github.com/ucsc/ucsc-2022/issues/355), [issue 393](https://github.com/ucsc/ucsc-2022/issues/393), [issue 394](https://github.com/ucsc/ucsc-2022/issues/394) — `7d4833f` ([PR 401](https://github.com/ucsc/ucsc-2022/pull/401)) · v5.0.0.
4. **Fixed the broken `path` argument in the block-style loader**, so small per-block stylesheets are inlined again and get RTL support — `638db61` · v5.0.0. Optional follow-up: `'ver' => filemtime($path)` for per-block cache busting.
5. **Restored PHP linting** (WPCS 3.3.0) so the security sniffs actually run — `44b7955` · v5.0.0.
6. **Stylelint can now lint Sass files** — `d2ea0ef` · v5.0.0.
7. **Theme CSS and `theme.js` versioned by file contents**, so rebuilt assets aren't served from stale caches — `7d4833f` ([PR 401](https://github.com/ucsc/ucsc-2022/pull/401)) · v5.0.0.
8. **License set to GPL-2.0-or-later everywhere** — `9c76e23` · v5.0.0.
9. **Cover block background videos:** play/pause and captions controls, reduced-motion support, styling refinements; captions button appears only when a captions file exists — `79bdb99` … `13ac382` · v5.0.0–v5.2.0.
10. **Dashboard auto-update library runs only under specific conditions** — `14a15a6` · v5.2.0.
11. **Truss loads in the `<head>` from `truss.ucsc.edu` with a `preconnect` hint** — `5960f2f`, `b9dc62f` · PR 404.
12. **`theme.json` upgraded to schema v3** (closes [issue 345](https://github.com/ucsc/ucsc-2022/issues/345)); fixed the `serif serif` font stack; removed the rule for the non-existent `core/block-post` block; removed "(Do not use) Page no title with breadcrumbs" from the template picker — `3b865ee`, `5c5b41f` · PR 404. Generated CSS confirmed identical before and after.
13. **Replaced the footer curves** ([issue 403](https://github.com/ucsc/ucsc-2022/issues/403)) — `431f692` · PR 404.
14. **ACF blocks declare ACF Blocks V3.** `blocks/breadcrumbs/block.json` and `blocks/main-nav/block.json` set `"acf": { "blockVersion": 3 }`, so they work in the WP 7.1 iframe editor without relying on ACF 6.8.9's default. This is ACF's own block version, not the WordPress `apiVersion` field — `480f92a`.
15. **Removed the unregistered ACF block style handles and the stray `echo`.** `acf/breadcrumb-styles` and `acf/main-nav-styles` are gone from `blocks/*/block.json`, and `blocks/main-nav/index.php` no longer `echo`es `wp_nav_menu()`. The hamburger toggle also gained a server-rendered `aria-label` — `52d3ad3`, `6ff650d`.
