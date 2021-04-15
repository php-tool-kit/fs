<?php

namespace PTK\FS;

use PTK\FS\Exception\NodeNotFoundException;
use PTK\FS\Exception\FSException;

/**
 * Manipula caminhos de arquivos/diretórios, independentemente de existir ou não em disco.
 *
 * @author Everton
 *        
 */
class Path
{

    /**
     * 
     * @var string
     */
    protected string $path;
    
    
    /**
     * 
     * @param array<string> $pieces Uma lista de strings.
     *  Caso mais de uma string for fornecida, a lista será concatenada usando PHP_EOL como separador. 
     */
    public function __construct(string ...$pieces)
    {
        $this->path = $this->normalize($this->join($pieces));
    }
    
    /**
     * Verifica se Path::$path existe no sistema de arquivos.
     * 
     * @return bool
     */
    public function exists(): bool
    {
        return file_exists($this->path);
    }
    
    /**
     * Une as partes de $pieces usando PHP_EOL;
     * @param string ...$pieces
     * @return string
     */
    protected function join(string ...$pieces): string
    {
        return join(PHP_EOL, $pieces);
    }
    
    /**
     * Substitui todas as barras e contrabarras por PHP_EOL. Se $path for um diretório, acrescenta
     *  PHP_EOL ao final.
     *  
     * @param string $path
     * @return string
     */
    protected function normalize(string $path): string
    {
        $path = preg_replace('/\\|\//', PHP_EOL, $path);
        
        if($this->detectIfIsDir($path)){
            $path .= PHP_EOL;
        }
        return $path;
    }
    
    /**
     * Detecta se $path é um diretório, mesmo que $path não exista no sistema de arquivos.
     * 
     * @param string $path
     * @return bool
     */
    protected function detectIfIsDir(string $path): bool
    {
        
    }
    
    /**
     * Retorna a extensão do arquivo.
     * 
     * @return string
     */
    public function getExtension(): string {
        ;
    }
    
    /**
     * É diretório. Não interessa se Path::$path existe ou não no sistema de arquivos. 
     * @return bool
     */
    public function isDir(): bool {
        ;
    }
    
    /**
     * É arquivo. Não interessa se Path::$path existe ou não no sistema de arquivos.
     * 
     * @return bool
     */
    public function isFile(): bool {
        ;
    }
    
    /**
     * Retorn o valor de Path::$path
     * @return string
     */
    public function getPath(): string{
        return $this->path;
    }
    
    /**
     * Retorna o caminho real se Path::$path existir.
     * 
     * @throws NodeNotFoundException
     * @return string
     */
    public function getRealPath(): string
    {
        if($this->exists()){
            return realpath($this->path);
        }
        throw new NodeNotFoundException($this->path);
    }
    
    /**
     * Wrapper para File::create() ou Directory::create().
     * 
     * @return NodeInterface
     * @throws FSException
     */
    public function create(): NodeInterface
    {
        if($this->isDir()){
            return Directory::create($this->path);
        }
        
        if($this->isFile()){
            return File::create($this->path);
        }
        
        throw new FSException($this->path);
    }
}

