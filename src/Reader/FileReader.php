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

namespace PTK\FS\Reader;

/**
 * Um reader de arquivos para ser usado por PTK\FS\File::read()
 *
 * @author Everton
 */
class FileReader
{
    /**
     *
     * @var resource
     */
    private $handle;

    /**
     *
     * @param resource $handle
     */
    public function __construct($handle)
    {
        $this->handle = $handle;
    }

    /**
     * Lê todo o conteúdo do arquivo e devolve como string, sem nenhum tratamento.
     *
     * * O ponto de leitura inicia no ponto onde o ponteiro do arquivo estiver definido conforme
     *  $mode de fopen() ou fseek()
     *
     * @return string
     */
    public function asString(): string
    {
        $data = '';
        while (\feof($this->handle) !== true) {
            $buffer = \fgets($this->handle);
            $data .= $buffer;
        }

        return $data;
    }

    /**
     * Lê o conteúdo do arquivo e devolve cada linha como um elemento de array. Alica trim() em cada linha.
     *
     * O ponto de leitura inicia no ponto onde o ponteiro do arquivo estiver definido conforme
     *  $mode de fopen() ou fseek()
     * @return array<string>
     */
    public function asArray(): array
    {
        $data = [];
        while (\feof($this->handle) !== true) {
            $buffer = \fgets($this->handle);
            if ($buffer !== false) {
                $data[] = \trim($buffer);
            }
        }

        return $data;
    }

    /**
     * Lê uma linha com fgets() e avança o ponteiro interno do arquivo.
     * @return mixed
     */
    public function line()
    {
        return \fgets($this->handle);
    }
}
