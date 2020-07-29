<?php

/**
 * Ferramentas para trabalhar com diretórios, arquivos e caminhos.
 */

namespace ptk\fs;

use Exception;

/**
 * Mescla strings como uma caminho de diretório ou arquivo.
 * 
 * @param string $pieces
 * @return string
 */
function join_path(string ...$pieces): string
{
    return join(DIRECTORY_SEPARATOR, $pieces);
}

/**
 * Substituis todas as / e \ para o separador de diretórios padrão do SO.
 * 
 * Adicionalmente, se $isdir for TRUE, adiciona um separador de diretório ao final.
 * @param string $path
 * @param bool $isdir
 * @return string
 */
function slashes(string $path, bool $isdir = false): string
{
    $path = str_replace('/', DIRECTORY_SEPARATOR, $path);
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $path);
    
    if($isdir){
        if(
            substr($path, -1, 1) !== '/'
            && substr($path, -1, 1) !== '\\'
        ) {
            $path .= DIRECTORY_SEPARATOR;
        }
    }
    
    return $path;
}

/**
 * Escaneia o diretório em busca dos subdiretórios/arquivos.
 * 
 * Não é recursivo e remove . e .. do resultado.
 * 
 * O retorno é feito com o caminho absoluto dos itens.
 * 
 * @param string $path
 * @return array<string>
 * @throws Exception
 * @see scan_recursive()
 */
function scan(string $path): array
{
    if(!is_dir($path)){
        throw new Exception("$path não é um diretório ou não existe.");
    }
    
    $path = realpath(slashes($path, true));
    
    $result = [];
    
    foreach (scandir($path) as $item){
        if($item !== '.' && $item !== '..'){
            $result[] = join_path($path, $item);
        }
    }
    
    return $result;
}

/**
 * Escaneia recursivamente um diretório, semelhante a scan().
 * 
 * @param string $path
 * @return array<astring>
 * @throws Exception
 * @see scan()
 */
function scan_recursive(string $path): array
{
    if(!is_dir($path)){
        throw new Exception("$path não é um diretório ou não existe.");
    }
    
    $path = realpath(slashes($path, true));
    
    $result = [];
    
    foreach (scandir($path) as $item){
        if($item !== '.' && $item !== '..'){
            $item = join_path($path, $item);
            $result[] = $item;
            if(is_dir($item)){
                $result = array_merge($result, scan_recursive($item));
            }
        }
    }
    
    return $result;
}

/**
 * Seleciona apenas os arquivos em uma lista de caminhos.
 * 
 * @param array<string> $list
 * @return array<string>
 * @see get_only_dir()
 */
function get_only_files(array $list): array
{
    $files = [];
    foreach ($list as $path){
        if(is_file($path)){
            $files[] = $path;
        }
    }
    
    return $files;
}

/**
 * Seleciona apenas os diretórios em uma lista de caminhos.
 * 
 * @param array<string> $list
 * @return array<string>
 * @see get_only_files()
 */
function get_only_dir(array $list): array
{
    $dir = [];
    foreach ($list as $path){
        if(is_dir($path)){
            $dir[] = $path;
        }
    }
    
    return $dir;
}