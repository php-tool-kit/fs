<?php

use function ptk\fs\get_extension;
/**
 * Exemplo do uso de pht\fs\get_extension()
 * 
 * @see ptk\fs\get_extension()
 */
require './vendor/autoload.php';

echo get_extension('unknow.txt'), PHP_EOL;
echo get_extension('noextension'), PHP_EOL;