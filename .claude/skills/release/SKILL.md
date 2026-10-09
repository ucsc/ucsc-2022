---
name: release
description: Cut a new ucsc-2022 theme release with commit-and-tag-version and push the tag to trigger the GitHub release build.
disable-model-invocation: true
---

Cut a release. Optional argument: `$ARGUMENTS` — a release type (`patch`, `minor`, `major`) or `--prerelease rc`. With no argument, let commit-and-tag-version infer the bump from commit types.

1. Preflight — stop and report if any fail:
   - Working tree is clean (`git status --porcelain` empty).
   - On `main` and up to date: `git checkout main && git pull --ff-only origin main`.
   - `npm run build` succeeds and `npm run lint:style` / `npm run lint:js` / `composer lint` pass (report lint failures; ask before continuing).
2. Show the commits since the last tag (`git log $(git describe --tags --abbrev=0)..HEAD --oneline`) and the bump commit-and-tag-version would make: `npx commit-and-tag-version --dry-run` (plus `--release-as <type>` if an argument was given, or `--prerelease rc`).
3. Ask the user to confirm the version and changelog.
4. Run `npm run release` with the same flags (`npm run release -- --release-as minor`). This bumps `package.json`, `package-lock.json`, and the `Version:` header in `style.css`, updates `CHANGELOG.md`, commits `chore(release): X.Y.Z`, and tags `vX.Y.Z`.
5. Verify `style.css` and `package.json` show the same new version, then ask before pushing.
6. `git push --follow-tags origin main`. The `v*.*.*` tag triggers `.github/workflows/release.yml`, which builds and attaches the zip.
7. Report the new version and the Actions URL: `gh run list --workflow=release.yml --limit 1`.
