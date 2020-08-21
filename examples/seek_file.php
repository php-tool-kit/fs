<?php

use function ptk\fs\seek_file;
/**
 * Exemplo do uso de pht\fs\seek_file()
 * 
 * @see ptk\fs\seek_file()
 */
require './vendor/autoload.php';

$handle = fopen('./examples/sample1.txt', 'r');
print_r(seek_file($handle, '/osborn/i'));
fclose($handle);