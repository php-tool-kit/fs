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
     * @param array<string> $pieces
     *            Uma lista de strings.
     *            Caso mais de uma string for fornecida, a lista será concatenada usando DIRECTORY_SEPARATOR
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
     * Se $path for um diretório, acrescenta
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
     * Retorna a extensão do arquivo (sem o ponto separador).
     *
     * Este método utiliza a existência de um ponto separador em Path::$path para definir se existe
     * ou não extensão de arquivo. Isso possibilita usar caminhos que ainda não existem em disco. Entretanto,
     * falsos positivos podem ocorrer caso Pathh::$path contenha um caminho de diretório que contenha
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
     * É diretório.
     * Não interessa se Path::$path existe ou não no sistema de arquivos.
     *
     * @return bool
     */
    public function isDir(): bool
    {
        // Se não tem extensão, então lança uma exceção, portanto é diretório.
        try {
            $this->getExtension();
        } catch (FSException $e) {
            return true;
        }

        return false;
    }

    /**
     * É arquivo.
     * Não interessa se Path::$path existe ou não no sistema de arquivos.
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

