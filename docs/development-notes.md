---
nav_order: 4
---

# Development Notes

Contributors meet twice weekly. Meeting notes are below. [Goals & milestones are here](https://github.com/ucsc/theme-ucsc/wiki/Goals-and-Milestones).

## Week of 2022-04-11

## Tasks

- [x] News front page structure: remove layout in the theme files

## Notes

## 2022-04-06

### Tasks

- [x] Add subtitle field
- [x] Release beta.5 on Friday.

### Notes

- Released 1.0.0-beta.5. Beta 4 had a bug that we missed. Rather than undo what we did, we opted to release beta 5.
- Beta 5 adds a subtitle field to posts and adds Advanced Custom Fields Pro as a dependency.
- We are now in need of some testing. We might need to create a formal sprint to get testing setup.
- We need automated testing to make sure the theme can be activated and that the front page returns HTML.
- Local testing setup will be important too.

We feel ready for pilot partners. There will be plenty of feedback and changes to come. Now is the time to get people in there and using the theme.

Looking at ways to limit the customization of buttons:

- [x] Border radius
- [ ] Color themes: need to limit what is possible or have preconfigured styles

Customization of block settings seems to be possible with a [similar mechanism to block content filtering](https://developer.wordpress.org/block-editor/reference-guides/filters/block-filters/).

## 2022-03-28

### Tasks for this week

- [x] Pagination link styles
- [x] Advanced Custom Fields (available on CampusPress) to add meta data field for news articles
- [x] FIX: breadcrumbs show up in any loop of posts.

### Notes

- Advanced Custom Fields for news article metadata: **ACF Pro is a minimum requirement** because you can create blocks from ACF fields with the pro version. The user experience is better.
- Still thinking about version bumping in various files when we do a release.
- Pilot partners get their sites next week.

## 2022-03-24

### Notes

- Theme is on WordPress-DEV CP network. On prod next week.
- So far, it looks good with UCSC custom plugin for directory.
- [We cannot give site owners access to modify the theme](https://github.com/ucsc/theme-ucsc/issues/13) within the theme editor
- Modifying the output of Gutenberg blocks to match our semantic HTML structure requires filtering the block
- Still seeing pokey performance on CP WordPress sites.
- We've found a nice, clean, scripted way to reliably generate the theme.zip folder required to make the theme portable using `wp-scripts plugin-zip`
- Block Gap setup is seemingly fixed for now.

## 2022-03-21

### Notes

Deployment looks like this.

1. In theme directory, run `npm run build && composer install`
2. Release `npm run release -- --release-as X.X.X-beta.X`
   - Updates the CHANGELOG
   - Bumps version in NPM package
   - TODO: how to bump version in style.css?
3. Run `npm run zip`

- Added `build` directory to the repo and deploying from Github directly to the CampusPress Bitbucket repo.
- We need to add pre-deploy testing and code formatting

**Custom fields for news articles**

1. `subtitle`
2. `campus_message_to`
3. `campus_message_from`

### Tasks

- [x] Pagination links

## 2022-03-17

- Individual story sharing to social: use a [plugin that modifies the core social links block](https://wordpress.org/plugins/social-sharing-block/). We'll table this for now and come back to it.
- Breadcrumbs: using another plugin, [Hybrid Breadcrumbs](https://github.com/themehybrid/hybrid-breadcrumbs). Test to see if it will work with Truss.
- Questions for the coming week:
  - [x] NO: Should we cement multi-column layouts into the theme on listing pages like news front?

## 2022-03-08

- Reusable blocks are freaking crazy. They can be exported and imported (as json). Can we include them in the theme?
  - Great for footers and headers and other repeated content.

## 2022-03-02

- [x] Rob: PR for setting site title and page titles
- [x] `.alignfull` class is shifted too far left

## 2022-02-25

- Further reduce the redundant template parts files since they aren't necessary for content areas
- CampusPress has our account info for the theme and plugins repo
- CampusPress is still on WP 5.8.3. We need them to upgrade to 5.9 to support our theme.
- [Initial performance test on CP network](https://webpagetest.org/result/220225_AiDcGM_F5W/) is disappointing. We're going to need a huge improvement or news and the homepage will have to live elsewhere.

## 2022-02-16

- [x] Name the two stylesheets properly for their contexts (we were able to use just one sheet for both)
- [x] Test the '_one sass partial per core block_' setup (this should work just fine)

## 2022-02-14

- We made sure the editor enqueues the same sass partials as the front end.
- We will go forward assuming that we will have an individual sass partial file for each core block _only when we need to change it_.
- We learned that `theme.json` has the final say in the style cascade, in both the front and the back end of the site.

## 2022-02-11

- Home page templates wrap the site title in an `<h1>` tag. All other page templates wrap the site title in a `<p>` tag. Why? Because the home page typically doesn't have a discrete `h1`. All other pages do. Also, you are on the home page, so the site title is a logical `h1` when no other `h1` is on the page.
- In WCMS, we have the same problem. We opted for site titles _always_ being in `<p>` tags. That means only home pages don't have a proper `h1` tag, and all sub pages do.

## 2022-02-07

### Notes

- We've entered a re-factoring stage. Important to clean up in prep for the next phase.
- Focus on sass files, templates, and linting sass, PHP, and HTML files.

## 2022-02-04

### Notes

- Release betas with standard-version: `standard-version --release-as 1.0.0-beta.V` where 'V' is the beta number.
- Refactored the header files to pull out the campus page top region into a separate template part.
- Jason found an official way™ 😆 for [modifying block output in PHP before it is rendered](https://wpseek.com/function/render_block_core_site_title/).

### Tasks

- [x] The other header template parts can also be refactored to remove redundant parts
- [x] Need a build script to create the installable theme folder

## 2022-02-02

- Jason organizing and abstracting templates and parts according to the latest block theme standards:
  - Abstract parts out and create sub-templates only when need is obvious (logo display differences between campus home and subsites)
  - Name template abstractions for widest use case
    - ✅ `header-subsite.html` rather than
    - 🛑 `header-department.html`
- First release on 2022-02-04
- Theme release process:
  - `standard-version` locally to bump version and create/update the CHANGELOG
  - Create a zipped folder of the built theme files for WordPress users to download
  - Push to Github and include tags
  - Create a pre-release (title is the tag) and attach working build
  - Save the release (Slack will be notified 🎉)

## 2022-01-31

- **Pull content creation functions from theme**
  - Include a `wordpress.xml` file _along with_ the theme that creates a couple of pages, posts, and menus, and puts them in the correct spots in the theme.
  - Treat this as a first-run experience for new theme users who may or may not be in CampusPress.

## 2022-01-28

- [x] Automatic page "About this theme" (We're changing this)
- [x] Shove footer to bottom
- [x] Remove title from home page template
- [x] Gap in some flexed objects (menus in global header)
- [x] Walk through the front versus back end CSS.

## 2022-01-26

- [x] Deployment strategy
- [x] Sample content (in a WordPress.xml file)
- [x] Individual block theming workflow
- [x] Add a folder for blocks
- [x] One CSS/SCSS file for each block
- [x] Enqueue block styles in functions.php once for each block, so WP knows which custom styles to load on a page.

## 2022-01-21

- Development workflow added to README:
  1. Have Docker running and wp-env installed.
  2. In your themes subfolder, `gh repo clone ucsc/theme-ucsc ucsc`
  3. In the ucsc folder, `npm install && npm run build` to compile theme file
  4. `cd` to root folder and run `wp-env start`
  5. Login (`admin:password`) and navigate to <http://localhost:8888/wp-admin/import.php>

## 2022-01-18

- Whether or not to have specific home page template? Not yet, but can do if it seems necessary
