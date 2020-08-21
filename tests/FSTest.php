<?php

use PHPUnit\Framework\TestCase;
use function ptk\fs\deldir;
use function ptk\fs\get_extension;
use function ptk\fs\get_only_dir;
use function ptk\fs\get_only_files;
use function ptk\fs\join_path;
use function ptk\fs\scan;
use function ptk\fs\scan_recursive;
use function ptk\fs\slashes;

class FSTest extends TestCase
{

    public function testJoinPath()
    {
        $pieces = ['', 'mnt\c', 'filename.txt'];
        $this->assertEquals(join(DIRECTORY_SEPARATOR, $pieces), join_path(...$pieces));
    }

    public function testSlashes()
    {

        $this->assertEquals(join(DIRECTORY_SEPARATOR, ['', 'dir', 'dir', 'dir', 'file.txt']), slashes('\\dir\dir/dir/file.txt'));

        $this->assertEquals(join(DIRECTORY_SEPARATOR, ['', 'dir', 'dir', 'dir', '']), slashes('\\dir\dir/dir', true));
    }

    public function testScan()
    {
        $path = realpath('./tests/assets/');
        $path = slashes($path, true);
        $expected = [
            $path . 'file.txt',
            $path . 'subdir',
            $path . 'subdir2'
        ];

        $this->assertEquals($expected, scan($path));
    }

    public function testScanFail()
    {
        $this->expectException(Exception::class);
        scan('unknow');
    }

    public function testScanRecursive()
    {
        $path = realpath('./tests/assets/');
        $path = slashes($path, true);
        $expected = [
            $path . 'file.txt',
            $path . 'subdir',
            $path . 'subdir/file.txt',
            $path . 'subdir2'
        ];

        $this->assertEquals($expected, scan_recursive($path));
    }

    public function testScanRecursiveFail()
    {
        $this->expectException(Exception::class);
        scan_recursive('unknow');
    }

    public function testGetOnlyFiles()
    {
        $path = realpath('./tests/assets/');
        $path = slashes($path, true);
        $expected = [
            $path . 'file.txt',
            $path . 'subdir/file.txt'
        ];

        $this->assertEquals($expected, get_only_files(scan_recursive($path)));
    }

    public function testGetOnlyDir()
    {
        $path = realpath('./tests/assets/');
        $path = slashes($path, true);
        $expected = [
            $path . 'subdir',
            $path . 'subdir2'
        ];

        $this->assertEquals($expected, get_only_dir(scan_recursive($path)));
    }

    public function testDelDir()
    {
        $path = './tests/assets/remove_this/';

        mkdir($path);

        mkdir(join_path($path, 'subdir'));

        copy('./README.md', join_path($path, 'README.md'));
        copy('./LICENSE', join_path($path, 'subdir', 'LICENSE'));

        $this->assertDirectoryExists($path);
        
        deldir($path);
        
        $this->assertDirectoryDoesNotExist($path);
    }
    
    public function testDelDirFail()
    {
        $this->expectException(Exception::class);
        deldir('unknow');
    }
    
    public function testGetExtension()
    {
        $this->assertEquals('txt', get_extension('unknow.txt'));
        $this->assertEquals('', get_extension('noextension'));
    }
}
