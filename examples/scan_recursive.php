<?php
/**
 * Exemplo do uso de pht\fs\scan_recursive()
 * 
 * @see ptk\fs\scan_recursive()
 */
require './vendor/autoload.php';

print_r(\ptk\fs\scan_recursive('./docs'));