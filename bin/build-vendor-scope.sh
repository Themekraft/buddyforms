#!/usr/bin/env bash
#
# Rebuilds vendor-scope/buddyforms/vendor: the Guzzle and reCAPTCHA libraries BuddyForms bundles,
# prefixed with the `tk\` namespace by php-scoper (configuration in scoper.inc.php).
#
# Usage (from the plugin root, needs Docker and curl):
#   bin/build-vendor-scope.sh            install the versions pinned in vendor-scope/buddyforms/composer.lock
#   bin/build-vendor-scope.sh --update   update the lock first (newest versions that support PHP 7.4)
#
set -euo pipefail

PHP_SCOPER_VERSION="0.18.20"
PHP_IMAGE="php:8.4-cli"
COMPOSER_IMAGE="composer:2"

root="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
scope_dir="vendor-scope/buddyforms"
work="$(mktemp -d)"
trap 'rm -rf "$work"' EXIT

composer_cmd="install"
if [[ "${1:-}" == "--update" ]]; then
	composer_cmd="update"
fi

run() {
	local image="$1"
	shift
	docker run --rm -u "$(id -u):$(id -g)" -e COMPOSER_HOME=/tmp/composer -v "$work:/work" -w /work "$image" "$@"
}

mkdir -p "$work/$scope_dir" "$work/tools"
cp "$root/scoper.inc.php" "$work/"
cp "$root/$scope_dir/composer.json" "$work/$scope_dir/"
if [[ -f "$root/$scope_dir/composer.lock" ]]; then
	cp "$root/$scope_dir/composer.lock" "$work/$scope_dir/"
fi

echo "==> composer $composer_cmd"
run "$COMPOSER_IMAGE" composer "$composer_cmd" --working-dir="$scope_dir" --no-dev --prefer-dist --no-interaction --no-progress

echo "==> php-scoper $PHP_SCOPER_VERSION"
curl -fsSL -o "$work/tools/php-scoper.phar" "https://github.com/humbug/php-scoper/releases/download/$PHP_SCOPER_VERSION/php-scoper.phar"
run "$PHP_IMAGE" php tools/php-scoper.phar add-prefix --config=scoper.inc.php --output-dir=out --no-interaction --force

echo "==> composer dump-autoload"
cp "$work/$scope_dir/composer.lock" "$work/out/"
run "$COMPOSER_IMAGE" composer dump-autoload --working-dir=out --no-dev --optimize --no-interaction

# Composer loads each autoloaded file once per request, keyed by an identifier derived from the
# package name and path. The scoped copies keep the identifiers of the original packages, so a
# plugin with an unscoped Guzzle loaded first would make Composer skip ours (and the other way
# round). Give the prefixed files their own identifiers. The global polyfills keep theirs: they
# define the same guarded global functions as any other copy.
echo "==> unique identifiers for scoped autoload files"
cat > "$work/ids.php" <<'PHP'
<?php
$dir    = 'out/vendor/composer';
$shared = array( '/symfony/polyfill-php80/bootstrap.php', '/ralouphie/getallheaders/src/getallheaders.php' );
$vendorDir = dirname( __DIR__ . '/' . $dir );
$files  = require $dir . '/autoload_files.php';
$map    = array();
foreach ( $files as $id => $path ) {
	$relative = substr( $path, strlen( $vendorDir ) );
	if ( ! in_array( $relative, $shared, true ) ) {
		$map[ "'" . $id . "'" ] = "'tk_" . $id . "'";
	}
}
foreach ( array( 'autoload_files.php', 'autoload_static.php' ) as $file ) {
	$contents = file_get_contents( "$dir/$file" );
	file_put_contents( "$dir/$file", strtr( $contents, $map ) );
}
PHP
run "$PHP_IMAGE" php ids.php

# BuddyForms loads vendor/autoload.php. scoper-autoload.php is not used (and its aliases point
# at tk\ names the global polyfills do not have), and installed.php would make Composer's
# runtime API (InstalledVersions) report our scoped packages as installed to other plugins.
rm -f "$work/out/vendor/scoper-autoload.php" "$work/out/vendor/composer/installed.php"

echo "==> copy into $scope_dir"
rm -rf "${root:?}/$scope_dir/vendor"
cp -R "$work/out/vendor" "$root/$scope_dir/vendor"
cp "$work/$scope_dir/composer.lock" "$root/$scope_dir/composer.lock"

echo "Done. Review the diff of $scope_dir and commit it (the vendor folder is gitignored: git add -f)."
