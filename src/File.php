<?php

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

use PTK\FS\Exception\NodeAlreadyExistsException;
use PTK\FS\Exception\NodeInaccessibleException;
use PTK\FS\Exception\NotFoundException;
use PTK\FS\Exception\NotReadableException;
use PTK\FS\Exception\NotWriteableException;
use PTK\FS\Reader\FileReader;
use PTK\FS\Writer\FileWriter;

/**
 * Manipula um arquivo.
 *
 * @author Everton
 */
class File implements NodeInterface {
    
    public const MODE_READ = 'r';
    public const MODE_READ_WRITE = 'r+';
    public const MODE_WRITE_TRUNCATE = 'w';
    public const MODE_READ_WRITE_TRUNCATE = 'w+';
    public const MODE_WRITE_APPEND = 'a';
    public const MODE_READ_WRITE_APPEND = 'a+';
    public const MODE_WRITE_FAIL_IF_EXISTS = 'x';
    public const MODE_READ_WRITE_FAIL_IF_EXISTS = 'x+';
    public const MODE_WRITE_START_POINTER = 'c';
    public const MODE_READ_WRITE_START_POINTER = 'c+';
    
    protected string $filename;
    
    protected $handle;
    
    protected string $openMode;

    public function __construct(string $filename) {
        $this->filename = realpath($filename);
        
        if(!file_exists($this->filename)){
            throw new NotFoundException($this->filename);
        }
    }
    
    public static function create(string $path): NodeInterface
    {
        if(file_exists($path)){
            throw new NodeAlreadyExistsException($path);
        }
        
        $handle = file_put_contents($path, '');
        // @codeCoverageIgnoreStart
        // não consegui imaginar uma forma de testar isso ainda
        if($handle === false){
            throw new NodeInaccessibleException($path);
        }
        // @codeCoverageIgnoreEnd
        
        return new File($path);
    }

    /**
     * Abre o arquivo para executar as operações permitidas por $mode. Wrapper para fopen().
     * @param string $mode Um dos valores aceitos por fopen()
     * @return File
     * @throws NodeInaccessibleException
     */
    public function open(string $mode): File
    {
        $this->handle = fopen($this->filename, $mode);
        // @codeCoverageIgnoreStart
        // não descobri como testar isso
        if($this->handle === false){
            throw new NodeInaccessibleException($this->filename);
        }
        // @codeCoverageIgnoreEnd
        $this->openMode = $mode;
        return $this;
    }
    
    /**
     * 
     * @return string
     */
    public function getOpenMode(): string
    {
        return $this->openMode;
    }
    
    /**
     * Retorna o caminho completo do arquivo.
     * @return string
     * @see File::getFileName()
     */
    public function getFilePath(): string
    {
        return $this->filename;
    }
    
    /**
     * Lê dados do arquivo.
     * 
     * Devolve uma instância de FileReader.
     * 
     * @return FileReader
     * @throws NotReadableException
     */
    public function read(): FileReader
    {
        if(is_null($this->handle)){
            throw new NotReadableException($this->filename);
        }
        
        // @codeCoverageIgnoreStart
        // não sei como testar isso ainda
        if(is_readable($this->filename) === false){
            throw new NotReadableException($this->filename);
        }
        // @codeCoverageIgnoreEnd
        return new FileReader($this->handle);
    }
    
    public function close(): File
    {
        $close = fclose($this->handle);
        // @codeCoverageIgnoreStart
        // não sei como testar isso ainda
        if($close === false){
            throw new NodeInaccessibleException($this->filename);
        }
        // @codeCoverageIgnoreEnd
        
        return $this;
    }
    
    /**
     * Escreve conteúdo no arquivo aberto.
     * 
     * @return FileWriter
     * @throws NotWriteableException
     */
    public function write(): FileWriter
    {
        if(is_null($this->handle)){
            throw new NotWriteableException($this->filename);
        }
        // @codeCoverageIgnoreStart
        // não sei como testar isso ainda
        if(is_writeable($this->filename) === false){
            throw new NotWriteableException($this->filename);
        }
        // @codeCoverageIgnoreEnd
        return new FileWriter($this->handle, $this);
    }
    
    /**
     * Detecta se o ponteiro do arquivo aberto está no fim do arquivo.
     * 
     * @return bool
     * @throws NodeInaccessibleException
     */
    public function eof(): bool
    {
        if(is_null($this->handle)){
            throw new NodeInaccessibleException($this->filename);
        }
        
        return feof($this->handle);
    }
    
    /**
     * Copia o arquivo para um novo local especificado por $to.
     * 
     * @param string $to
     * @return File Retorna uma instância de File representando o novo arquivo.
     */
    public function copy(string $to): File
    {
        $copy = copy($this->filename, $to);
        
        // @codeCoverageIgnoreStart
        // ainda não sei como testar
        if($copy === false){
            throw new CopyException($this->filename, $to);
        }
        // @codeCoverageIgnoreEnd
        
        return new File($to);
    }
    
    /**
     * Move o arquivo para um novo local especificado por $to.
     * 
     * @param string $to Novo arquivo. Se existir, será sobrescrito.
     * @return File Retorna uma instância de File representando o novo arquivo.
     */
    public function move(string $to): File
    {
        $move = rename($this->filename, $to);
        // @codeCoverageIgnoreStart
        // ainda não sei como testar
        if($move === false){
            throw new MoveException($this->filename, $to);
        }
        // @codeCoverageIgnoreEnd
        
        return new File($to);
    }
    
    /**
     * Renomeia o arquivo.
     * 
     * Internamente utiliza File::move().
     * 
     * @param string $newName
     * @return File
     */
    public function rename(string $newName): File
    {
        return $this->move($newName);
    }
    
    /**
     * Apaga o arquivo.
     * 
     * @return bool
     */
    public function delete(): bool
    {
        return unlink($this->filename);
    }
    
    /**
     * Retorna a extensão do arquivo, sem o ponto.
     * @return string
     */
    public function getExtension(): string
    {
        return pathinfo($this->filename, PATHINFO_EXTENSION);
    }
    
    /**
     * Retorna o nome do arquivo, com ou sem a extensão.
     * 
     * @param bool $extension
     * @return string
     */
    public function getFileName(bool $extension = true): string
    {
        $suffix = '';
        if($extension === false){
            $suffix = ".{$this->getExtension()}";
        }
        
        return basename($this->filename, $suffix);
    }
    
    /**
     * Retorna o caminho do diretório do arquivo.
     * 
     * @return string
     */
    public function getFileDir(): string
    {
       return dirname($this->filename);
    }
    
    /**
     * Reincia o ponteiro do arquivo.
     * 
     * @return File
     * @throws NodeInaccessibleException
     * @throws FSException
     */
    public function rewind(): File
    {
        if(is_null($this->handle)){
            throw new NodeInaccessibleException($this->filename);
        }
        
        $rewind = rewind($this->handle);
        // @codeCoverageIgnoreStart
        // ainda não sei como testar isso
        if($rewind === false){
            throw new FSException($this->filename);
        }
        // @codeCoverageIgnoreEnd
        
        return $this;
    }
}