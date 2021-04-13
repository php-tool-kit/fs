# Modelo para projetos em PHP.

## Instalação

Ao criar seu repositório no Github siga estes passos:

1. No GitHub, navegue até a página principal do repositório.
2. Acima da lista de arquivos, clique em *Use this template (Usar este modelo)*.
3. Use o menu suspenso Proprietário e selecione a conta que você deseja implantar no repositório.
4. Digite um nome para seu repositório e uma descrição opcional.
5. Escolha uma visibilidade do repositório.
6. Opcionalmente, para incluir a estrutura de diretório e os arquivos de todas as ramificações no modelo, e não apenas a ramificação padrão, selecione *Incluir todas as ramificações*.
7. Opcionalmente, se a conta ou organização pessoal em que você está criando usar algum aplicativo GitHub do GitHub Marketplace, selecione os aplicativos que você gostaria de usar no repositório.
8. Clique em *Create repository from template (Criar repositório a partir do modelo)*.

Mais detalhes na [Ajuda do Github](https://help.github.com/pt/github/creating-cloning-and-archiving-repositories/creating-a-repository-from-a-template).


**Não esqueça de adaptar ```composer.json```, principalmente os campos ```name``` e ```autoload``` e ```config/phpunit.xml``` na tag ```default-package-name```.**


## Comandos do Composer

No modelo estão incluídos alguns comandos personalizados no Composer para serem usados com ```composer nome_do_comando```.

A lista dos comandos personalizados pode ser consultada com ```composer run -l```.