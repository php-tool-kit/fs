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

namespace PTK\FS;

use PTK\FS\Exception\FSException;
use PTK\FS\Exception\NodeNotFoundException;
use PTK\FS\Recursive\RecursiveDirectory;

/**
 * Manipulador de diretório.
 *
 * @author Everton
 */
class Directory implements NodeInterface
{
    /**
     *
     * @var string
     */
    protected string $directory = '';

    public function __construct(string $directory)
    {
        if (! \file_exists($directory)) {
            throw new NodeNotFoundException($directory);
        }

        $this->directory = (string) \realpath($directory);
    }

    public function copy(string $destiny): Directory
    {
    }

    public function delete(): bool
    {
    }

    public function move(string $destiny): Directory
    {
    }

    public function rename(string $newName): Directory
    {
    }

    public static function create(string $directory): Directory
    {
        $mkdir = true;
        if (! \file_exists($directory)) {
            $mkdir = \mkdir($directory, 0755, true);
        }

        if ($mkdir) {
            return new Directory($directory);
        }

        throw FSException($directory);
    }

    public function getDirPath(): string
    {
    }

    public function list(): array
    {
    }

    public function recursive(): RecursiveDirectory
    {
    }

    public function getParent(): string
    {
        return \dirname();
    }
}
