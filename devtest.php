<?php

use PTK\FS\Directory;

require 'vendor/autoload.php';

$dirpath = "tests/cache/test/";
$subdir1 = $dirpath.'subdir1/';
$subdir2 = $dirpath.'subdir2/';
$file1 = $dirpath.'file1.txt';
$file2 = $dirpath.'file2.txt';
$file3 = $dirpath.'file3.txt';
$file11 = $subdir1.'file11.txt';
$file12 = $subdir1.'file12.txt';
$dir = new Directory($dirpath);
@\mkdir($subdir1);
@\mkdir($subdir2);
\file_put_contents($file1, 'teste');
\file_put_contents($file2, 'teste');
\file_put_contents($file3, 'teste');
\file_put_contents($file11, 'teste');
\file_put_contents($file12, 'teste');

$expected = [
    \realpath($file1),
    \realpath($file2),
    \realpath($file3),
    \realpath($file11),
    \realpath($file12)
];

print_r($expected);
print_r($dir->recursive()->list(Directory::LIST_FILES));