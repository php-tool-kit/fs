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

/**
 * Node representa um arquivo ou diretório no sistema de arquivos.
 *
 * @author Everton
 */
interface NodeInterface
{
    /**
     *
     * @param string $path
     */
    public function __construct(string $path);

    /**
     * Cria um arquivo/diretório.
     *
     * Diretórios são criados recursivamente.
     *
     * O comportamento padrão quando se está tentando criar:
     *
     * - diretório: se já existe, não tenta recriar; se não existe, cria;
     * - arquivo: se já existe, dispara uma exceção, se não, cria.
     *
     * @param string $path
     * @return NodeInterface Retorna uma instância do node criado.
     * @throws FSException
     */
    public static function create(string $path): NodeInterface;

    /**
     * Copia um arquivo/diretório.
     *
     * @param string $destiny
     * @return NodeInterface Retorna uma nova instância com o novo arquivo/diretório.
     */
    public function copy(string $destiny): NodeInterface;

    /**
     * Move um arquivo/diretório.
     *
     * @param string $destiny
     * @return NodeInterface Retorna uma nova instância com o novo arquivo/diretório.
     */
    public function move(string $destiny): NodeInterface;

    /**
     * Renomeia o arquivo/diretório. Internamente utiliza NodeInterface::move()
     *
     * @param string $newName
     * @return NodeInterface
     */
    public function rename(string $newName): NodeInterface;

    /**
     * Apaga um arquivo/diretório.
     *
     * @return bool
     */
    public function delete(): bool;

    /**
     * Retorna o diretório imediatamente acima do node.
     *
     * @return string
     */
    public function getParent(): string;

    /**
     * Mostra o caminho do node quando o casting para string for feito.
     * @return string
     */
    public function __toString(): string;
}
