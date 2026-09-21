# I/O (arquivos e streams)

## Manipulação de Arquivos e Streams em PHP

Projeto em PHP focado em leitura, escrita, processamento e manipulação de arquivos em diferentes formatos, além do uso de streams e wrappers para interagir com console, ZIP, diretórios e HTTP.

- Leitura e escrita de arquivos: os scripts `1-leitor.php` e `2-escritor.php` demonstram o uso de `fopen()`, `fgets()`, `fread()`, `file_get_contents()`, `file()` para leitura de arquivos em TXT, além de `fwrite()`, `file_put_contents()` e `FILE_APPEND` para escrita e atualização incremental de conteúdo. Há também exemplos de exportação e leitura de dados em CSV, como em `6-csv.php` e `6-spl.php`.
- Streams: o projeto explora streams nativos do PHP, como `php://stdin`, `php://stdout` e `php://stderr` para entrada/saída de console; `zip://` para acessar arquivos compactados diretamente; `http://` para carregar conteúdo de endpoints externos; e `stream_context_create()` para configurar contextos de stream, como requisições HTTP e acesso a ZIP com senha. O uso de `SplFileObject` também mostra uma leitura orientada a arquivos em CSV.
- Processamento: os exercícios envolvem leitura linha a linha, gravação em arquivos, concatenação de textos, listagem de diretórios com `dir()`, transformação de conteúdo entre streams com `stream_copy_to_stream()`, e processamento de arquivos em formatos estruturados como CSV. Há também exemplos de manipulação de arquivos compactados, leitura de dados externos e uso de entrada do usuário para persistir registros em arquivo.

**Palavras-chave:** PHP, Streams, TXT, CSV, ZIP, HTTP, SPL.
