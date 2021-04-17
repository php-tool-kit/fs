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
 * Manipulador de diret�rio.
 *
 * @author Everton
 * 
 * TODO Melhorias de c�digo
 * TODO Codecoverage
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

    /**
     * Copia recursivamente o conte�do do diret�rio.
     * 
     * @return Directory Retorna uma inst�ncia do diret�rio de destino.
     * 
     * {@inheritDoc}
     * @see \PTK\FS\NodeInterface::copy()
     */
    public function copy(string $destiny): Directory
    {
        if (!\file_exists($destiny)) {
            if (\mkdir($destiny, 0755, true) === false) {
                throw new NodeInaccessibleException($destiny);
            }
        }
        
        $destiny = new Directory($destiny);

        $list = $this->recursive()->list();

        foreach ($list as $o) {
            $d = str_replace($this->directory, $destiny->getDirPath(), $o);
            $path = new Path($d);
            if($path->isDir()){
                if(!$path->exists()){
                    mkdir($d, 0755, true);
                }
            }
            
            if($path->isFile()){
                $parent = dirname($d);
                if(!file_exists($parent)){
                    mkdir($parent, 0755, true);
                }
                
                copy($o, $d);
            }
        }
        
        return $destiny;
    }

    /**
     * Deleta os arquivos (n�o recursivo) e os subdiret�rios n�o vazios.
     *
     * O diret�rio em si n�o � deletado, apenas o seu conte�do, de forma n�o recursiva.
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

    /**
     * Move o conte�do para um novo local.
     * 
     * Observe que a partir do sucesso da opera��o, o diret�rio atual n�o vai mais existir, ent�o a inst�ncia atual n�o vai mais poder ser usada.
     * 
     * @return Directory Retorna uma inst�ncia com o diret�rio de destino.
     * 
     * {@inheritDoc}
     * @see \PTK\FS\NodeInterface::move()
     */
    public function move(string $destiny): Directory
    {
        $target = $this->copy($destiny);
        $this->recursive()->delete();
        rmdir($this->directory);
        return $target;
    }

    /**
     * Apelido para Directory::move()
     * 
     * @see Directory::move()
     * 
     * {@inheritDoc}
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
        throw FSException($directory);
        // @codeCoverageIgnoreEnd
    }

    /**
     * Retorna o caminho do diret�rio.
     *
     * @return string
     */
    public function getDirPath(): string
    {
        return $this->directory;
    }

    /**
     * Lista o conte�do do diret�rio (n�o recursivo).
     *
     * @param int $filter
     *            0: Mostra arquivos e diret�rios; 1: mostra apenas diret�rios; 2: Mostra apenas arquivos. Veja as constantes Directory::LIST_*
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
    
    public function __toString(): string {
        return $this->directory;
    }
}
