<?php

/**
 * Prooph was here at `%package%` in `%year%`! Please create a .docheader in the project root and run `composer cs-fix`
 */
declare(strict_types = 1);

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
use PTK\FS\Exception\NodeInaccessibleException;

/**
 * Manipulador de diretÃ³rio.
 *
 * @author Everton
 */
class Directory implements NodeInterface
{

    const LIST_ALL = 0;

    const LIST_DIR = 1;

    const LIST_FILES = 2;

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
        if (! \file_exists($destiny)) {
            if (\mkdir($destiny, 0755, true) === false) {
                throw new NodeInaccessibleException($destiny);
            }
        }

        $list = $this->recursive()->list();

        foreach ($list as $node) {
            
        }
    }

    /**
     * Deleta os arquivos (não recursivo) e os subdiretórios não vazios.
     *
     * O diretório em si não é deletado, apenas o seu conteúdo, de forma não recursiva.
     *
     * {@inheritdoc}
     * @see \PTK\FS\NodeInterface::delete()
     */
    public function delete(): bool
    {
        $list = $this->list();

        foreach ($list as $node) {
            if (\is_file($node)) {
                \unlink($node);
            }

            if (\is_dir($node)) {
                $dir = new Directory($node);
                $subcontent = $dir->list();
                if (\sizeof($subcontent) === 0) {
                    \rmdir($node);
                }
            }
        }

        return true;
    }

    public function move(string $destiny): Directory
    {}

    public function rename(string $newName): Directory
    {}

    /**
     *
     * @param string $directory
     * @return Directory
     */
    public static function create(string $directory): Directory
    {
        $mkdir = true;
        if (! \file_exists($directory)) {
            $mkdir = \mkdir($directory, 0755, true);
        }

        if ($mkdir) {
            return new Directory($directory);
        }

        // @codeCoverageIgnoreStart
        throw FSException($directory);
        // @codeCoverageIgnoreEnd
    }

    /**
     * Retorna o caminho do diretório.
     *
     * @return string
     */
    public function getDirPath(): string
    {
        return $this->directory;
    }

    /**
     * Lista o conteúdo do diretório (não recursivo).
     *
     * @param int $filter
     *            0: Mostra arquivos e diretórios; 1: mostra apenas diretórios; 2: Mostra apenas arquivos. Veja as constantes Directory::LIST_*
     * @return array<string>
     */
    public function list(int $filter = 0): array
    {
        $iterator = new \DirectoryIterator($this->directory);
        $content = [];

        foreach ($iterator as $node) {
            if ($node->isDot()) {
                continue;
            }

            if ($filter === 0) {
                $content[] = $node->getRealPath();
                continue;
            }

            if ($filter === 1 && $node->isDir()) {
                $content[] = $node->getRealPath();
                continue;
            }

            if ($filter === 2 && $node->isFile()) {
                $content[] = $node->getRealPath();
                continue;
            }
        }

        return $content;
    }

    public function recursive(): RecursiveDirectory
    {
        return new RecursiveDirectory($this->directory);
    }

    public function getParent(): string
    {
        return \dirname($this->directory);
    }
}
