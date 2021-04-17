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

use DirectoryIterator;
use PTK\FS\Exception\FSException;
use PTK\FS\Exception\NodeInaccessibleException;
use PTK\FS\Exception\NodeNotFoundException;
use PTK\FS\Recursive\RecursiveDirectory;

/**
 * Manipulador de diretório.
 *
 * @author Everton
 *
 * TODO Codecoverage
 */
class Directory implements NodeInterface
{
    /**
     * Lista arquivos e diretetórios.
     *
     * @var integer
     */
    public const LIST_ALL = 0;

    /**
     * Lista apenas diretórios.
     *
     * @var integer
     */
    public const LIST_DIR = 1;

    /**
     * Lista apenas arquivos.
     *
     * @var integer
     */
    public const LIST_FILES = 2;

    /**
     * O diretório da instância.
     *
     * @var string
     */
    protected string $directory = '';

    /**
     *
     * @param string $directory
     * @throws NodeNotFoundException
     */
    public function __construct(string $directory)
    {
        if (! \file_exists($directory)) {
            throw new NodeNotFoundException($directory);
        }

        $this->directory = (string) \realpath($directory);
    }

    /**
     * Copia recursivamente o conteúdo do diretório.
     *
     * @return Directory Retorna uma instância do diretório de destino.
     *
     * {@inheritdoc}
     * @see \PTK\FS\NodeInterface::copy()
     */
    public function copy(string $destiny): Directory
    {
        if (! \file_exists($destiny)) {
            if (\mkdir($destiny, 0755, true) === false) {
                throw new NodeInaccessibleException($destiny);
            }
        }

        $destiny = new Directory($destiny);

        $list = $this->recursive()->list();

        foreach ($list as $o) {
            $target = \str_replace($this->directory, $destiny->getDirPath(), $o);
            $path = new Path($target);
            if ($path->isDir()) {
                if (! $path->exists()) {
                    \mkdir($target, 0755, true);
                }
            }

            if ($path->isFile()) {
                $parent = \dirname($target);
                if (! \file_exists($parent)) {
                    \mkdir($parent, 0755, true);
                }

                \copy($o, $target);
            }
        }

        return $destiny;
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
                if (\count($subcontent) === 0) {
                    \rmdir($node);
                }
            }
        }

        return true;
    }

    /**
     * Move o conteúdo para um novo local.
     *
     * Observe que a partir do sucesso da operação, o diretório atual não vai mais existir,
     *  então a instância atual não vai mais poder ser usada.
     *
     * @return Directory Retorna uma instância com o diretório de destino.
     *
     * {@inheritdoc}
     * @see \PTK\FS\NodeInterface::move()
     */
    public function move(string $destiny): Directory
    {
        $target = $this->copy($destiny);
        $this->recursive()->delete();
        \rmdir($this->directory);

        return $target;
    }

    /**
     * Apelido para Directory::move()
     *
     * @see Directory::move()
     *
     * {@inheritdoc}
     * @see \PTK\FS\NodeInterface::rename()
     */
    public function rename(string $newName): Directory
    {
        return $this->move($newName);
    }

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
        throw new FSException($directory);
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
     *            0: Mostra arquivos e diretórios; 1: mostra apenas diretórios; 2: Mostra apenas arquivos.
     *             Veja as constantes Directory::LIST_*
     * @return array<string>
     */
    public function list(int $filter = 0): array
    {
        $iterator = new DirectoryIterator($this->directory);
        $content = [];

        foreach ($iterator as $node) {
            if ($node->isDot()) {
                continue;
            }

            $path = $node->getRealPath();

            if ($path === false) {
                throw new NodeInaccessibleException((string) $path);
            }

            if ($filter === 0) {
                $content[] = $path;
                continue;
            }

            if ($filter === 1 && $node->isDir()) {
                $content[] = $path;
                continue;
            }

            if ($filter === 2 && $node->isFile()) {
                $content[] = $path;
                continue;
            }
        }

        return $content;
    }

    /**
     * Devolve uma instância com métodos recursivos.
     *
     * @return RecursiveDirectory
     */
    public function recursive(): RecursiveDirectory
    {
        return new RecursiveDirectory($this->directory);
    }

    /**
     * O diretório imediatamente superior.
     *
     * {@inheritdoc}
     * @see \PTK\FS\NodeInterface::getParent()
     */
    public function getParent(): string
    {
        return \dirname($this->directory);
    }

    /**
     * Retorna o diretório da instância quando casting para string é aplicado sobre o objeto.
     *
     * {@inheritdoc}
     * @see \PTK\FS\NodeInterface::__toString()
     */
    public function __toString(): string
    {
        return $this->directory;
    }
}
