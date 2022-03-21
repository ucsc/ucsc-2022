# Changelog

All notable changes to this project will be documented in this file. See [standard-version](https://github.com/conventional-changelog/standard-version) for commit guidelines.

## [1.0.0-beta.3](https://github.com/ucsc/theme-ucsc/compare/v1.0.0-beta.2...v1.0.0-beta.3) (2022-03-21)


### Features

* :sparkles: Ad meta fields for subtitle, and campus messages; functions to render; styles ([c3e936d](https://github.com/ucsc/theme-ucsc/commit/c3e936d17930d59ae3d4fb22177e1ded87bef70a))
* :sparkles: Added and styled breadcrumbs. First draft. ([d7e9660](https://github.com/ucsc/theme-ucsc/commit/d7e96606cd91408d9151597ab68f899ca40b776a))


### Bug Fixes

* :fire: Remove composer from `.gitignore` ([412ff96](https://github.com/ucsc/theme-ucsc/commit/412ff96536e4e7e04dba447a6ce65b96983132aa))
* 🐛 Typo in front-page classes ([dd6d254](https://github.com/ucsc/theme-ucsc/commit/dd6d2549a55ee37e1a1a3f2cf5f44362545419c2))

## [1.0.0-beta.2](https://github.com/ucsc/theme-ucsc/compare/v1.0.0-beta.1...v1.0.0-beta.2) (2022-03-04)


### Features

* :art: Add Archive Title. New since `WP5.9` ([7e38929](https://github.com/ucsc/theme-ucsc/commit/7e389291e16b50a9ca47011321a094daf6f056b8))
* :art: Add filter function to replace `site-title` `h1` element with `p` element ([11a8cea](https://github.com/ucsc/theme-ucsc/commit/11a8ceaee120c757c7f1edbf0d6f56671cf5aeaf))
* ✨ Initial setup of custom block styles ([ffce7b9](https://github.com/ucsc/theme-ucsc/commit/ffce7b96773c03054f4919bf8f2cc2e330ae5dc3))


### Bug Fixes

* 🐛 Auto-formatting turned off ([9ebed16](https://github.com/ucsc/theme-ucsc/commit/9ebed165468a43ee8215e8b6457ece915a009218))
* 🐛 Ensure all stylesheets are loaded in the editor; fix link styles ([099ae65](https://github.com/ucsc/theme-ucsc/commit/099ae6541098fa9644b635ccf963daad20adf90d))
* 🐛 Last modified date shows created date if page is new ([afd67df](https://github.com/ucsc/theme-ucsc/commit/afd67dfb6944fd9b735b3bdda8b1263e15ddfd53))
* 🐛 Remove right alignment class from search box ([51b4a3d](https://github.com/ucsc/theme-ucsc/commit/51b4a3dff4f6749aeed6a20c834c96a82e93e748))
* 🐛 Set proper heading level on site title ([89459b3](https://github.com/ucsc/theme-ucsc/commit/89459b3a88b7929f78ddfdb272d1d379fd6d9aad))

## [1.0.0-beta.1](https://github.com/ucsc/theme-ucsc/compare/v0.0.1...v1.0.0-beta.1) (2022-02-04)


### Features

* :art: Add custom templates for campus, dept, and div front pages; work on headers ([a21b5fe](https://github.com/ucsc/theme-ucsc/commit/a21b5fe172aecae204b99a638efc4cf797956fd7))
* :art: Beginning of building php theme ([e5ac548](https://github.com/ucsc/theme-ucsc/commit/e5ac5484dbe716b3250f5f131e0ebaec308d43cc))
* :art: Complete abstratcting content templates from primary templates ([6b7f929](https://github.com/ucsc/theme-ucsc/commit/6b7f9291c8a1ec23c5b174ecd87c8800e64be04d))
* :art: Enqueue `Roboto Condensed` font, adjust `site-title` block accordingly ([07a8d7c](https://github.com/ucsc/theme-ucsc/commit/07a8d7c8f016b36dc1e98c5343daa90cca80232b))
* :art: Style `.flourish` class added to title via javascript; add new custom color to `theme.json`; enqueue editor scripts and styles ([d3cac3b](https://github.com/ucsc/theme-ucsc/commit/d3cac3b3721a74b61ec3c2a5257365330f154605))
* :art: Style fullWidth and wideWidth classes so front end matches back end. ([20ec276](https://github.com/ucsc/theme-ucsc/commit/20ec2768a92dbb923356f1adbdd77397dc83c335))
* :art: Working on header Page Top. Basic structure. ([8cc88ca](https://github.com/ucsc/theme-ucsc/commit/8cc88cad553ceb87f0836cb401ab488f250866ee))
* :art: Write basic query for `index.html`, which is the default `Posts` template ([9fac26b](https://github.com/ucsc/theme-ucsc/commit/9fac26b570453f73d27541c34254d719cbdc0120))
* :construction: added page-left and page-right sidebar template stubs ([f200407](https://github.com/ucsc/theme-ucsc/commit/f200407761390d5c6eabc989ad2b792dd934517a))
* :construction: WIP create "About Theme" page upon activation ([5bbacc2](https://github.com/ucsc/theme-ucsc/commit/5bbacc2ee3edb8e128cf33670df632d9c7a3aa30))
* :sparkles: Add basic `front-page.html` template based on `page.html` ([d93538a](https://github.com/ucsc/theme-ucsc/commit/d93538a0615e832a2c70683ffaefb3cf5c555282))
* :sparkles: Automatically generate footer menu upon theme activation ([529a834](https://github.com/ucsc/theme-ucsc/commit/529a834a949abb19acc002cb234b6b6f7f5bdc99))
* :sparkles: create local `webpack.config.js` that splits the output into `css` and `js`; enqueued javascript ([5c73bd6](https://github.com/ucsc/theme-ucsc/commit/5c73bd64c3159cea3675f100f8d4b91a57cff88a))
* :sparkles: Wrote "Last Modified" shortcode to be included in footer. WIP. ([dc26e4d](https://github.com/ucsc/theme-ucsc/commit/dc26e4dba52205385d417ece2fd4e9a6c70f8e78))


### Bug Fixes

* :bug: fix `clearfix`; did it wrong first time ([95fbf58](https://github.com/ucsc/theme-ucsc/commit/95fbf583d353accfc106d46beb8dbfe66dc2d6e4))
* :bug: fix overflow ([c556fcd](https://github.com/ucsc/theme-ucsc/commit/c556fcd343df080d49c894d6efc7c6e653cb3d54))
* 🐛 Fix broken Google Fonts URL for Roboto ([4b1a233](https://github.com/ucsc/theme-ucsc/commit/4b1a233c746d4f4d6dd054cbf51c3ce5d0b684ba))
* 🐛 One more template part that needed 'theme-ucsc' folder declaration removed ([18671b2](https://github.com/ucsc/theme-ucsc/commit/18671b2c01562267c4dac58101082f07ae4376fd))
* 🐛 Remove theme folder declaration from template files ([18f18d6](https://github.com/ucsc/theme-ucsc/commit/18f18d684e739f38457f3996328ca64c3b68d071))

### 0.0.1 (2021-11-18)
