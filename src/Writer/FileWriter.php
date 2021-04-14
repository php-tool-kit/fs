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

namespace PTK\FS\Writer;

use PTK\FS\Exception\NotWriteableException;
use PTK\FS\File;

/**
 * Um writer de arquivos para ser usado por PTK\FS\File::write()
 *
 * @author Everton
 */
class FileWriter {

    /**
     * 
     * @var resource
     */
    private $handle;
    
    /**
     * 
     * @var File
     */
    private File $file;

    /**
     * 
     * @param resource $handle
     * @param File $file
     */
    public function __construct($handle, File $file) {
        $this->handle = $handle;
        $this->file = $file;
    }

    /**
     * Escreve uma string no arquivo.
     * 
     * Não é adicionada quebra de linha ao final da string. Isso deve ser feito manualmente se necessário.
     * 
     * Escreve na posição atual do ponteiro, conforme $mode usado por fopen() ou fseek().
     * @param string $data
     * @return File
     * @throws NotWriteableException
     */
    public function string(string $data): File {
        $result = fwrite($this->handle, $data);
        // @codeCoverageIgnoreStart
        // ainda não sei como testar
        if ($result === false) {
            throw new NotWriteableException($this->file->getFilePath());
        }
        // @codeCoverageIgnoreEnd
        return $this->file;
    }

    /**
     * Escreve um array para o arquivo considerando cada item do array uma nova linha. Por isso,
     *  adiciona ao final de cada item do array, uma quebra de linha como PHP_EOL.
     * 
     * Escreve na posição atual do ponteiro, conforme $mode usado por fopen() ou fseek().
     * 
     * @param array $data
     * @return File
     * @throws NotWriteableException
     */
    public function array(array $data): File {
        foreach ($data as $buffer) {
            $result = fwrite($this->handle, $buffer.PHP_EOL);
            // @codeCoverageIgnoreStart
            // ainda não sei como testar
            if ($result === false) {
                throw new NotWriteableException($this->file->getFilePath());
            }
            // @codeCoverageIgnoreEnd
        }

        return $this->file;
    }

}
