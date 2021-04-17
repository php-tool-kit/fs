<?php

/**
 * Prooph was here at `%package%` in `%year%`! Please create a .docheader in the project root and run `composer cs-fix`
 */

declare(strict_types=1);

/*
 * The MIT License
 *
 * Copyright 2021 Everton.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace PTK\FS\Test;

use Exception;
use PHPUnit\Framework\TestCase;
use PTK\FS\Exception\NodeAlreadyExistsException;
use PTK\FS\Exception\NodeInaccessibleException;
use PTK\FS\Exception\NodeNotFoundException;
use PTK\FS\Exception\NotReadableException;
use PTK\FS\Exception\NotWriteableException;
use PTK\FS\File;

/**
 * Testes da classe \PTK\FS\File
 *
 * @author Everton
 */
class FileTest extends TestCase
{
    protected string $testCache = 'tests/cache/';

    protected string $sampleTextString = 'Linha 1'
            . 'Linha 2'
            . 'Linha3';

    protected array $sampleTextArray = ['Linha 1', 'Linha 2', 'Linha 3'];

    protected static function delDir(string $path): bool
    {
        if (! \is_dir($path)) {
            throw new Exception("$path não é um diretório ou não existe.");
        }

        $files = \array_diff(\scandir($path), ['.', '..']);
        foreach ($files as $file) {
            $target = \implode(DIRECTORY_SEPARATOR, [$path, $file]);
            (\is_dir($target)) ? self::delDir($target) : \unlink($target);
        }

        return \rmdir($path);
    }

    protected function setUp(): void
    {
        self::delDir($this->testCache);
        \mkdir($this->testCache, 0777, true);
    }

    public function testCreationSuccess()
    {
        $filename = $this->testCache . 'test.txt';

        $file = File::create($filename);

        $this->assertInstanceOf(File::class, $file);
        $this->assertTrue(\file_exists($filename));
    }

    public function testCreationOverWriteFails()
    {
        $filename = $this->testCache . 'test.txt';
        $file = File::create($filename);

        $this->expectException(NodeAlreadyExistsException::class);
        $file = File::create($filename);
    }

    public function testInstanceCreation()
    {
        $filename = $this->testCache . 'test.txt';

        $file = File::create($filename);
        $file = new File($filename);

        $this->assertInstanceOf(File::class, $file);
    }

    public function testInstanceCreationOfInexistentFile()
    {
        $path = 'inexistent.txt';

        $this->expectException(NodeNotFoundException::class);
        $file = new File($path);
    }

    public function testOpenSuccess()
    {
        $filename = $this->testCache . 'test.txt';
        $file = File::create($filename);
        $this->assertInstanceOf(File::class, $file->open(File::MODE_READ));
    }

    /*public function testOpenFails()
    {
        $filename = $this->testCache.'test.txt';
        $file = File::create($filename);
        flock(fopen($filename, 'w'), LOCK_EX, true);
        $this->expectException(NodeInaccessibleException::class);
        $file->open(File::MODE_READ_TRUNCATE);
    }*/

    public function testGetOpenMode()
    {
        $filename = $this->testCache . 'test.txt';
        $file = File::create($filename);
        $file->open(File::MODE_READ);
        $this->assertEquals(File::MODE_READ, $file->getOpenMode());
    }

    public function testGetFilePath()
    {
        $filename = $this->testCache . 'test.txt';
        $file = File::create($filename);
        $this->assertEquals(\realpath($filename), $file->getFilePath());
    }

    public function testReadAsString()
    {
        $filename = $this->testCache . 'test.txt';
        \file_put_contents($filename, $this->sampleTextString);

        $file = new File($filename);
        $file->open(File::MODE_READ);
        $this->assertEquals($this->sampleTextString, $file->read()->asString());
    }

    public function testReadAsArray()
    {
        $filename = $this->testCache . 'test.txt';
        \file_put_contents($filename, \implode(PHP_EOL, $this->sampleTextArray));

        $file = new File($filename);
        $file->open(File::MODE_READ);
        $this->assertEquals($this->sampleTextArray, $file->read()->asArray());
    }

    public function testReadLines()
    {
        $filename = $this->testCache . 'test.txt';
        \file_put_contents($filename, \implode(PHP_EOL, $this->sampleTextArray));

        $file = new File($filename);
        $file->open(File::MODE_READ);
        $data = [];
        while (($buffer = $file->read()->line()) !== false) {
            $data[] = \trim($buffer);
        }
        $this->assertEquals($this->sampleTextArray, $data);
    }

    public function testReadFails()
    {
        $filename = $this->testCache . 'test.txt';

        $file = File::create($filename);
        $this->expectException(NotReadableException::class);
        $file->read();
    }

    public function testCloseSuccess()
    {
        $filename = $this->testCache . 'test.txt';

        $file = File::create($filename);
        $file->open(File::MODE_READ);
        $this->assertInstanceOf(File::class, $file->close());
    }

    public function testWriteStringSuccess()
    {
        $filename = $this->testCache . 'test.txt';
        $file = File::create($filename);
        $file->open(File::MODE_READ_WRITE);
        $this->assertInstanceOf(File::class, $file->write()->string($this->sampleTextString));
        $file->close();
        $this->assertEquals(\file_get_contents($filename), $this->sampleTextString);
    }

    public function testWriteArraySuccess()
    {
        $filename = $this->testCache . 'test.txt';
        $file = File::create($filename);
        $file->open(File::MODE_READ_WRITE);
        $this->assertInstanceOf(File::class, $file->write()->array($this->sampleTextArray));
        $file->close();
        $this->assertEquals(\array_map('trim', \file($filename)), $this->sampleTextArray);
    }

    public function testWriteFails()
    {
        $filename = $this->testCache . 'test.txt';

        $file = File::create($filename);
        $this->expectException(NotWriteableException::class);
        $file->write();
    }

    public function testEndOfFileDetect()
    {
        $filename = $this->testCache . 'test.txt';
        $file = File::create($filename);
        $file->open(File::MODE_READ_WRITE);
        $this->assertInstanceOf(File::class, $file->write()->array($this->sampleTextArray));

        $this->assertFalse($file->eof());
    }

    public function testEndOfFileDetectFails()
    {
        $filename = $this->testCache . 'test.txt';

        $file = File::create($filename);
        $this->expectException(NodeInaccessibleException::class);
        $file->eof();
    }

    public function testCopySuccess()
    {
        $filename = $this->testCache . 'test.txt';
        $newfile = $this->testCache . 'new.txt';
        \file_put_contents($filename, $this->sampleTextString);
        $file = new File($filename);

        $this->assertInstanceOf(File::class, $file->copy($newfile));
        $this->assertEquals(\file_get_contents($filename), \file_get_contents($newfile));
        $this->assertTrue(\file_exists($filename));
        $this->assertTrue(\file_exists($newfile));
    }

    public function testMoveSuccess()
    {
        $filename = $this->testCache . 'test.txt';
        $newfile = $this->testCache . 'new.txt';
        \file_put_contents($filename, $this->sampleTextString);
        $file = new File($filename);

        $this->assertInstanceOf(File::class, $file->move($newfile));
        $this->assertEquals($this->sampleTextString, \file_get_contents($newfile));
        $this->assertFalse(\file_exists($filename));
        $this->assertTrue(\file_exists($newfile));
    }

    public function testRenameSuccess()
    {
        $filename = $this->testCache . 'test.txt';
        $newfile = $this->testCache . 'new.txt';
        \file_put_contents($filename, $this->sampleTextString);
        $file = new File($filename);

        $this->assertInstanceOf(File::class, $file->rename($newfile));
        $this->assertEquals($this->sampleTextString, \file_get_contents($newfile));
        $this->assertFalse(\file_exists($filename));
        $this->assertTrue(\file_exists($newfile));
    }

    public function testDeleteSuccess()
    {
        $filename = $this->testCache . 'test.txt';
        \file_put_contents($filename, $this->sampleTextString);
        $file = new File($filename);
        $file->delete();
        $this->assertFalse(\file_exists($filename));
    }

    public function testGetExtension()
    {
        $filename = $this->testCache . 'test.txt';
        $file = File::create($filename);
        $this->assertEquals('txt', $file->getExtension());
    }

    public function testGetFileNameWithExtension()
    {
        $filename = $this->testCache . 'test.txt';
        $file = File::create($filename);
        $this->assertEquals('test.txt', $file->getFileName());
    }

    public function testGetFileNameWithoutExtension()
    {
        $filename = $this->testCache . 'test.txt';
        $file = File::create($filename);
        $this->assertEquals('test', $file->getFileNameNoExtension());
    }

    public function testGetDirName()
    {
        $filename = $this->testCache . 'test.txt';
        $file = File::create($filename);
        $this->assertEquals(\realpath($this->testCache), $file->getFileDir());
    }

    public function testRewindSuccess()
    {
        $filename = $this->testCache . 'test.txt';
        $file = File::create($filename);
        $file->open(File::MODE_WRITE_APPEND);
        $this->assertInstanceOf(File::class, $file->rewind());
    }

    public function testRewindFails()
    {
        $filename = $this->testCache . 'test.txt';
        $file = File::create($filename);
        $this->expectException(NodeInaccessibleException::class);
        $file->rewind();
    }

    public function testMagicToString()
    {
        $path = new File(__FILE__);
        $this->assertEquals(\realpath(__FILE__), (string) $path);
    }

    public function testGetParent()
    {
        $path = new File(__FILE__);
        $this->assertEquals(\realpath(dirname(__FILE__)), $path->getParent());
    }
}
