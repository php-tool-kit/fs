<?php
/**
 * Exemplo do uso de pht\fs\slashes()
 * 
 * @see ptk\fs\slashes()
 */
require './vendor/autoload.php';

echo \ptk\fs\slashes('/mnt\c\filename.txt'), PHP_EOL;
// /mnt/c/filename.txt

echo \ptk\fs\slashes('/mnt\c\dirname', true), PHP_EOL;
// /mnt/c/dirname/