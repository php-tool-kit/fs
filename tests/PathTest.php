<?php

/**
 * Prooph was here at `%package%` in `%year%`! Please create a .docheader in the project root and run `composer cs-fix`
 */

declare(strict_types=1);

namespace PTK\FS\Test;

use PHPUnit\Framework\TestCase;
use PTK;
use PTK\FS\Directory;
use PTK\FS\Exception\FSException;
use PTK\FS\Exception\NodeNotFoundException;
use PTK\FS\Path;

class PathTest extends TestCase
{
    protected array $sampleDir = ['tests', 'cache', 'subdir'];
    protected array $sampleFile = ['tests', 'cache', 'subdir', 'sample.file'];

    public function testConstructorDirectory()
    {
        $path = new Path(...$this->sampleDir);
        $this->assertInstanceOf(Path::class, $path);
        $this->assertEquals(\implode(DIRECTORY_SEPARATOR, $this->sampleDir) . DIRECTORY_SEPARATOR, $path->getPath());
    }

    public function testConstructorFile()
    {
        $path = new Path(...$this->sampleFile);
        $this->assertInstanceOf(Path::class, $path);
        $this->assertEquals(\implode(DIRECTORY_SEPARATOR, $this->sampleFile), $path->getPath());
    }

    public function testNotExists()
    {
        $path = new Path(...$this->sampleFile);
        $this->assertFalse($path->exists());
    }

    public function testExists()
    {
        $path = new Path(__FILE__);
        $this->assertTrue($path->exists());
    }

    public function testGetExtensionSuccess()
    {
        $path = new Path(...$this->sampleFile);
        $this->assertEquals('file', $path->getExtension());
    }

    public function testGetExtensionFromDirPathFails()
    {
        $path = new Path(...$this->sampleDir);
        $this->expectException(FSException::class);
        $path->getExtension();
    }

    public function testGetExtensionFromProblematicPathFails()
    {
        $path = new Path('this/is/problematic.path/file.ext');
        $this->expectException(FSException::class);
        $path->getExtension();
    }

    public function testIsDir()
    {
        $path = new Path(...$this->sampleDir);
        $this->assertTrue($path->isDir());
    }

    public function testIsNotDir()
    {
        $path = new Path(...$this->sampleFile);
        $this->assertFalse($path->isDir());
    }

    public function testIsFile()
    {
        $path = new Path(...$this->sampleFile);
        $this->assertTrue($path->isFile());
    }

    public function testIsNotFile()
    {
        $path = new Path(...$this->sampleDir);
        $this->assertFalse($path->isFile());
    }

    public function testGetPath()
    {
        $path = new Path(...$this->sampleDir);
        $this->assertEquals(\implode(DIRECTORY_SEPARATOR, $this->sampleDir) . DIRECTORY_SEPARATOR, $path->getPath());
    }

    public function testGetRealPathSuccess()
    {
        $path = new Path(__FILE__);
        $this->assertEquals(\realpath(__FILE__), $path->getRealPath());
    }

    public function testGetRealPathFails()
    {
        $path = new Path('this/is/problematic.path/file.ext');
        $this->expectException(NodeNotFoundException::class);
        $path->getRealPath();
    }

    public function testDirCreation()
    {
        $path = new Path(...$this->sampleDir);
        $this->assertInstanceOf(Directory::class, $path->create());
    }

    public function testfileCreation()
    {
        $path = new Path(...$this->sampleFile);
        $this->assertInstanceOf(PTK\FS\File::class, $path->create());
    }
}
