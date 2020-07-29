<?php
/**
 * Exemplo do uso de pht\fs\get_only_files()
 * 
 * @see ptk\fs\get_only_files()
 */
require './vendor/autoload.php';

print_r(\ptk\fs\get_only_files(\ptk\fs\scan_recursive('./vendor')));