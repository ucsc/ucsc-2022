#!/usr/bin/env bash
# PostToolUse hook: run Prettier on an edited JS/CSS/SCSS/JSON file, but only if
# the committed version was already Prettier-clean (or the file is new). Keeps
# edits to not-yet-formatted legacy files from becoming whole-file reformats.
set -u
root="$(cd "$(dirname "$0")/../.." && pwd)"
f="$(jq -r '.tool_response.filePath // .tool_input.file_path // empty')"
[ -n "$f" ] && [ -f "$f" ] || exit 0
case "$f" in
	"$root"/node_modules/* | "$root"/vendor/* | "$root"/build/*) exit 0 ;;
	"$root"/*.js | "$root"/*.scss | "$root"/*.css | "$root"/*.json) ;;
	*) exit 0 ;;
esac
rel="${f#"$root"/}"
prettier="$root/node_modules/.bin/prettier"
[ -x "$prettier" ] || exit 0
cd "$root" || exit 0
if git cat-file -e "HEAD:$rel" 2>/dev/null; then
	git show "HEAD:$rel" | "$prettier" --check --stdin-filepath "$rel" >/dev/null 2>&1 || exit 0
fi
"$prettier" --write "$rel"
