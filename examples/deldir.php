<?php

use function ptk\fs\deldir;
use function ptk\fs\join_path;
use function ptk\fs\scan_recursive;
/**
 * Exemplo do uso de pht\fs\deldir()
 * 
 * @see ptk\fs\deldir()
 */
require './vendor/autoload.php';

$path = './examples/remove_this/';

mkdir($path);

mkdir(join_path($path, 'subdir'));

copy('./README.md', join_path($path, 'README.md'));
copy('./LICENSE', join_path($path, 'subdir', 'LICENSE'));

print_r(scan_recursive($path));

deldir($path);

var_dump(file_exists($path));