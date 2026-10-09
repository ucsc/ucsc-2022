---
name: add-block-style
description: Add or change styles for a core/third-party block in the ucsc-2022 theme — per-block stylesheets in wp-blocks/, block style variations (is-style-*), or block variations. Use whenever styling a specific block or adding a new "Styles" option in the editor.
---

Block-specific styling in this theme is split across three places. Missing any one silently does nothing.

## 1. Stylesheet: `wp-blocks/<block-name>.css`

- File name is the part of the block name after the slash: `core/quote` → `wp-blocks/quote.css`, `outermost/icon-block` → `wp-blocks/icon-block.css`.
- Plain CSS — **not** compiled by webpack, so no Sass. Use `theme.json` presets (`var(--wp--preset--spacing--30)`, `var(--wp--preset--color--...)`, `var(--wp--preset--font-size--...)`) rather than hard-coded values.
- Tabs for indentation; run `npm run lint:style` on it.

## 2. Register the block in `functions.php`

Add the block name to the `$styled_blocks` array inside `ucsc_setup()`. The loop calls `wp_enqueue_block_style()` with handle `ucsc-<block-name>`, so the CSS only loads on pages containing that block. If the file already exists, the block is already listed — just edit the CSS.

## 3. Editor style option: `wp-blocks/styles.js` (only for a new style variation)

Add inside the existing `wp.domReady()` callback, next to the other entries for that block:

```js
wp.blocks.registerBlockStyle( 'core/<block>', {
	name: 'ucsc-<style-name>',
	label: '<Label shown in editor>',
	style_handle: 'ucsc-<block-name>',
} );
```

- WordPress adds class `is-style-<name>` to the block; target that class in the CSS from step 1.
- Prefix new style names with `ucsc-` (older `core/details` and icon-block styles predate this).
- Block *variations* (`wp.blocks.registerBlockVariation`) go in the same file.

## Not this skill

- Site-wide/global styles → `theme.json` (`styles.blocks.<name>`) or `src/scss/` (then `npm run build`).
- Styles tied to a pattern → `src/scss/block-patterns/`.
