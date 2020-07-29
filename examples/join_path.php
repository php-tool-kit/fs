<?php
/**
 * Exemplo do uso de pht\fs\join_path()
 * 
 * @see ptk\fs\join_path()
 */
require './vendor/autoload.php';

echo \ptk\fs\join_path('', 'mnt/c', 'filename.txt'), PHP_EOL;
// /mnt/c/filename.txt