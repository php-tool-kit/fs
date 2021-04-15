<?php
namespace PTK\FS;

use PTK\FS\Exception\NodeNotFoundException;
use PTK\FS\Exception\FSException;

/**
 * Manipula caminhos de arquivos/diret�rios, independentemente de existir ou n�o em disco.
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
     * @param array<string> $pieces
     *            Uma lista de strings.
     *            Caso mais de uma string for fornecida, a lista ser� concatenada usando DIRECTORY_SEPARATOR
     *            como separador.
     */
    public function __construct(string ...$pieces)
    {
        $this->path = $this->join($pieces);
        $this->path = $this->normalize($this->path);
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
     * Une as partes de $pieces usando DIRECTORY_SEPARATOR;
     *
     * @param array<string> $pieces
     * @return string
     */
    protected function join(array $pieces): string
    {
        return join(DIRECTORY_SEPARATOR, $pieces);
    }

    /**
     * Substitui todas as barras e contrabarras por DIRECTORY_SEPARATOR.
     * Se $path for um diret�rio, acrescenta
     * PHP_EOL ao final.
     *
     * @param string $path
     * @return string
     */
    protected function normalize(string $path): string
    {
        $path = preg_replace('/\\|\//', DIRECTORY_SEPARATOR, $path);

        if ($this->isDir()) {
            $path .= DIRECTORY_SEPARATOR;
        }
        return $path;
    }

    /**
     * Retorna a extens�o do arquivo (sem o ponto separador).
     *
     * Este m�todo utiliza a exist�ncia de um ponto separador em Path::$path para definir se existe
     * ou n�o extens�o de arquivo. Isso possibilita usar caminhos que ainda n�o existem em disco. Entretanto,
     * falsos positivos podem ocorrer caso Pathh::$path contenha um caminho de diret�rio que contenha
     * ponto na sua estrutura.
     *
     * @return string
     */
    public function getExtension(): string
    {
        $boom = explode('.', $this->path);
        $size = sizeof($boom);

        if ($size === 1) {
            throw new FSException($this->path, $size);
        }

        if ($size > 2) {
            throw new FSException($this->path, $size);
        }

        return array_pop($boom);
    }

    /**
     * � diret�rio.
     * N�o interessa se Path::$path existe ou n�o no sistema de arquivos.
     *
     * @return bool
     */
    public function isDir(): bool
    {
        // Se n�o tem extens�o, ent�o lan�a uma exce��o, portanto � diret�rio.
        try {
            $this->getExtension();
        } catch (FSException $e) {
            return true;
        }

        return false;
    }

    /**
     * � arquivo.
     * N�o interessa se Path::$path existe ou n�o no sistema de arquivos.
     *
     * @return bool
     */
    public function isFile(): bool
    {
        if ($this->isDir())
            return false;

        return true;
    }

    /**
     * Retorn o valor de Path::$path
     *
     * @return string
     */
    public function getPath(): string
    {
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
        if ($this->exists()) {
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
        if ($this->isDir()) {
            return Directory::create($this->path);
        }

        if ($this->isFile()) {
            return File::create($this->path);
        }

        throw new FSException($this->path);
    }
}

