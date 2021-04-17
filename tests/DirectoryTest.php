<?php

/**
 * Prooph was here at `%package%` in `%year%`! Please create a .docheader in the project root and run `composer cs-fix`
 */

declare(strict_types=1);

namespace tests;

use PHPUnit\Framework\TestCase;
use PTK\FS\Directory;
use PTK\FS\Exception\NodeNotFoundException;

class DirectoryTest extends TestCase
{
    protected string $cacheDir = 'tests/cache/';

    public function testCreationSuccess()
    {
        $dir = new Directory($this->cacheDir);
        $this->assertInstanceOf(Directory::class, $dir);
    }

    public function testCreationFails()
    {
        $this->expectException(NodeNotFoundException::class);
        $dir = new Directory('not_exists_dir');
    }

    public function testCreateDirExists()
    {
        $dirpath = $this->cacheDir . 'test/';
        $dir = Directory::create($dirpath);
        $this->assertInstanceOf(Directory::class, $dir);
        $this->assertTrue(\is_dir($dirpath));
    }

    public function testCreateDirNotExists()
    {
        $dirpath = $this->cacheDir . 'test1/';
        @\rmdir($dirpath);
        $dir = Directory::create($dirpath);
        $this->assertInstanceOf(Directory::class, $dir);
        $this->assertTrue(\is_dir($dirpath));
    }

    public function testGetParent()
    {
        $dirpath = $this->cacheDir . 'test/';
        $dir = Directory::create($dirpath);
        $this->assertEquals(\realpath(\dirname($dirpath)), $dir->getParent());
    }

    public function testGetDirPath()
    {
        $dirpath = $this->cacheDir . 'test/';
        $dir = Directory::create($dirpath);
        $this->assertEquals(\realpath($dirpath), $dir->getDirPath());
    }

    public function testListAllNotRecursive()
    {
        $dirpath = $this->cacheDir . 'test/';
        $subdir1 = $dirpath . 'subdir1/';
        $subdir2 = $dirpath . 'subdir2/';
        $file1 = $dirpath . 'file1.txt';
        $file2 = $dirpath . 'file2.txt';
        $file3 = $dirpath . 'file3.txt';
        $file11 = $subdir1 . 'file11.txt';
        $file12 = $subdir1 . 'file12.txt';
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
            \realpath($subdir1),
            \realpath($subdir2),
        ];

        $this->assertEquals($expected, $dir->list());
    }

    public function testListOnlyFilesNotRecursive()
    {
        $dirpath = $this->cacheDir . 'test/';
        $subdir1 = $dirpath . 'subdir1/';
        $subdir2 = $dirpath . 'subdir2/';
        $file1 = $dirpath . 'file1.txt';
        $file2 = $dirpath . 'file2.txt';
        $file3 = $dirpath . 'file3.txt';
        $file11 = $subdir1 . 'file11.txt';
        $file12 = $subdir1 . 'file12.txt';
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
        ];

        $this->assertEquals($expected, $dir->list(Directory::LIST_FILES));
    }

    public function testListOnlyDirsNotRecursive()
    {
        $dirpath = $this->cacheDir . 'test/';
        $subdir1 = $dirpath . 'subdir1/';
        $subdir2 = $dirpath . 'subdir2/';
        $file1 = $dirpath . 'file1.txt';
        $file2 = $dirpath . 'file2.txt';
        $file3 = $dirpath . 'file3.txt';
        $file11 = $subdir1 . 'file11.txt';
        $file12 = $subdir1 . 'file12.txt';
        $dir = new Directory($dirpath);
        @\mkdir($subdir1);
        @\mkdir($subdir2);
        \file_put_contents($file1, 'teste');
        \file_put_contents($file2, 'teste');
        \file_put_contents($file3, 'teste');
        \file_put_contents($file11, 'teste');
        \file_put_contents($file12, 'teste');

        $expected = [
            \realpath($subdir1),
            \realpath($subdir2),
        ];

        $this->assertEquals($expected, $dir->list(Directory::LIST_DIR));
    }

    public function testDeleteNotRecursive()
    {
        $dirpath = $this->cacheDir . 'test/';
        $subdir1 = $dirpath . 'subdir1/';
        $subdir2 = $dirpath . 'subdir2/';
        $file1 = $dirpath . 'file1.txt';
        $file2 = $dirpath . 'file2.txt';
        $file3 = $dirpath . 'file3.txt';
        $file11 = $subdir1 . 'file11.txt';
        $file12 = $subdir1 . 'file12.txt';
        $dir = new Directory($dirpath);
        @\mkdir($subdir1);
        @\mkdir($subdir2);
        \file_put_contents($file1, 'teste');
        \file_put_contents($file2, 'teste');
        \file_put_contents($file3, 'teste');
        \file_put_contents($file11, 'teste');
        \file_put_contents($file12, 'teste');

        $expected = [
            \realpath($subdir1),
        ];

        $this->assertTrue($dir->delete());
        $this->assertEquals($expected, $dir->list());
    }

    public function testListAllRecursive()
    {
        $dirpath = $this->cacheDir . 'test/';
        $subdir1 = $dirpath . 'subdir1/';
        $subdir2 = $dirpath . 'subdir2/';
        $file1 = $dirpath . 'file1.txt';
        $file2 = $dirpath . 'file2.txt';
        $file3 = $dirpath . 'file3.txt';
        $file11 = $subdir1 . 'file11.txt';
        $file12 = $subdir1 . 'file12.txt';
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
            \realpath($subdir1),
            \realpath($file11),
            \realpath($file12),
            \realpath($subdir2),
        ];

        $this->assertEquals($expected, $dir->recursive()
            ->list());
    }

    public function testListOnlyDirsRecursive()
    {
        $dirpath = $this->cacheDir . 'test/';
        $subdir1 = $dirpath . 'subdir1/';
        $subdir2 = $dirpath . 'subdir2/';
        $file1 = $dirpath . 'file1.txt';
        $file2 = $dirpath . 'file2.txt';
        $file3 = $dirpath . 'file3.txt';
        $file11 = $subdir1 . 'file11.txt';
        $file12 = $subdir1 . 'file12.txt';
        $dir = new Directory($dirpath);
        @\mkdir($subdir1);
        @\mkdir($subdir2);
        \file_put_contents($file1, 'teste');
        \file_put_contents($file2, 'teste');
        \file_put_contents($file3, 'teste');
        \file_put_contents($file11, 'teste');
        \file_put_contents($file12, 'teste');

        $expected = [
            \realpath($subdir1),
            \realpath($subdir2),
        ];

        $this->assertEquals($expected, $dir->recursive()
            ->list(Directory::LIST_DIR));
    }

    public function testListOnlyFilesRecursive()
    {
        $dirpath = $this->cacheDir . 'test/';
        $subdir1 = $dirpath . 'subdir1/';
        $subdir2 = $dirpath . 'subdir2/';
        $file1 = $dirpath . 'file1.txt';
        $file2 = $dirpath . 'file2.txt';
        $file3 = $dirpath . 'file3.txt';
        $file11 = $subdir1 . 'file11.txt';
        $file12 = $subdir1 . 'file12.txt';
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
            \realpath($file12),
        ];

        $this->assertEquals($expected, $dir->recursive()
            ->list(Directory::LIST_FILES));
    }

    public function testGetRecursiveDirectoryIterator()
    {
        $dir = new Directory(__DIR__);
        $this->assertInstanceOf(\RecursiveDirectoryIterator::class, $dir->recursive()
            ->iterator());
    }

    public function testDeleteRecursive()
    {
        $dirpath = $this->cacheDir . 'test/';
        $subdir1 = $dirpath . 'subdir1/';
        $subdir2 = $dirpath . 'subdir2/';
        $file1 = $dirpath . 'file1.txt';
        $file2 = $dirpath . 'file2.txt';
        $file3 = $dirpath . 'file3.txt';
        $file11 = $subdir1 . 'file11.txt';
        $file12 = $subdir1 . 'file12.txt';
        $dir = new Directory($dirpath);
        @\mkdir($subdir1);
        @\mkdir($subdir2);
        \file_put_contents($file1, 'teste');
        \file_put_contents($file2, 'teste');
        \file_put_contents($file3, 'teste');
        \file_put_contents($file11, 'teste');
        \file_put_contents($file12, 'teste');

        $dir->recursive()->delete();
        $this->assertEquals([], $dir->recursive()
            ->list());
    }

    public function testCopySuccess()
    {
        $dirpath = $this->cacheDir . 'test/';
        $destiny = $this->cacheDir . 'test_copyed/';
        $subdir1 = $dirpath . 'subdir1/';
        $subdir2 = $dirpath . 'subdir2/';
        $file1 = $dirpath . 'file1.txt';
        $file2 = $dirpath . 'file2.txt';
        $file3 = $dirpath . 'file3.txt';
        $file11 = $subdir1 . 'file11.txt';
        $file12 = $subdir1 . 'file12.txt';
        $dir = new Directory($dirpath);
        @\mkdir($subdir1);
        @\mkdir($subdir2);
        \file_put_contents($file1, 'teste');
        \file_put_contents($file2, 'teste');
        \file_put_contents($file3, 'teste');
        \file_put_contents($file11, 'teste');
        \file_put_contents($file12, 'teste');

        $this->assertInstanceOf(Directory::class, $target = $dir->copy($destiny));

        $expected = [
            \realpath($destiny . 'file1.txt'),
            \realpath($destiny . 'file2.txt'),
            \realpath($destiny . 'file3.txt'),
            \realpath($destiny . 'subdir1'),
            \realpath($destiny . 'subdir1/file11.txt'),
            \realpath($destiny . 'subdir1/file12.txt'),
            \realpath($destiny . 'subdir2'),
        ];

        $this->assertEquals($expected, $target->recursive()
            ->list());
    }

    public function testMoveSuccess()
    {
        $dirpath = $this->cacheDir . 'test/';
        $destiny = $this->cacheDir . 'test_copyed/';
        $subdir1 = $dirpath . 'subdir1/';
        $subdir2 = $dirpath . 'subdir2/';
        $file1 = $dirpath . 'file1.txt';
        $file2 = $dirpath . 'file2.txt';
        $file3 = $dirpath . 'file3.txt';
        $file11 = $subdir1 . 'file11.txt';
        $file12 = $subdir1 . 'file12.txt';
        $dir = new Directory($dirpath);
        @\mkdir($subdir1);
        @\mkdir($subdir2);
        \file_put_contents($file1, 'teste');
        \file_put_contents($file2, 'teste');
        \file_put_contents($file3, 'teste');
        \file_put_contents($file11, 'teste');
        \file_put_contents($file12, 'teste');

        $this->assertInstanceOf(Directory::class, $target = $dir->move($destiny));

        $expected = [
            \realpath($destiny . 'file1.txt'),
            \realpath($destiny . 'file2.txt'),
            \realpath($destiny . 'file3.txt'),
            \realpath($destiny . 'subdir1'),
            \realpath($destiny . 'subdir1/file11.txt'),
            \realpath($destiny . 'subdir1/file12.txt'),
            \realpath($destiny . 'subdir2'),
        ];

        $this->assertEquals($expected, $target->recursive()
            ->list());

        $this->assertFalse(\file_exists($dirpath));
    }

    public function testRenameSuccess()
    {
        $dirpath = $this->cacheDir . 'test/';
        $destiny = $this->cacheDir . 'test_copyed/';
        $subdir1 = $dirpath . 'subdir1/';
        $subdir2 = $dirpath . 'subdir2/';
        $file1 = $dirpath . 'file1.txt';
        $file2 = $dirpath . 'file2.txt';
        $file3 = $dirpath . 'file3.txt';
        $file11 = $subdir1 . 'file11.txt';
        $file12 = $subdir1 . 'file12.txt';
        $dir = Directory::create($dirpath);
        @\mkdir($subdir1);
        @\mkdir($subdir2);
        \file_put_contents($file1, 'teste');
        \file_put_contents($file2, 'teste');
        \file_put_contents($file3, 'teste');
        \file_put_contents($file11, 'teste');
        \file_put_contents($file12, 'teste');

        $this->assertInstanceOf(Directory::class, $target = $dir->rename($destiny));

        $expected = [
            \realpath($destiny . 'file1.txt'),
            \realpath($destiny . 'file2.txt'),
            \realpath($destiny . 'file3.txt'),
            \realpath($destiny . 'subdir1'),
            \realpath($destiny . 'subdir1/file11.txt'),
            \realpath($destiny . 'subdir1/file12.txt'),
            \realpath($destiny . 'subdir2'),
        ];

        $this->assertEquals($expected, $target->recursive()
            ->list());

        $this->assertFalse(\file_exists($dirpath));
    }
}
