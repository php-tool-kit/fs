<?php
/**
 * Exemplo do uso de pht\fs\get_only_dir()
 * 
 * @see ptk\fs\get_only_dir()
 */
require './vendor/autoload.php';

print_r(\ptk\fs\get_only_dir(\ptk\fs\scan_recursive('./vendor')));