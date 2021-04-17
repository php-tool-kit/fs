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
namespace PTK\FS\Recursive;

use PTK\FS\Directory;
use RecursiveDirectoryIterator;

/**
 * Implementa métodos recursivos
 *
 * @author Everton
 */
class RecursiveDirectory
{
    /**
     *
     * @var string O diretório de trabalho.
     */
    protected string $directory;

    /**
     *
     * @param string $directory
     */
    public function __construct(string $directory)
    {
        $this->directory = $directory;
    }

    /**
     * Lista de arquivos/diretório recursivos.
     *
     * @param int $filter Filtro de acordo com as constantes LIST_*
     *
     * @return array<string>
     */
    public function list(int $filter = 0): array
    {
        $dir = new Directory($this->directory);

        $content = [];

        $list = $dir->list();

        foreach ($list as $node) {
            $content[] = $node;
            if (\is_dir($node)) {
                $dir = new Directory($node);
                $content = \array_merge($content, $dir->list());
            }
        }

        if ($filter === Directory::LIST_DIR) {
            foreach ($content as $key => $node) {
                if (! \is_dir($node)) {
                    unset($content[$key]);
                }
            }
        }

        if ($filter === Directory::LIST_FILES) {
            foreach ($content as $key => $node) {
                if (! \is_file($node)) {
                    unset($content[$key]);
                }
            }
        }

        return \array_merge($content);
    }

    /**
     *
     * @return RecursiveDirectoryIterator
     */
    public function iterator(): RecursiveDirectoryIterator
    {
        return new RecursiveDirectoryIterator($this->directory);
    }

    /**
     * Deleção recursiva.
     *
     * @return bool
     */
    public function delete(): bool
    {
        $list = $this->list(Directory::LIST_FILES);
        foreach ($list as $node) {
            if (\is_file($node)) {
                \unlink($node);
            }
        }
        $list = $this->list(Directory::LIST_DIR);
        foreach ($list as $node) {
            if (\is_dir($node)) {
                \rmdir($node);
            }
        }

        return true;
    }
}
