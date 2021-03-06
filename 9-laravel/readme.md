# Laravel
O Laravel é um _framework full stack_ do PHP, ou seja, nos oferece ferramentas para desenvolver a lógica da aplicação (_back-end_) e a interface de interação do usuário (_front-end_). Ele segue a arquitetura MVC e oferece uma série de facilidades que permitem um rápido desenvolvimento. O objetivo aqui será desenvolver uma aplicação para gerenciar as séries que o usuário estiver assistindo.

#### Índice<a name='topo'></a>
1. <a href='#1'>Configurando o ambiente</a>  
1.1. Criando um projeto  
1.2. Estrutura de arquivos  
1.3. A primeira rota  
2. <a href='#2'>_Controllers_</a>  
2.1. Acessando dados da requisição  
3. <a href='#3'>_Views_</a>  
3.1. _View_ de listagem de séries  
3.2. Estilizando a _view_ com Bootstrap  
3.3. _View_ para adicionar série  
3.4. Blades  
4. <a href='#4'>Criando registros</a>  
4.1. Configurando o Banco de Dados  
4.2. _Migrations_  
4.3. _Model_  
4.4. Consultas no Banco  
4.5. Atribuição em massa  
5. <a href='#5'>Lapidando a aplicação</a>  
6. <a href='#6'>Destruindo registros</a>  
7. <a href='#7'>Nomeando rotas</a>  
8. <a href='#8'>Lapidando a aplicação (mais um pouco)</a>  
9. <a href='#9'>Validando os dados</a>  
9.1. `validate()`  
9.2. _Form Request_  
10. <a href='#10'>Novos _models_</a>  
10.1. Relacionamentos  
10.2. _Migrations_  
11. <a href='#11'>CRUD de séries</a>  
11.1. Modificando a criação de séries  
11.2. Listando temporadas  
11.3. Refatorando e separando a "criação de séries"  
11.4. Refatorando a exclusão de séries  
11.5. Refatorando criação de séries (novamente)  
11.6. Alterando o nome de uma série  
12. <a href='#12'>Episódios</a>  
12.1. Listando episódios  
12.2. Assitindo episódios  
13. <a href='#13'>Autenticação</a>  
13.1. `Auth`  
13.2. Protegendo rotas  
13.2.1. Usando `Auth::check()`  
13.2.2. Em cada rota  
13.2.3. No construtor  
13.2.4. No _kernel_  
13.3. Autenticação "própria"  
13.3.1. Entrando  
13.3.2. Registrando novos usuários  
13.4. Melhorando a navegação  
13.5. _Middleware_  
13.5.1. Modificando o _middleware_ padrão para autenticação  
13.5.2. Criando nosso próprio _middleware_ para autenticação  
14. <a href='#14'>Testes automatizados</a>  
.1. Primeiro teste (`Temporada`)  
14.2. Testando inserção de registros no Banco (`CriadorDeSerie`)  
14.3. Rodando os testes em um Banco "na meméria"  
14.4. Testando exclusão de registros no Banco (`RemovedorDeSerie`)  
15. <a href='#15'>Envio de e-mail</a>  
15.1. Template do e-mail  
15.1.1. _Markdown_  
15.2. Enviando e-mail  
15.2.1. _mailtrap_  
15.2.2. Incorporando o envio à regra de negócio  
15.2.3. Adicionando tempo entre os envios  
16. <a href='#16'>Processamento dados com filas</a>  
16.1. Configurando o ambiente  
16.2. Enviando processos para a fila  
17. <a href='#17'>Eventos e ouvintes</a>  
17.1. Criando um _event-listener_ para enviar _e-mail_  
17.2. Registrando os eventos  
17.3. Emitindo um evento  
17.4. Criando um _listener_ para _log_ da aplicação  
17.5. Processando eventos de forma assíncrona  
18. <a href='#18'>_Upload_ de arquivos</a>  
18.1. Carregando arquivo a partir do formulário  
18.2. Configurando armazenamento  
18.3. Apresentando as imagens  
18.4. Excluindo o arquivo direto no _service_  
18.5. Excluindo o arquivo através de um evento  
19. <a href='#19'>Usando _jobs_</a>  

## 1. Configurando o ambiente<a name='1'></a>
### 1.1. Criando um projeto
O primeiro passo é se certificar de que todas as ferramentas/_softwares_ necessários estão instalados: o mínimo é o PHP 7.1.3 (ou maior) e o composer (pela facilidade para criar e gerenciar o projeto; além disso, é necessário que algumas [extensões](https://laravel.com/docs/5.8) do PHP estejam habilitadas.

Isso pronto, podemos rodar o comando que cria um projeto Laravel, então vá até o diretório que pretende desenvolver o projeto e rode o comando abaixo:
```sh
$ composer create-project --prefer-dist laravel/laravel nome-projeto 5.8.*
```
isso vai criar uma pasta com nome `nome-projeto` e todos os arquivos necessários para dentro dela (_commit_ [be90199](https://github.com/brnocesar/alura/commit/be9019905600c96afa5fc8307b43b587e46b8e89)). Além disso, especificamos a versão 5.8 do Laravel.

### 1.2. Estrutura de arquivos
Entrando no diretório do projeto podemos ver suas pastas:
- `app`: contém toda lógica da aplicação
- `config`: arquivos de configuração
- `database`: é onde ficam as migrations
- `resources`: fica toda a parte visualizada pelo usuário
- `routes`: armazena as rotas da aplicação

### 1.3. A primeira rota
Vamos começar falando sobre as rotas, que são o _"mapeamento de URLs para ações no PHP"_. Ao entrarmos da pasta `routes` podemos observar alguns arquivos de rotas, cada uma específica para um tipo de aplicação. No caso de uma aplicação web podemos usar o arquivo `routes/web.php`.

Ao abrir este arquivo podemos ver a definição de uma rota, note que o verbo é GET, o primeiro parâmetro é `'/'` e o segunda uma função. Podemos assumir que o primeiro parâmetro se trata da rota, então vamos acessá-la para ver o que temos.

Para levantar um servidor de desenvolvimento no Laravel podemos usar o Artisan, que é uma ferramenta de linha de comando que nos oferece uma série de facilidades no desenvolvimento de projetos Laravel. O comando é:
```sh
$ php artisan serve
```

Então acessamos a rota raiz do domínio `localhost:8000` (por via das dúvidas, sempre confira a porta na saida do terminal) e vemos a tela de baos vindas do Laravel.

Agora vamos criar nossa própria rota que vai apresentar um texto de nossa escolha. Definimos uma nova rota no primeiro argumento e printamos alguma coisa ao invés de retornar uma _view_. No exemplo da [minha implementação](https://github.com/brnocesar/alura/commit/dd422984a25273af237ce4700e56ad67a21d3262), ao acessar a rota `localhost:8000/ola` recebo o texto `"Olá Mundo!"` na tela.

Mas como a aplicação é para gerenciar minhas séries, vou trocar o texto inicial (e a rota) para algo mais próximo do contexto. Além disso vou apresentar o conteúdo como HTML (_commit_ [6a4eb96](https://github.com/brnocesar/alura/commit/6a4eb969212fa08dce5085bb5a7a4060af1e0cf5)). Agora ao acessar a rota `localhost:8000/series` é possível observar que existe uma lista HTML na página retornada.

<p style="text-align: right"> <a href="#topo">voltar ao topo </p>

## 2. _Controllers_<a name='2'></a>
Note que neste momento as rotas estão fazendo mais que sua responsabilidade, que é "levar à execução de uma ação". Como essa ação será executada é responsabilidade de outro tipo de arquivo, portanto, vamos criar um _controller_ e mover este código que foi escrito na rota.

Navegamos até a pasta `app/Http/Controllers` e criamos um arquivo chamado `SeriesController.php`. Note que o `namespace` deve reproduzir a árvore de diretórios e a nossa classe deve herdar a classe `Controller`.

Então vamos mover o código que está na rota para esta classe, fazendo as devidas modificações. Precisamos definir um método público e com nome em nossa classe para receber o código, e na rota devemos especificar o que será executado quando esta rota for acessada.  
No lugar da função na rota informamos: o caminho relativo à pasta Controllers (`SeriesController`) e o método que será executado (`listarSeries`), unidos por uma arroba (`@`) (_commit_ [b998c74](https://github.com/brnocesar/alura/commit/b998c742a14108e05ea1c8260262f10fb21726d7)).  
Feito isso basta acessar a rota novamente e conferir que está tudo certo (é para estar tudo certo, se não tiver você fez alguma coisa de errado, ou a sintaxe do Laravel mudou desde que isso foi escrito).

### 2.1. Acessando dados da requisição
Podemos [injetar uma dependência](https://github.com/brnocesar/alura/tree/master/php/formacao_php/8-mvc#9-4) no nosso método para que ele possa receber dados de uma requisição através da classe `Request`. Com isso temos acesso a várias informações interessantes como a URL da requisição e aos parâmetros passados (_commit_ [56c9087](https://github.com/brnocesar/alura/commit/56c90871bc1fef44b67b247606894d41f4d39a54)).

<p style="text-align: right"> <a href="#topo">voltar ao topo </p>

## 3. _Views_<a name='3'></a>
### 3.1. _View_ de listagem de séries
Vamos isolar mais as responsabilidades da nossa aplicação. Agora vamos retirar o HTML do _controller_ e colocá-lo no seu devido lugar: nas _views_.

Antes disso vamos alterar o nome do nosso método para `index()`, pois este é o método que me apresenta todas as minhas séries e assim vamos estar de acordo com o padrão do Laravel (_commit_ [f3bb924](https://github.com/brnocesar/alura/commit/f3bb924de8cebf4bf9263dc73d41c67babf8b23a)).

Feito isso, na pasta `resources/views` criamos a pasta `series` e dentro dela o arquivo de _view_ `index.php`. Note que é um arquivo PHP que vai ter a estrutura HTML, assim seremos capazes de acessar variáveis.

No método `index()` agora retornamos o método `view()` que recebe dois parâmetros: o primeiro é o caminho relativo da _view_ com `.` (pontos) no lugar de `\` (barras) e o segundo são as variáveis que a _view_ terá acesso.  
Existem duas formas como as variáveis podem ser passadas para a _view_:
- através de um _array_ associativo em que a chave é a variável acessível na _view_ e o valor a variável do _controller_;
```php
return view('series.index', ['variavelNaView' => $variavelNoController]);
```
- e a outra é usando a função `compact()` do PHP, que busca uma váriavel com o nome passado e retorna um _array_ associativo.
```php
return view('series.index', compact('series'));
```
Isso pode ser visto no _commit_ [1e20b08](https://github.com/brnocesar/alura/commit/1e20b08fea3e7294262907c223cdcab3d3132576).

### 3.2. Estilizando a _view_ com Bootstrap
Uma boa aplicação deve ser agradável aos olhos, então vamos aplicar "estilo" em nossa _view_. Por questão de praticidade vamos usar um _Framework_ de CSS, o [Bootstrap](https://getbootstrap.com/), e na sua página encontramos facilmente o _link_ para incluir essa ferramenta em nosso projeto sem precisar fazer _download_ algum, basta inserir o _link_ na _view_.

Apenas essa ação já é o suficiente para alterar os _bullets_ e a fonte da nossa página, então prosseguimos adicionando classes às _tags_ no nosso HTML, containeres, cabeçalhos...

Pensando mais a frente, também ja criamos um botão para adicionar novas séries (_commit_ [8d81910](https://github.com/brnocesar/alura/commit/8d81910a00492835e4920d77ecd17fa69f304372)).

### 3.3. _View_ para adicionar série
Vamos criar uma _view_ para adicionar novas séries a nossa lista. Devemos então: (i) criar um arquivo de _view_; (ii) criar um método que retorne essa _view_; e (iii) criar uma rota para acessar esse método. Além disso colocamos essa rota no botão da _view_ de listagem (_commit_ [8486838](https://github.com/brnocesar/alura/commit/848683863cb48ebfacfe1e4851df9507dfa9398a)).

### 3.4. Blades
Note que nas duas _view_ criadas praticamente todo HTML é repetido, por isso vamos começar usar um recurso do Laravel que permite definir um _layout_ que pode ser usado por qualquer _view_ do projeto.

Criamos um arquivo `layout.blade.php` na pasta `views` e colocamos todo HTML que é comum (_commit_ [8223c05](https://github.com/brnocesar/alura/commit/8223c05d55eeec63712ab97723711d9cf0a967e0)).

O Blade utiliza o conceito de seções, o que significa que podemos definir (rotular) seções no _layout_ que receberão diferentes conteúdos (_commit_ [b05fc7f](https://github.com/brnocesar/alura/commit/b05fc7f6be94f343389cc41c7aaee3a5bc265f8f)). Ou seja, o _layout_ contém apenas uma estrutura e nessa estrutura teremos partes que serão informadas pelo arquivo que o usar.

Agora que temos um _layout_ Blade, podemos modificar nossas _views_ para utilizarem-no. O primeiro passo é renomear as _views_ para o padrão Blade. Após isso, a primeiro coisa em cada _view_ deve ser a informação de que elas "herdam" o _layout_, e aṕos isso vamos abrindo e fechando cada seção adicionando o devido conteúdo (_commit_ [919751d](https://github.com/brnocesar/alura/commit/919751d2633321b6f097016a77f335442a115db8)).

Outra funcionalidade do Blade é permitir escrever PHP de uma forma mais amgável utilizando `@` ao invés de `tags` (_commit_ [4e0a1f0](https://github.com/brnocesar/alura/commit/4e0a1f0d4eb87fc5a7f705880e1d1a35694818c9)).

<p style="text-align: right"> <a href="#topo">voltar ao topo </p>

## 4. Criando registros<a name='4'></a>
### 4.1. Configurando o Banco de Dados
Até este ponto nossa aplicação possui duas páginas, uma para listagem das séries cadatradas e outra para cadastrar novas séries, então agora vamos implementar a funcionalidade que vai permitir adicioná-las.

A primeira coisa a ser feita é criar o Banco de Dados onde os registros serão armazenados, então vamos entender como o Laravel lida com isso. Existem dois lugares onde inserimos as informações do Banco:
- no arquivo das variáveis de ambiente: `.env`
- e no arquivo de configurações da Base de Dados: `config/database.php`

O arquivo de configurações retorna um _array_ associativo em que o primeiro elemento é a chave `'default'` e o valor é o nome da conexão da Base de Dados. Para retornar o nome da Base de Dados primeiro será verificado se existe algum valor na variável `DB_CONNECTION` do `.env` e se não encontrar retorna o segundo parâmetro passado.

No nosso projeto vamos usar o SQLite então podemos definir o nome `'sqlite'` no arquivo das variáveis de ambiente e no de configurações (segundo parâmetro). No arquivo das variáveis de ambiente podemos comentar as outras linhas do bloco DB.
```
DB_CONNECTION=sqlite
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=laravel
# DB_USERNAME=root
# DB_PASSWORD=
```

Quando verificamos conexão `'sqlite'` no arquivo de configurações vemos que há um "database_path" que é o caminho da Base de Dados relativo a pasta `database`, então podemos criar nossa Base de Dados nesse local. Caso você esteja usando algum sistema de versionamento notará que este arquivo não está listado, o motivo é que o arquivo `database/.gitignore` possui a instrução `*.sqlite` em seu conteúdo, portanto, todos os arquivos com essa extensão serão ignorados pelo Git (_commit_ [d17c6f7](https://github.com/brnocesar/alura/commit/d17c6f71c3c4a15905553482d4b897f1199e9661)).

### 4.2. _Migrations_
Agora que temos um Banco podemos podemos começar a pensar na tabela que vai armazenar as séries. Para criar uma tabela vamos usar o Artisan, com o comando `make:migration`:
```sh
$ php artisan make:migration criar_tabela_series
```
Este comando cria uma _migration_ com o nome passado ao comando no diretório `database/migrations`. Dentro do arquivo criado temos duas funções: `upd()` e `down()` (_commit_ [9766b2c](https://github.com/brnocesar/alura/commit/9766b2ce4a11954847c8de111d5d585bbf73b898)).

Como vamos criar a tabela usaremos a função `up()`. No corpo da função usaremos o método estático  `Schema::create()` passando como primeiro parâmetro uma _string_ com o nome da tabela e o segundo é uma função de _callback_ que irá conter informações sobre as colunas tais como: tipo do dado que será armazenado, nome da coluna, valor padrão, e etc (_commit_ [7c02d62](https://github.com/brnocesar/alura/commit/7c02d621f796d1289d9628b60d7a2a4cff2f0548)). Note que não foi necessário definir o tipo do dado de acordo com o SGDB usado, por exemplo, se a _string_ é `char`, `varchar` e tal, o Eloquent (ORM usado pelo Laravel por padrão) faz todas essas abstrações por você.

Para rodar a _migration_ e de fato criar a tabela no Banco rodamos o comando abaixo:
```sh
$ php artisan make:migrate
```

Note que além da _migration_ `criar_tabela_series` foram rodadas outras duas, estas representam as tabelas de usuários e senhas resetadas e já são criadas por padrão no Laravel.

### 4.3. _Model_
Antes de começar-mos a criar registros na tabela 'series' devemos ter uma classe que "modele" esses registros. Para criamos um arquino na pasta `app` chamado `Classe.php` e dentro dele teremos a classe Serie herdando a classe Model do Eloquent. Essa herança vai permitir a classe Série usar vários métodos para operar no Banco.

O Laravel assume o nome da tabela como o minúsculo plural em inglês de sua classe, se este não for o caso, devemos informar o nome da tabela usando o atributo `$table`.

Como os campos do formulário são enviados pelo método POST, devemos criar uma rota com este verbo. Ainda no formulário precisamos adicionar a diretiva do Blade `@csrf` dentro da _tag_ `<form>`, isso é necessário pois o Laravel precisa receber um _token_ de verificação gerado por ele mesmo quando um POST com dados de um formulário chegar para ele.

Como não criamos as coluans `updated_at` e `created_at` na tabela, precisamo informar que o Laravel não precisa preenchê-las, pois ele irá tentar fazer isso por padrão.

Após isso (_commit_ [2e7fd3a](https://github.com/brnocesar/alura/commit/2e7fd3a18cc1998de0c6f982c1f92e356ee24f86)), é possível começar a inserir registros em nossa tabela de séries. Mas se fizermos isso no formulário não recebemos nenhum _feedback_ de nossa ação, na verdade nem somos redirecionados para alguma outra rota. Então vamos ao menos apresentar uma mensagem na tela informando o nome da série criada (_commit_ [5052030](https://github.com/brnocesar/alura/commit/50520302c0337e0cfc6aea68bcdf09be56ecd444)).

### 4.4. Consultas no Banco
Agora que temos séries cadastradas no Banco vamos começar a recuperá-las para apresentar na página de listagem de séries. Lembrando que nossa Classe modelo herda a classe Model, portanto, podemos usar o método estático `all()` que retorna todos os registros de uma classe. Além disso devemos especificar na _view_ que queremos apresentar o atributo `nome` dos objetos do tipo Serie, isso é necessário porque o Laravel identifica que estamos mandando uma coleção de objetos para a _view_ e envia os dados em formato JSON (_commit_ [5f76081](https://github.com/brnocesar/alura/commit/5f76081cd14fcf69cf3abad560afcbb1253dc19d)). 

### 4.5. Atribuição em massa
Podemos refinar o código que cria um registro usando o conceito de atribuição em massa. No método `store()` do _controller_ usamos o método estático `create()` de Model, e assim podemos criar um objeto e atribuir valores a seus atributos de uma vez só, passando um _array_ associativo com os atributos e valores. Isso elimina a necessidade de instânciar a classe e atribuir o valor de cada atributo individualemnte.

Antes de usarmos esse recurso devemos indicar ao Laravel quais atributos serão permitidos na atribuição em massa. Isso é feito no Model Serie através do do atributo `$fillable`, passando um vetor com os campos que serão permitidos (_commit_ [c2f52dd](https://github.com/brnocesar/alura/commit/c2f52ddb07e413721f8700934a9c8a4321a19c26)). 

Dependendo de como montamos o formulário da _view_ (se os nomes dos campos coincidem com os nomes dos atributos da classe) podemos ser ainda mais práticos e passar a _request_, que no fim das contas é um _array_ associativo (_commit_ [8f79508](https://github.com/brnocesar/alura/commit/8f7950805fbd7366a2b714d092f90dfc6e0a976e)).

<p style="text-align: right"> <a href="#topo">voltar ao topo </p>

## 5. Lapidando a aplicação (parte 1)<a name='5'></a>
Podemos realizar algumas alterações que tornem a aplicação mais agradável para os usuários como: redirecionamentos (_commit_ [48db541](https://github.com/brnocesar/alura/commit/48db5414591a70c44a9515e34d713a2a800b56a5)) ou apresentar de mensagens de _feeback_ (_commit_ [b8f551f](https://github.com/brnocesar/alura/commit/b8f551fc5022fe57f5576047c744510e8daf39b7)), após uma ação ser realizada.

<p style="text-align: right"> <a href="#topo">voltar ao topo </p>

## 6. Destruindo registros<a name='6'></a>
Vamos implementar a funcionalidade de excluir séries cadastradas e para isso vamos criar uma função que é acessada através do método POST, para evitar que _bots_ ou automações externas sejam capazes de excluir registros.

Basta criar um botão dentro de uma _tag_ `<form>` e no atributo `action` passar a rota do método que exclue séries e o `ID` da série que deve ser excluída. O problema é que não temos este identificador em nossa tabela de séries.  
Para adicionar esta coluna na tabela podemos simplesmente deletar o arquivo do Banco e recria-lo (já que é SQLite), adicionar a coluna na _migration_ criada e roda-la novamente (_commit_ [b8f551f](https://github.com/brnocesar/alura/commit/b8f551fc5022fe57f5576047c744510e8daf39b7)). Como sou preguiçoso vou pular o primeiro passo e após alterar a _migration_ vou rodar o comando:
```sh
$ php artisan migrate:fresh
```
Esse comando vai "dropar" as tabelas do banco e rodar as _migrations_ novamente (_commit_ [f21d96c](https://github.com/brnocesar/alura/commit/f21d96c2552c980163535ce5d63cb17dfdedd972)).

Agora voltando à função que apaga registros (_commit_ [aa10055](https://github.com/brnocesar/alura/commit/aa10055973cc5b06e900bc105f7524c6218b09c3)), podemos implementar o verbo `DELETE` para esta rota, mas como o HTTP não aceita verbos diferentes de `GET` e `POST`, precisamos indicar isso na Blade através da diretiva `@method`. Além disso podemos alterar um pouco a rota para ficar com uma "carinha" de API (_commit_ [670c5fb](https://github.com/brnocesar/alura/commit/670c5fb29cddc4afba3286001878cafee3e072a4)).

Outro cuidado que podemos ter é prevenir exclusões acidentais e isso pode ser facilmente feito com um alert do JavaScript (_commit_ [cae4d74](https://github.com/brnocesar/alura/commit/cae4d744bd8fc9905a507725ec89192bdcd2801c)).

<p style="text-align: right"> <a href="#topo">voltar ao topo </p>

## 7. Nomeando rotas<a name='7'></a>
Isso é algo bastante simples de se fazer e ao mesmo tempo muito poderoso, pois agora não precisamos nos preocupar em alterar as rotas nos locais em que elas serão acessadas.

Para nomear uma rota basta aplicar o método `name()` nesta rota e passar o valor do nome como parâmetro. Na hora de definir uma rota para ser acessada usamos o _helper_ `route()` que recebe o nome da rota (_commit_ [d3064bc](https://github.com/brnocesar/alura/commit/d3064bcadef57a7fee84c7712f2f1a1816e19f15)).

<p style="text-align: right"> <a href="#topo">voltar ao topo </p>

## 8. Lapidando a aplicação (parte 2)<a name='8'></a>
Podemos mexer no estilo das _views_ para deixa-las mais bonitinhas, alinhando os elementos e adicionando ícones (_commit_ [3abd552](https://github.com/brnocesar/alura/commit/3abd552a5f6f1a4d301ad7e1bebf8bff41e658ab)).

<p style="text-align: right"> <a href="#topo">voltar ao topo </p>

## 9. Validando os dados<a name='9'></a>
### 9.1. `validate()`
Para validar os dados vindos de uma requisição temos o método `validate()` da classe Request. Para usá-lo basta passar um _array_ associativo em que a chave é o nome do campo (da requisição) e o valor são as regras de validação (_commit_ [365857e](https://github.com/brnocesar/alura/commit/365857e84acdcc6e8c59d75914e3f9a95934be12)).

Outra facilidade que o Laravel nos oferece é sobre como dizer ao usuário que essas regras não estão sendo seguidas. Caso algum campo não passe no teste de validação, o Laravel injeta na sessão mensagens indicando qual campo violou qual regra. E na própria [documentação do Laravel](https://laravel.com/docs/5.8/validation#quick-displaying-the-validation-errors) é disponibilizado um fragmento de código para ser colocado nas Blades e mostrar os erros (_commit_ [69adde7](https://github.com/brnocesar/alura/commit/69adde75ebb6cd7623434ef6db456858e5eeae1e)).

### 9.2. _Form Request_
O método `validate()` pode ser uma boa forma de validar requisições quando temos apenas um ou dois campos, mas em situações que esse número é maior o ideal é utilizarmos uma classe própria para essa tarefa. Essas classes sào chamadas de _Form Request_ e para cria-la usamos o comando:
```sh
$ php artisan make:request SeriesFormRequest
```
que cria um arquivo com nome `SeriesFormRequest.php` na pasta `Requests` dentro de `Http` (_commit_ [668a220](https://github.com/brnocesar/alura/commit/668a220a5cb7da2a3eafaa31727a92dbab8e430c)).

Para usar essa classe precisamos apenas: dizer que o usuário esta autorizado a realizar esta requisição na função `authorize()` (qualquer usuário no caso, já que ainda não temos um sistema de autenticação); colocar a regra de validação na função `rules()` e definir que nosso método `store()` em `SeriesController` espera receber um Request específico, no caso o `SeriesFormRequest`.

Além disso podemos ainda personalizar as mensagens de erro, definindo uma mensagem para cada regra (_commit_ [c141d7e](https://github.com/brnocesar/alura/commit/c141d7eae3c21015dcbed4d013a0ebfa7a478381)).

<p style="text-align: right"> <a href="#topo">voltar ao topo </p>

## 10. Novos _models_<a name='10'></a>
Para representar melhor uma série podemos armazenar informações sobre suas temporadas e episodios, então vamos modelar essas duas classes. Os atributos e relacionamentos das classes vão ser:
- Serie
    - id: identificador único;
    - nome
    - temporadas: cada série pode ter várias temporadas
- Temporada
    - id: identificador único;
    - numero
    - serie: cada temporada pertence a uma série
    - episodios: cada temporada pode ter vários episódios
- Episodio
    - id: identificador único;
    - numero
    - temporada: cada episódio pertence a uma temporada

Agora que ja temos um modelo mais completinho para nosso sistema, podemos começar a criar nossos _models_ e _migrations_, e para isso vamos utilizar o `artisan`. Rodando o comando:
```sh
$ php artisan make:model Temporada -m
```
será criado um arquivo `Temporada.php` na pasta `app` com a estrutura básica de um arquivo de _model_. Além disso, como adicionamos a _flag_ `-m` será criada uma _migration_ automaticamente. Para a classe Episodio basta repetir o procedimento trocando o nome do _model_ passado (_commit_ [c9aefd8](https://github.com/brnocesar/alura/commit/c9aefd851a6fc5cf075f202c38913f79182205b5)).

### 10.1. Relacionamentos
Para definir relacionamentos no Laravel não usamos atributos, mas sim métodos. Vamos criar métodos com um nome pela qual queremos acessar a relação.

Por convenção, quando temos uma relação do tipo "um para muitos" ou "muitos para muitos", o nome do relacionamamento é definido no plural (no lado "múltiplo") e singular no outro lado.

Dessa forma se queremos acessar a série de um episódio usamos `$temporada->serie`, ou os episodios de uma temporada `$temporada->episodios`.

Note que conforme [definimos o relacionamento](https://github.com/brnocesar/alura/commit/b6325438eb6964deb1207cd986c9683eecdea777) de um lado, temos a relação inversa no outro:
- 1:1 - `hasOne` <-> `belongsTo`
- 1:N - `hasMany` <-> `belongsTo`
- N:N - `belongsToMany` <-> `belongsToMany`

### 10.2. _Migrations_
Agora definimos os atributos de cada uma das classes em sua _migration_ e também os relacionamentos, ou seja, ao que este relacionamento referência (_commit_ [a228b70](https://github.com/brnocesar/alura/commit/a228b70058013cef7ee33150f3279f77b8340d5c)). 

<p style="text-align: right"> <a href="#topo">voltar ao topo </p>

## 11. CRUD de séries<a name='11'></a>
### 11.1. Modificando a criação de séries
Vamos modificar o cadastro de séries de modo que seja possível inserir o número de temporadas e a quantidade de episódios por temporada (vamos considerar que todas as temporadas possuem a mesma quantidade de episódios).

O primeiro passo é modificar o formulário (_commit_ [150d0e6](https://github.com/brnocesar/alura/commit/150d0e6bc851cd8046bcf8f324daef01656ff09d)). 

Após isso acrescentamos o código responsável por criar os registros de temporadas e episódios no método `store()` de séries. Note que precisamos adicionar atributos `$fillabele` com os campos que serão passados no `create()` em cada um dos _models_, assim como foi feito com séries. Além, disso foi necessário corrigir um pequeno erro de digitação em uma das _migrations_ xD (_commit_ [f86c23a](https://github.com/brnocesar/alura/commit/f86c23a921bb064b9a4714b8f6cf9c3a03a94e07)).

### 11.2. Listando temporadas
Como as temporadas "fazem parte" de uma série, faz sentido apresenta-las "a partir da série", então vamos criar uma página para isso.

Primeiro criamos um botão na página de listagem de séries que vai levar até a página de listar temporadas (_commit_ [2426111](https://github.com/brnocesar/alura/commit/2426111f53b41d67557797ab5d7c56339fefe3be)). 

Em seguida criamos uma rota e a mapeamos para o método `index()` do controller de temporadas (_commit_ [76af820](https://github.com/brnocesar/alura/commit/76af82028c17e20a84c81665bb820f4c4b6b7fce)).

Agora usamos o _artisan_ para criar um controller chamado `TemporadasController`, com o comando:
```sh
$ php artisan make:controller TemporadasController
```

E dentro deste controller criamos o método `index()` que recupera a coleção de temporadas de uma série e passa essa coleção para _view_ que retorna  (_commit_ [673ab80](https://github.com/brnocesar/alura/commit/673ab80c873d948cccb844bb3131780f623a90b5)).

Finalmente criamos a _view_ de listar temporadas de forma muito similar ao que foi feito com séries (_commit_ [50a3f26](https://github.com/brnocesar/alura/commit/50a3f261530370aae41e39275c9d6b420abc5ec5)). Note que alteramos o método `index()` do controller de temporadas e passamos a enviar apenas o objeto Serie para a _view_, isso está sendo feito pois é possível acessar a coleção de temporadas associadas a esta série através do relacionamento.

### 11.3. Refatorando e separando a "criação de séries"
Vamos separar todo o código relativo a criação de séries em uma nova classe, uma classe de serviço. Então criamos um arquivo chamado `CriadorDeSerie.php` na pasta `app/Service` e movemos o código responsável por criar uma série para um método dentro desta classe, que retorna a série criada.

No método `store()`, onde esse código estava, passamos a receber mais um objeto, um do tipo `CriadorDeSerie` (injeção de dependência), e então, criamos a série a partir do método desse objeto (_commit_ [eabaee8](https://github.com/brnocesar/alura/commit/eabaee8952a04147aa6123db973da42912523137)).  
Mas na classe de serviço é necessário passar os parâmetros que serão usados, assim como no método `store()` (_commit_ [277d406](https://github.com/brnocesar/alura/commit/277d40664fac0049a757c19fe4155e0de0aeac90)).

### 11.4. Refatorando a exclusão de séries
Vamos modificar o código para que as temporadas de uma série e seus episódios sejam excluídos também, pois quando queremos excluir uma entidade, devemos excluir seus objetos relacionados.

A partir dos relacionamentos usamos o método `each()` que recebe uma função anônima que será executado para cada objeto da coleção. Esta função anônima recebe como parâmetro um objeto do tipo em que é aplicado (?) (_commit_ [ec74bd2](https://github.com/brnocesar/alura/commit/ec74bd250a33449b57c3b63dcbdc3920549ed119)).

Após isso podemos extrair esse código para uma classe de serviço específica para esta função (_commit_ [9cf8f34](https://github.com/brnocesar/alura/commit/9cf8f341f4d5e4b05c5d41847aaaa6ffbf824a64)).

Mas note que da forma como foi implementado, se ocorrer um problema em alguma das exclusões é bem provável que processo não seja finalizado e alguns objetos não sejam excluídos. Por isso vamos usar o método `transaction()` da facade `DB`.  
Esse método recebe uma função e executada todo seu código em uma única transação, havendo algum erro em alguma execução interna, a transação não ocorre.

Note que precisamos ter acesso à variável que armazena o nome da série e ao seu ID, por isso "usamos" as variáveis para serem usadas na função anônima. Mas perceba que com a variável que armazena o nome da série passamos seu endereço de memória, se não fizessemos dessa forma o PHP criaria apenas uma cópia da variável para usar na função anônima (_commit_ [5d92c33](https://github.com/brnocesar/alura/commit/5d92c333ee97db83b867959a0a81fe2bd7cf0789)).

Dependendo da situação, muitos níveis de identação dentro de um método podem acabar atralhando a leitura do código, então podemos separar cada uma das responsabilidades (exclusão de um tipo de objeto) em um método separado (_commit_ [18a71c9](https://github.com/brnocesar/alura/commit/18a71c9871613b09d3f37c38a16f7a292249667b)).

### 11.5. Refatorando criação de séries (novamente)
Vamos aplicar a mesma lógica de separação de responsabilidades e usar _transaction()_ na classe de serviço que cria séries (_commit_ [4a9ed10](https://github.com/brnocesar/alura/commit/4a9ed10e565a3a21d75e4980124fb226ed8e1f4e)).

Mas perceba que tivemos que passar quatro variáveis para o _use()_ da função anônima, isso acaba aumentando a complexidade do código bem como a possibilidade de cometer(-mos) erros. Então vamos utilizar outro maneira de informar ao Laravel que uma transação está iniciando e depois que ela terminou, usando `DB::beginTransaction()` e `DB::commit()`. Assim não precisamos usar a função anônima (_commit_ [b5e38a7](https://github.com/brnocesar/alura/commit/b5e38a7539efa078a6a234bea1223314f8f2c9ae)).

### 11.6. Alterando o nome de uma série
Essa ação será feita (pelo usuário) a partir da página de listagem de séries. Vamos adicionar elementos HTML que seram _hidden_ por padrão e quando o usuário quiser editar o nome de alguma série esse campo será apresentado mediante o clique em um botão editar. Essa mudança nos estados (_hidden_ e não-*hidden*) será feita por código JavaScript (_commit_ [ebad098](https://github.com/brnocesar/alura/commit/ebad0989a6214a701d10443265bc9e546a7a8311)).

Criamos um método JavaScript para enviar a requisição ao Laravel com o valor do campo _input_ nome, uma rota, e o método no _server side_ responsável por persistir esta mudança (_commit_ [35b120b](https://github.com/brnocesar/alura/commit/35b120b3222ba4958317e678760bf08c7712d4ae)).

Agora vamos lapidar um pouco mais esta funcionalidade, após clicar no botão que confirma a alteração queremos voltar a esconder o input. Usamos um `then()` no `fetch()`, que através de uma função anônima (_arrow function_) chama a funcão `toggleInput()` passando o ID da série e atribui o novo nome ao conteudo do elemento (_commit_ [dd5df72](https://github.com/brnocesar/alura/commit/dd5df72f471100d62e004af4afd909a368ecf401)). E com isso podemos considerar finalizado o CRUD de séries (_commit_ [b74dda5](https://github.com/brnocesar/alura/commit/b74dda5e58b72ccbcff7f10e11bb17b140d59667)).

<p style="text-align: right"> <a href="#topo">voltar ao topo </p>

## 12. Episódios<a name='12'></a>
### 12.1. Listando episódios
Agora que temos um CRUD para a entidade **Serie** vamos voltar nossa atenção para os episódios. A primeira coisa a ser feita é transformar as temporadas listadas em um link que posteriormente vai levar para a listagem dos respectivos episódios (_commit_ [e5d447f](https://github.com/brnocesar/alura/commit/e5d447f01c78ca00944a2f3d2e978581b8c2e1f7)) e também adicionamos um "selinho" (_badge_) indicando quantos episódio foram assistidos em relação ao total (_commit_ [a3b2781](https://github.com/brnocesar/alura/commit/a3b2781bc5c11a0102d297717d6b11e68faf190f)).

Agora precisamos de um método que retorne a _view_ de listagem dos episódios, então criamos um _controller_ para episódios com o método `index()` (_commit_ [a537d4b](https://github.com/brnocesar/alura/commit/a537d4b8c74363a8b334f6316e8e04c4290e7a51)).  
Criamos a rota para acessar essa página passando o ID da temporada e partir desse parâmetro obtemos a temporada correspondente (_commit_ [64dfc4b](https://github.com/brnocesar/alura/commit/64dfc4b4e2cb8dfba9f6101db230551668e9e3bc)).  
Mas o Laravel possui um recurso que facilita ainda mais esse processo, podemos injetar a dependência de uma classe indicando que queremos receber um objeto desta classe no método, e desde que os parâmetros no método e na URL sejam iguais, o Laravel já retorna um objeto dessa classe usando o `find()` (_commit_ [c5438a7](https://github.com/brnocesar/alura/commit/c5438a7c00edef627d581a4fe81f5a64dd5b1631)). Mas para que isso ocorra apenas um parâmetro pode ser passado na URL (?<sup>*</sup>).

Enfim, a partir do objeto da classe **Temporada** conseguimos acessar a coleção de seus episódios relacionados e [retorná-los](https://github.com/brnocesar/alura/commit/842d3a0ac39fd43dd93b919fbc0cb77aecfb6026) para a _view_ de listagem criada (_commit_ [b6c3421](https://github.com/brnocesar/alura/commit/b6c34212c6736cc30467d4c48f012765275a3203)).

### 12.2. Assitindo episódios
Para marcar os episódios que já foram assitidos adicionamos um _checkbox_ para cada e colocamos a lista de episódios dentro de um _form_ (_commit_ [21ce68e](https://github.com/brnocesar/alura/commit/21ce68e23fe683ad8b50ffb8851330e97f853f24)), pois precisamos enviar essa informação para algum lugar.

Para armazenar a informação de quais episódios ja foram assistidos precisamos adicionar um campo na tabela 'episodios', então rodamos o comando abaixo para criar uma _migration_ que "mexe" nessa tabela (_commit_ [7f694f3](https://github.com/brnocesar/alura/commit/7f694f3d6f342293fdffc69b0ec29fafac5aef69)):
```sh
$ php artisan make:migration AdicionaCampoAssitido --table=episodios
```
no método `up()` da _migration_ definimos a coluna que será adicionada como sendo do tipo _boolean_ e com valor padrão `false` (_commit_ [93d4796](https://github.com/brnocesar/alura/commit/93d4796431311f83e5e2197f703cb66149b8e42b)), e então rodamos a _migration_.

Para facilitar o "recebimento" da informação de quais episódios ja foram assistidos vamos enviar essa informação da _view_ como um _array_ pelo formulário. Para isso basta adicionar um par de colchetes junto ao _name_ do _checkbox_ e definir seu valor como o ID do episódio, assim, quando o formulário for enviado o campo `episodios` será um vetor dos episódios marcados como assitidos (_commit_ [ac038ec](https://github.com/brnocesar/alura/commit/ac038ecb64b2d112c4d9f42d74221931d7885dc8)).  
Agora basta definir a rota, adicionála ao _form_ e crir o método que irá receber a requisição do formulário (_commit_ [c246884](https://github.com/brnocesar/alura/commit/c24688459eff11049a16a783d8aca5ac255f52e0)).

No método que recebe a requisição deste formulário escrevemos um código que verifica se cada episódio foi assitido e atribui o devido valor ao atributo `assistido` da tabela de episódios, para isso usamos o método `each()` que executa uma função para cada objeto de uma coleção e ao final o método `push()` que envia todas as modificações de um objeto e nas suas relações para o Banco. Depois que a informação foi tratada e persistida retornamos a última rota acessada (_commit_ [1507628](https://github.com/brnocesar/alura/commit/1507628bc30a120012c47c6f4a0ab7480deeb074)).

Falta definir uma condição para que os _checkboxes_ fiquem marcados e isso é feito avaliando o valor do atributo `assitido` (_commit_ [a10eab4](https://github.com/brnocesar/alura/commit/a10eab4226b940e8705d5cc89f5bb84c18c91a8e)).  
Também enviamos uma _flash message_ indicando que a lista de episódios assistidos foi modificada (_commit_ [958e799](https://github.com/brnocesar/alura/commit/958e79926f314b67d66b8e8bb2d842867410655f)).

**BUG**: notei que se nenhum episódio fosse selecionado, ao clicar no salvar teríamos um erro pois o campo episódios não existiria na requisição, esse problema foi resolvido [aqui](https://github.com/brnocesar/alura/commit/b621eeee8d004f5961acca8800930c9e6d7055f4).

**REFATORANDO**: vamos refatorar o HTML em nossas Blades referente à exibição das _flash messages_. Então extraio o HTML para um arquivo próprio na raiz da pasta `views` e passo a incluir essa _"subview"_ usando a diretiva `@include()` do Blade(_commit_ [b76e7db](https://github.com/brnocesar/alura/commit/b76e7dba69161e7c0a2b3dd064b7a309a087afd1)).

Para finalizar essa parte da aplicação, vamos exibir o número de episódios assitidos. Para isso escrevemos um método na _model_ `Temporada` que retorna uma coleção que contém apenas os episódios assistidos. Para selecionar os objetos que vão compor essa coleção usamos o método `filter()`, que retorna apenas os objetos que atendem o critério definido. Feito isso, basta acessar este método no objeto `$temporada` na _view_ e usar também o método `count()` (_commit_ [fc536c8](https://github.com/brnocesar/alura/commit/fc536c8f211d9be9d190d5678b2b20092713b176)).

<p style="text-align: right"> <a href="#topo">voltar ao topo </p>

## 13. Autenticação<a name='13'></a>
### 13.1. `Auth`
Para proteger a aplicação é necessário que suas rotas sejam protegidas de forma que apenas usuários autorizados possam acessá-las. Portanto a aplicação deve ser capaz gerenciar registros de usuários e controlar o que cada um pode acessar. O Laravel já fornece uma estrutura bem completa, basta rodar o comando:
```sh
$ php artisan make:auth
```
e então vemos que alguns arquivos foram criados (um _controller_ e algumas _views_) e o arquivo de rotas foi modificado (_commit_ [b619617](https://github.com/brnocesar/alura/commit/b6196179f8073f2ba6663724cfa73bb0656f21e6)).

Agora se acessar-mos as rotas `/login` e `register`, vemos que existem páginas prontas para serem usadas, podemos inclusive nos registrar.

### 13.2. Protegendo rotas
Mesmo com essa nova funcionalidade, se fizermos *logout*, ainda conseguiremos acessar as antigas rotas, então vamos ver formas de protegê-las.

#### 13.2.1. Usando `Auth::check()`
Podemos verificar se o usuário está logado diretamente no método acessado, avaliando o retorno do método estático `check()` da classe **Auth** (_commit_ [fa7c443](https://github.com/brnocesar/alura/commit/fa7c443599a9d42f509aa5b9ecd9f5d79debbca1)).

#### 13.2.2. Em cada rota
Podemos informar em cada uma das rotas se ela é protegida por um "autenticador", adicionando o método `middleware('auth')`. Quando fazemos isso dizemos ao Laravel que alguma classe ou serviço vai realizar uma manipulação nessa requisição antes que ela chegue ao _controller_, nesse caso é a classe **Auth** (_commit_ [09ae1f4](https://github.com/brnocesar/alura/commit/09ae1f49d8b4e6c6eb431999145a6dfc91185a7f)).

#### 13.2.3. No construtor
Os dois processos acima são bastante repetitivos e penosos, se quisermos proteger todas as rotas de um determinado _controller_ podemos chamar o método `middleware()` no construtor. Assim, sempre que um método desse _controller_ for acessado, e consequentemente uma instância sua for criada, e _middleware_ será executado (_commit_ [e461c76](https://github.com/brnocesar/alura/commit/e461c76684fed5a19d1c6e9b6722a8dbb814b25a)).

#### 13.2.4. No _kernel_
Outro local em que podemos definir qual _middleware_ vai ser executado e onde é na classe `app/Http/Kernel.php`. Essa classe possui alguns atributos queTestes automatizados influênciam a aplicação da seguinte forma:
- `$middleware`: especifica os "manipuladores" (_handlers_) executados globalmente, ou seja, para toda aplicação;
- `$middlewareGroups`: permite especificar o grupo em que um _handler_ será utilizado, por exemplo, nas rotas definidas em `web` ou `api`;
- `$routeMiddleware`: e onde são definidos os nomes dos _handlers_.

Poderiamos adicionar o _middleware_ de autenticação no grupo `web` (_commit_ [a2150fe](https://github.com/brnocesar/alura/commit/a2150fed62f7f2b3fd7d48f11b4605a7eaa86bf3)), mas para que isso seja efetivo, precisamos encontrar uma forma de transformar as rotas `/login`, `/register` e  etc em exceçẽs. Pois se isso não for feito a aplicação vai entrar em um _loop_ infinito e receberemos um "redirecionamento incorreto", já que usuários não-autenticados são redirecionados para a rota `/login` que também está protegida.

### 13.3. Autenticação "própria"
Vamos criar nossa "própria" autenticação, mas ainda assim usando alguns recursos do Laravel quando for conveniente. 

#### 13.3.1. Entrando
Vamos criar as rotas para: acessar a página de _login_ e submeter as credênciais; devemos criar a página com o formulário de _login_; e o _controller_ responsável por realizar essas ações.

Note que as rotas criadas acessam a mesma URI, de resto cada uma possui um verbo, acessam diferentes métodos e possuem nomes distintos (_commit_ [cdb8369](https://github.com/brnocesar/alura/commit/cdb83695db7169f0f26a3973ba451666f94bf06f)).

No formulário da _view_ não precisamos definir uma rota no atributo `action` da tag `<form>` pois a submissão ocorre para a mesma URI, apenas temos que especificar o verbo da requisição para que o Laravel saiba qual rota usar. Além disso note que o nome dos campos estão em inglês, isso é devido aos métodos do Laravel que vamos usar e esperam receber campos com esses nomes (_commit_ [267a21d](https://github.com/brnocesar/alura/commit/267a21d7c2b1e7688c690c567d8271206d67fbec)).

No método que realiza o login passamos as credênciais do usuário como um _array_ associativo para o méto estático `Auth::attempt()` que tenta realizar o _login_ e retorna `true` em caso de sucesso. Quando isso acontece o Laravel armazena na sessão que existe um usuário logado e qual é.

Se o _login_ for realizado redirecionamos para a página de listar séries e do contrário redirecionamos de volta com uma _flash massage_ informando o erro (_commit_ [ca9ba2b](https://github.com/brnocesar/alura/commit/ca9ba2bd7e1e744d322dd735391dc7820ee3a2b5)). 

Mas perceba que se já estamos logados e tentamos realizar _login_ novamente, recebemos o mesmo erro, então adicionamos uma condição que avalia isso e envia uma mensagem mais adequada (_commit_ [f98c03f](https://github.com/brnocesar/alura/commit/f98c03f6b514d726175275ac8e75785ae5270a33)). 

Se tivéssimos nomeado os campos do formulário com outros nomes, que não os usados pelos componentes de autenticação do Laravel, teríamos que específica-los ao passar o _array_ para o método `attempt()` (_commit_ [6849064](https://github.com/brnocesar/alura/commit/6849064f7b5595d6ba21ce6e8163ffc374c4ad8e)).

Por fim protegêmos todas as rotas que acessam algum método do _controller_ de séries (_commit_ [c2e3172](https://github.com/brnocesar/alura/commit/c2e317210cbb6c0a3756c59a672cabc0e01c2ebe)).

#### 13.3.2. Registrando novos usuários
Vamos adicionar essa funcionalidade na autenticação "própria". No _controller_ da autenticaçao própria criamos os método que vão retornar a paǵina com o formulário e o que vai persistir os dados no Banco.

Em seguida criamos as rotas de forma bem similar ao que foi feito para o _login_ próprio, fazemos elas "iguais".

Criamos a _view_ com o formulário (_commit_ [71baa0d](https://github.com/brnocesar/alura/commit/71baa0d4692a22584cfbd9f542e3b0b761e5136c)).

E finalmente, implementamos a lógica no _controller_ (_commit_ [171917c](https://github.com/brnocesar/alura/commit/171917cad4b98f0a9bf314c3968a0d3ea4fac8ee)). Novamente, o fato de usarmos os nomes dos atributos do _model_ **User** nos campos do fomrulário nos traz uma facilidade na hora de criar um objeto desta classe. Mas perceba que antes disso usamos o método `make()` para criptografar a senha, essa é a forma padrão do Laravel criptografar informações.

### 13.4. Melhorando a navegação
Vamos adicionar mehorias na navegação começando por adicionar uma _navbar_ no arquivo `lauoyt.blade.php`, para maiores detalhes consulte a [documentação do Bootstrap](https://getbootstrap.com/docs/4.0/components/navbar/). Nesta _navbar_ vamos ter um botão que envia para a página de listar séries e outro que realiza o _logout_. 

Como o código responsável por deslogar um usuário é pequeno, poderíamos escrevê-lo até mesmo na própria definição da rota usando uma função anônima (_commit_ [de2f0c4](https://github.com/brnocesar/alura/commit/de2f0c42b12010939ead28c6c67c019bd73f42c0)). Na imensa maioria dos casos, isso será considerado uma prática ruim, então vamos escrever um método no _controller_ de autenticação própria (_commit_ [81f1725](https://github.com/brnocesar/alura/commit/81f172525f42d68e5b6bb2297c45a21e39348690)).

Agora vamos começar a esconder/mostrar alguns elementos em função de ser um usuário autenticado ou não que está acessando. O primeiro elemento que vamos mexer é o botão de "Sair", que queremos apresentá-lo apenas se o usuário está logado, do contrário exibe opções relativas a _login_ e registro (_commit_ [2703d65](https://github.com/brnocesar/alura/commit/2703d65de3e622c407799a399681c8493e5e624d)).

Seguindo essa linha, vamos alterar os níveis de permissão relativos às séries. Primeiro retiramos o _middleware_ de autenticação do _controller_ de séries e o colocamos apenas nas rotas que não queremos permitir acesso para "visitantes" (_commit_ [50ca623](https://github.com/brnocesar/alura/commit/50ca6231b7d87198384885af8ee7d022ec10f5fa)). 

Escondemos os elementos (botões, campos, _checkboxes_) usados para acessar as ações proibidas aos visitantes (_commit_ [c031d3f](https://github.com/brnocesar/alura/commit/c031d3f48bdfc6eac237c3ff30c066d14ad55751)).

### 13.5. _Middleware_
Quando tentamos acessar uma rota protegida, como visitante, somos redirecionados para a rota `/login` que é página de _login_ do Laravel. Se quisermos mudar isso, para que o redirecionamento seja feito para a rota `/entrar` temos duas opções: modificar o _middleware_ padrão (que está sendo utilizado neste momento) ou criar nosso próprio.

#### 13.5.1. Modificando o _middleware_ padrão para autenticação
Para encontrar este _middleware_ podemos ir através do arquivo `app/Http/Kernel.php` e no vetor `$routeMiddleware` vemos que ele se encontra na pasta `\App\Http\Middleware` sob o nome de `Authenticate.php`. 

Abrindo esse arquivo vemos que existe apenas um método nessa classe e a única coisa que ele faz é retornar a rota `/login` de acordo com uma condição.

Para resolver nosso problema por esse caminho basta trocar pela rota `/entrar` (_commit_ [071d0c0](https://github.com/brnocesar/alura/commit/071d0c0b9d02bed07167cca0a4376f39405b7172)).

#### 13.5.2. Criando nosso próprio _middleware_ para autenticação
Para criar um _middleware_ temos um comando do Artisan:
```sh
$ php artisan make:middleware Autenticador
```
ao rodar este comando será criado uTestes automatizadosm arquivo chamado `Autenticador.php` na pasta `\App\Http\Middleware`. Neste arquivo temos um método `handle()` que recebe dois parâmetros: uma `$request` e uma função do PHP indicando o próximo _middleware_ a ser executado (_commit_ [0a133b0](https://github.com/brnocesar/alura/commit/0a133b0ddbbd4911b9f9110854d4bcb3eb44ffb7)). 

Mas vamos discorrer mais um pouco sobre o que é um _middleware_ e para que ele serve. Quando acessamos uma rota é com o objetivo de executar algum método (em geral) em um _controller_, que pode servir para retornar um _view_ ou persistir algum dado no Banco. Ou seja, nós enviamos informações (requisição) **para** o método e esperamos obter uma resposta de acordo com sua execução.

Os _middlewares_ são como "filtros" que vão atuar sobre a requisição ou resposta desse código, como por exemplo, existe um _middleware_ que verifica a existência do token `@csrf` em requisições com verbo POST (não tenho certeza se é essa a condição pra verificar isso<sup>(*)</sup>).

Para "filtrar" a requisição, ou seja, fazer algo antes do _controller_ ser executado, basta adicionar o código responsável por isso no corpo da função `handle()`. Se for pra avaliar a resposta, ainda não sei, mas ja deixo anotado aqui pra procurar melhor depois<sup>(*)</sup>.

Nesse caso, o _middleware_ será usado em rotas protegidas é então queremos que ele verifique se o usuário está logado e se não estiver deve redirecionar para a rota `/entrar` (_commit_ [06a3c8c](https://github.com/brnocesar/alura/commit/06a3c8c4ca6c47b7ba2084a782f8443045bc3674)).

Para facilitar o uso do nosso autenticador próprio podemos definir um nome para ele no arquivo `app/Http/Kernel.php` no vetor `$routeMiddleware` (_commit_ [52398e4](https://github.com/brnocesar/alura/commit/52398e4b3ac3c36aa4f43e265f45ba6fedcb19b1)).

Mas agora que usamos um _middleware_ em que temos um maior controle do que ele faz, podemos testar mais uma vez adicionar o autenticador no grupo de _middlewares_ executados para todas as rotas. Para isso é claro, devemos especificar as rotas que ele deve ignorar e fazemos isso avaliando o retorno de `$request->is()` que recebe "padrões" de URI como parâmtro (_commit_ [d55662f](https://github.com/brnocesar/alura/commit/d55662f84f6af0850cb4b037a1b52f97e089b778)).

<p style="text-align: right"> <a href="#topo">voltar ao topo </p>

## 14. Testes automatizados<a name='14'></a>

O PHPUnit é um _framework_ de testes do PHP que vem integrado ao Laravel por padrão e roda na linha de comando.

### 14.1. Primeiro teste (`Temporada`)

O primeiro teste que faremos será em cima do _model_ `Temporada`, vamos verificar se o método `getEpisodiosAssistidos()` está retornando a quantidade correta de episódios assistidos. 

Podemos criar um arquivo para testes usando o Artisan:

```sh
$ php artisan make:test TemporadaTest --unit
```

com este comando criamos um arquivo de nome `TemporadaTest.php` na pasta `tests/Unit` (_commit_ [a01f739](https://github.com/brnocesar/alura/commit/a01f7393cd0a9c9e21d18fe973f35d93c0a06aa8)), se não for passado o parâmetro `--unit` o arquivo será criado na pasta `tests/Feature`. Não sei (ainda) qual a implicação disso.

**OBS. 1:** É importante se atentar que o nome do arquivo de testes deve terminal com `Test`, pode ter qualquer coisa antes, desde que o final seja `Test` (hehe).  
**OBS. 2:** As funções dentro de um arquivo de testes podem ter qualquer nome, **desde que** comecem com `test`.

No arquivo criado já vem um teste para verificar se `true` é `true`. Além disso, por padrão projetos Laravel vem com outros dois testes: `tests/Unit/ExampleTest.php` é igual mencionado acima; e `tests/Feature/ExampleTest.php` verifica se se a rota `/` existe. Você pode excluí-los se quiser.

No método `testeExample()` apagamos o exemplo que ja veio e começar a adicionar o código do nosso teste: 
- criamos uma instância de Temporada e **algumas** de Episódio;
- atribuímos `true` ou `false` para o atributo `assistido` dos objetos Episodio (da forma como você quiser);
- usando o método `add()`, relacionamos os objetos Episodio à nossa instânica de Temporada;
- obtemos a coleção de episódios assistidos com o método `getEpisodiosAssistidos()` (_commit_ [40b62a3](https://github.com/brnocesar/alura/commit/40b62a369b92e9580247e7d9d5c3a6ad3a8c05c6))  

chamamos o método `$this->assertCount()` passando como parâmetros o tamanho esperado para a coleção e a coleção que será avaliada. Para rodar os testes usamos o comando abaixo:

```sh
$ vendor/bin/phpunit
```

Agora podemos adicionar outra verificação: se o atributo `assitido` possui valor `true` para todos os objetos da coleção de episódios assistidos (_commit_ [1011dc5](https://github.com/brnocesar/alura/commit/1011dc5638187887b6dc71f19082bb4205a68d81)).

Considerando que teremos vários testes para a classe Temporada, por uma questão de organização, faz mais sentido separá-los em métodos dentro do arquivo de testes para Temporada. Para preparar o cenário de testes apenas uma vez e não ficar repetindo código, podemos utilizar o método `setUp()` fornecido pelo PHPUnit. Este método é executado antes de cada teste, então podemos preparar o cenrário de testes nesse método e ao final de sua execução atribuir isso para uma propriedade da classe de testes (_commit_ [b55442f](https://github.com/brnocesar/alura/commit/b55442f195d34f8333580151418d32cdb44ddd41)).

### 14.2. Testando inserção de registros no Banco (`CriadorDeSerie`)

Agora vamos realizar testes acerca da criação de registros no Banco e vamos fazê-los em cima do _service_ `CriadorDeSerie`. Após criar o arquivo para testes instânciamos o "criador de séries" e criamos uma série de nome qualquer, com uma temporada e um episódio por temporada.

Então adicionamos as verificações:
1. `$serieCriada` é uma instância de `Serie`?
2. na tabela `'series'` existe uma série com nome `$nomeSerie`?
3. na tabela `'temporadas'` existe algum registro com `'serie_id'` igual a `$serieCriada->id` e com `'numero'` igual a `1`?
4. na tabela `'episodios'` existe algum registro com `'numero'` igual a `1`? (_commit_ [68c5f5d](https://github.com/brnocesar/alura/commit/68c5f5dad760267f042e0f64111e94282ec8711e))

### 14.3. Rodando os testes em um Banco "na meméria"

Perceba que o último teste de fato realizou a persistência no Banco da aplicação e podemos contonar esse problema realizando os testes em um Banco dedicado a essa finalidade.

Para isso devemos criar um arquivo para as variáveis de ambiente desse Banco chamado `.env.testing` e precisamos colocar apenas as as variáveis que dizem respeito ao Banco. Mais ainda, o SQLite permite usar um Banco na memória, bastando definir o nome da Base de Dados como `:memory:`. Dessa forma, o conteúdo do arquivo `.env.testing` será:

```env
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
```

Além disso devemos dar um `use` na _trait_ `RefreshDatabase` para que esse Banco seja criado na memória (_commit_ [83a5dd9](https://github.com/brnocesar/alura/commit/83a5dd922b368aeb68e275729e4739f19225e445)).

### 14.4. Testando exclusão de registros no Banco (`RemovedorDeSerie`)

Esse é o caso de um teste em que executamos a ação testada entre _asserts_, pois precisamos nos certificar de que o registro que será excluído de fato existia.

Podemos preparar o cenário para o teste na função `setUp()`, onde apenas criamos uma série. Na função em que de fato vão ocorrer os testes:
1. primeiramente, avaliamos se o registro que vamos excluir existe no Banco;
2. instânciamos o `RemovedorDeSerie` e rnta de testes do PHP que pode ser rodada na linhaemovemos a série (criada no `setUp()`) atribuindo seu retorno a uma variável;
3. sabemos que quando uma série é excluída seu `'nome'` é retornado, então verificamos se o retorno da exclusão é uma _string_;
4. verificamos o valor da _string_ retornada, que deve ser igual ao nome passado na criação da série; e
5. nos asseguramos que não existe um registro na tabela `'series'` com o ID da série excluída (_commit_ [81e89bc](https://github.com/brnocesar/alura/commit/81e89bc25d3359247cd47667faaaf95bf50901ee)).

<p style="text-align: right"> <a href="#topo">voltar ao topo </p>

## 15. Envio de _e-mail_<a name='15'></a>

O Laravel já nos fornece uma classe que gerencia vários recursos interessantes para o "envio de _e-mails_" e podemos criar uma classe de _e-mail_ através de um comando _artisan_:

```terminal
php artisan make:mail NovaSerie
```

Como resultado será criado o arquivo `app/Mail/NovaSerie.php`, que tem esse nome pois iremos usar essa classe para notificar os usuários sobre a criação de uma nova Série. Abrindo esse arquivo temos apenas dois métodos: o construtor e um método `build()` (_commit_ [df3656e](https://github.com/brnocesar/alura/commit/df3656ec884fa8302abc1f3d3c51bd0ff560c979)).

### 15.1. Template do e-mail

O método `build()` retorna a _view_ que será o _template_ do e-mail enviado. Esta _view_ se comporta exatamente como as _views_ das páginas que podem ser acessadas na nossa aplicação. Então podemos criar um arquivo Blade com um _layout_ e apenas precisamos adicionar o _dot path_ deste arquivo no método `build()`.

Após isso, podemos criar uma rota de teste apenas para visualizar o _template_ de _e-mail_. A função executada ao acessar essa rota precisa apenas retornar uma instância da nossa classe _e-mail_ (_commit_ [f4db4e0](https://github.com/brnocesar/alura/commit/f4db4e01ef98fc4448a084894bbd4f4e5d85de7d)), que a _view_ com o _template_ será automáticamente retornada.

Em relação a passagem de parâmetros o comportamento é exatamente igual por parte das _views_, a difereça fica por conta da forma como eles são passados **para** os métodos do _back_, ou seja, como a nossa classe de _e-mail_ os recebe. Para que o método `build()` saiba o que enviar, devemos criar atributos que sejam inicializados pelo construtor, dessa forma eles estarão disponíveis para serem usados dentro da classe (_commit_ [88d1792](https://github.com/brnocesar/alura/commit/88d1792a74bb0eb8f831c744defceec5f82876a8)). Se esses atributos forem criados como públicos não é necessário passar um _array_ associativo para  a _view_, eles ficarão disponíveis automáticamente.

#### 15.1.1. _Markdown_

Também é possivel escrever o _template_ de _e-mails_ em _markdown_, para isso basta adicionar a _flag_ `--markdown` passando o `dot path` do template (a partir do diretório _views_).

```terminal
php artisan make:mail NovaSerie --markdown=mail.series.nova-serie
```

Ou se quisermos aproveitar uma classe de _e-mail_ já existente, devemos trocar a chamada do método `view()` pelo método `markdown()` e adequar a estrutura do arquivo do _template_ (_commit_ [033e9a3](https://github.com/brnocesar/alura/commit/033e9a3a097417e1dd771fb45c5fec28a1402d57)). Mais detalhes podem ser vistos na [documentação](https://laravel.com/docs/7.x/mail#markdown-mailables)

### 15.2. Enviando e-mail

Falar sobre as formas de uma aplicação enviar _e-mails_, SMTP, etc.

O Laravel já possui _drivers_ de _e-mail_, então basta configurarmos as devidas credênciais e ele fará todo o resto.

### 15.2.1. _mailtrap_

Neste momento será utilizada a ferramenta [**mailtrap.io**](https://mailtrap.io/), que cria uma "caixa de entrada _fake_" para receber os _e-mails_ da aplicação. Isso é muito útil em ambientes de desenvolvimento, que é o nosso caso.

Após realizar o _login_ no _site_ da ferramenta acesse a "_demo inbox_" e copie os valores das credênciais _Username_ e Password_ e cole nas respectivas variáveis de ambiente, `MAIL_USERNAME` e `MAIL_PASSWORD`. Por padrão o Laravel já vem configurado para o uso do **mailtrap**, então não é necessário alterar mais nada, mas é sempre bom dar uma conferida.

```env
MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=lalalalalala
MAIL_PASSWORD=1234abcd
MAIL_ENCRYPTION=null
```

Se olharmos novamente na [documentação](https://laravel.com/docs/7.x/mail#sending-mail) podemos ver o código necessário para enviar um _e-mail_ e após isso podemos criar uma rota de teste, semelhante ao que foi feito para o _template_ (_commit_ [e918070](https://github.com/brnocesar/alura/commit/e918070441bd03e2163a6f710baafd82537a5066)).

### 15.2.2. Incorporando o envio à regra de negócio

Como o objetivo desses envios é notificar os usuários de novas séries que foram cadastradas, fica claro que devemos disparar o envio de _e-mails_ logo após isso ocorrer. Então devemos colocar o código responsável por isso no método `store()` do _controller_ de **séries**.

Isso pode ser feito de várias formas, aqui eu optei por criar um _service_ que dispara o envio de _e-mails_ e injetar sua dependência no método `store()` (_commit_ [b8852fe](https://github.com/brnocesar/alura/commit/b8852fe6b427aa34a8fc34e840494557cf0c0712)). Depois alterei o código para enviar o _e-mail_ para todos os usuários, enviando apenas um e-mail para uma lista de endereços (_commit_ [0c130b4](https://github.com/brnocesar/alura/commit/0c130b4304321f47155c9f3d9b5e6d17df246a9d)). E por fim iterei sobre a coleção de usuários para que o fosse enviado um _e-mail_ especificamente para cada usuário (_commit_ [15d7e52](https://github.com/brnocesar/alura/commit/15d7e52b46eb91343978494263fddeb5245e4ede)).

### 15.2.3. Adicionando tempo entre os envios

Isso é importante de ser feito pois muitas ferramentas de envio impõem uma taxa limite de envios por segundo. No **mailtrap** por exemplo, o plano gratuito permite apenas dois envios a cada dez segundos. Para "resolver" esse problema basta adicionar um `sleep()` com o tempo adequado no _service_ que dispara os _e-mails_ (_commit_ [741fbb3](https://github.com/brnocesar/alura/commit/741fbb3798870821186fdcd59132a790e473bdcf)).

## 16. Processamento dados com filas<a name='16'></a>

Falar sobre processos síncronos e assíncronos.

O uso de filas permite que sejam executados processos assíncronos, ou seja, a execução do processo seguinte não depende do anteirior terminar. Isso é muito útil para resolver nosso problema dos _e-mails_ que precisam de um intervalo mínimo entre os envios.

### 16.1. Configurando o ambiente

As configurações de ambiente para as filas no Laravel são armazenadas no arquivo `config/queue.php` Neste arquivo já existem algumas conexões previamente configuradas, então basta escolher uma e preencher no `.env`:

```env
QUEUE_CONNECTION=database
```

A opção que selecionamos é a `database`, que vai utilizar uma tabela do banco para armazenar os processos em fila. Como será utilizada uma tabela, precisamos de uma _migration_ para ela, e o _artisan_ fornece um comando específico para criar esta "tabela de _jobs_":

```terminal
php artisan queue:table
```

Como resultado será criado o arquivo `database/migrations/<timestamp>_create_jobs_table.php` que já contém todas as colunas necessárias para representar o processo: é possível especificar a fila (a padrão se chama `default`), na coluna `payload` colocamos todos os parâmetros e etc.

Além disso devemos criar uma tabela para gerenciar os processos que falharem, para que seja possível tentar executá-los novamente, e para esta tabela o comando é:

```terminal
php artisan queue:failed-table
```

Após isso basta rodar essas _migrations_ (_commit_ [622ab9f](https://github.com/brnocesar/alura/commit/622ab9f34f822b47fe994d080168951c99ca217a)).

### 16.2. Enviando processos para a fila

No caso específico dos _e-mails_, em termos do código tudo que precisamos fazer é [trocar](https://laravel.com/docs/5.8/mail#queueing-mail) o método `send()` pelo `queue()` no nosso _service_ que faz o envio dos _e-mails_ (_commit_ [8933eff](https://github.com/brnocesar/alura/commit/8933eff8822291e4261bd1f66ee2a73e5ba215d9)). E para que a fila seja executada devemos rodar um comando _artisan_ que fica "escutando" a fila, existem dois:

```terminal
php artisan queue:listen
php artisan queue:work
```

Basicamente o primeiro é utilizado em ambiente de desenvolvimento (fica lendo o código) e o segundo em produção (usa cache). É possível passar parâmetros nesses comandos para especificar o número de tentativas e o_delay_ entre as tentativas, por exemplo, vamos especificar duas tentativas para o envio de e-mail e um _delay_ de 5 segundos para que a nova tentativa seja executada:

```terminal
php artisan queue:listen --tries=2 --delay=5
```

E outros comandos bastante úteis para o desenvolvimento são:

```terminal
php artisan queue:failed
php artisan queue:retry
```

o primeiro apresenta os processos que falharam (em todas as tentativas) e o segundo retorna esse(s) processo falhado para a fila.

Também é possivel definir o _delay_ entre os processos diretamente no código. Novamente no _service_ para envio _e-mail_, agora trocamos o método `queue()` pelo `later()`, que aceita também uma instância de `DateTime` indicando quando deve ser feito o envio. No caso, foi definido que os envios devem ocorrer a cada 5 segundos (_commit_ [aeba295](https://github.com/brnocesar/alura/commit/aeba2957097d51772bcce229c78695ee0bf69aae)).

## 17. Eventos e ouvintes<a name='17'></a>

Este é um conceito/recurso derivado do _design pattern **observable**_ e com ele podemos escrever um código menos acoplados, ou seja, é possível a aplicação de forma que cada unidade do código (função ou classe) tenha uma única responsabilidade (ou algo próximo disso) e possa executar apenas uma única tarefa. Se funcionamento é baseado na definição de "eventos" e "ouvintes" (_listeners_), onde os _listeners_ serão executados quando seu respectivo evento for gerado.

### 17.1. Criando um _event-listener_ para enviar _e-mail_

O _artisan_ fornece comandos para criar tanto os eventos como os ouvintes:

```terminal
php artisan make:event NovaSerieEvent
php artisan make:listener NovaSerieEmailListener -e NovaSerieEvent
```

como resultado são criados os arquivos `app/Events/NovaSerieEvent.php` e `app/Listeners/NovaSerieEmailListener.php` (_commit_ [ac1c5a5](https://github.com/brnocesar/alura/commit/ac1c5a5599ce2ad58e092062a5b6771d846fc832)), note que no método `handle()` do _listener_ é feita uma injeção de dependência para a classe do evento de forma automática (ou isso não é injeção de dependência?).

No evento precisamos apenas criar atributos públicos que devem ser inicializados pelo contrutor e serão acessados pelos ouvintes. E no _listener_ devemos colocar todo o código que queremos executar dentro do método `handle()` (_commit_ [fb80536](https://github.com/brnocesar/alura/commit/fb80536ec8066852152b18d8a5331bc580dbc1b2)).

### 17.2. Registrando os eventos
Cada evento pode ter vários _listeners_ associados e eles podem ser completamente independentes um do outro. Os _event listeners_ devem ser registrados no arquivo `app/Providers/EventServiceProvider.php` no vetor `listen`, onde o evento será a chave de cada elemento e o valor um _array_ com todos seus ouvintes  (_commit_ [30375eb](https://github.com/brnocesar/alura/commit/30375eba283ce048d4891eea83e24d2caaeb6fc7)).

Outra possibilidade seria registrar os eventos e ouvintes primeiro e depois rodar o comando _artisan_ que gera os eventos e ouvintes listados que ainda não existem:

```terminal
php artisan event:generate
```

### 17.3. Emitindo um evento

Agora devemos "emitir" o evento basta realizar a chamada do método `event()` passando uma instância do evento desejado. No caso queremos emitir esse evento com a criação de uma nova série, então faremos isso no método `store()` do _controller_ de séries (_commit_ [59ec2f1](https://github.com/brnocesar/alura/commit/59ec2f1949b217fd75bc542a848369183ff07958)).

### 17.4. Criando um _listener_ para _log_ da aplicação

Um outro exemplo pode ser a criação de _logs_ para as novas séries cadastradas, pós escrever o código que cria o _log_ (_commit_ [ab6d69f](https://github.com/brnocesar/alura/commit/ab6d69f675cfa754f7d233ebf974bf4f90c34395)), podemos verificar os _logs_ na pasta `storage/logs/` pelo último arquivo criado (depois de executar a ação é claro).

### 17.5. Processando eventos de forma assíncrona

Para executar os _listeners_ de um evento de forma assíncrona basta apenas implementar a interface `ShouldQueue` nos _listeners_ que assim se desejar (_commit_ [7b52b5b](https://github.com/brnocesar/alura/commit/7b52b5b2967af990cd939d8dc7975b5cd5d81f78)).

## 18. Upload_ de arquivos<a name='18'></a>

### 18.1. Carregando arquivo a partir do formulário

O primeiro _upload_ de arquivo que vamos implementar é o da capa das séries. Primeiro precisamos criar um novo campo na tabela de séries, para armazenar o caminho da imagem, e adicionar este campo no _fillable_ do _model_ **Série** (_commit_ [4f50009](https://github.com/brnocesar/alura/commit/4f50009649ef33e27c3b5a984039727b5ad0752e)).

Em seguida vamos alterar o formulário e o _controller_ de cadastro de séries. No formulário começamos adicionando um campo do tipo _file_ para a `capa` e depois precisamos adicionar o atributo `enctype="multipart/form-data"` pois agora também estamos trabalhando com arquivos, esse atributo sempre deve ser adicionado nessa situação. Note que foi feita outra alteração no `form`, o atributo `action` foi retirado, como estamos submetendo o formulário para a mesma rota (apenas mudando o verbo para POST) não é necessário especificar a rota em que p método `create()` está disponível (_commit_ [68232bf](https://github.com/brnocesar/alura/commit/68232bf8ad80171615ad32f23d72e3128671b1b2)).

### 18.2. Configurando armazenamento

Agora vamos cuidar do armazenamento desse arquivo em nossa aplicação. As configurações relativas a armazenamento são feitas no arquivo `config/filesystems.php` e se formos até ele veremos que o _default_ está com o valor `local`, que significa que os arquivos serão armazenados na pasta `storage/app`. Mas para que a aplicação tenha acesso aos arquivos é necessário que eles estejam na pasta `public` (raiz do projeto). O Laravel oferece um forma de "linkar" as pastas `storage/app/public` e `public`, rodando o comando abaixo será criado um _link_ simbólico entre essas pastas:

```terminal
php artisan storage:link
```

Em seguida vamos fazer com que os arquivos sejam salvos em `storage/app/public`. Começamos adicionando a váriavel de ambiente do "sistema de arquivos" com o valor `public` no `.env`:

```env
FILESYSTEM_DRIVER=public
```

e para de fato armazenar o arquivo devemos chamar o método `store()` para o campo da capa no _controller_ de séries. Este método recebe um argumento opcional que é o caminho relativo em que o arquivo será armazenado. Pode ser que você receba uma _exception_ ao tentar relaizar essa ação, nesse caso, provavelmente seja necessário habilitar/instalar a extensão `php_fileinfo`.

Também é necessário modificar o _controller_ para verificar se existe um arquivo selecionado para _upload_ e atribui-lo. E note que no _service_ para criar séries foi adicionado um sinal de interrogação na tipagem da variável `$capa` e retirado o valor padrão, isso indica que a variável deixa de ser opcional mas que pode receber `null` (_commit_ [ed61847](https://github.com/brnocesar/alura/commit/ed6184774152f4aadbfcbea8c8284b0be5a9d0fd)).

### 18.3. Apresentando as imagens

Para começar vamos adicionar uma imagem padrão por que o envio de imagens não é obrigatório (_commit_ [f40a500](https://github.com/brnocesar/alura/commit/f40a5008faa737220cb45b54dd6305b1b3a6fa86)).

Após isso adicionamos a tag `<img/>` nas _views_ de listagem e detalhes das séries e acessamos a URL da imagem através de um _mutator_ definido no _model_ **Série** (_commit_ [6792862](https://github.com/brnocesar/alura/commit/6792862fd579b1aa9930038caad3718245f5d663)).

### 18.4. Excluindo o arquivo direto no _service_

Para também excluir a imagem de capa da série, quando ela é deletada, basta realizar a chamada ao método `delete()` da _Facade_ `Storage` passando o caminho da imagem. No caso isso deve ser feito no _service_ responsável deletar uma série (_commit_ [c1b598f](https://github.com/brnocesar/alura/commit/c1b598f9612f396150ecbcd7af8c3f5199e79d36)).

### 18.5. Excluindo o arquivo através de um evento

Com o objetivo de desacoplar nosso código, deixando o _service_ que remove uma série apenas com esta responsabilidade, vamos criar um evento que será emitido neste _service_. A partir disso criamos um _listener_ síncrono com a reponsabilidade de remover o arquivo da imagem de capa da série, adicionamos o código que deve ser executado e registramos os _event-listener_ no _provider_ (_commit_ [f8aa9d5](https://github.com/brnocesar/alura/commit/f8aa9d5121d5342dec85434e63b81b2309b28dff)).

Se quisermos tornar esse evento assíncrono, colocando-o na fila, precisamos realizar algumas alterações no código. Primeiro devemos fazer o _listener_ implementar a interface `ShouldQueue`. E também precisamos mudar o tipo de dado que estamos passando para o evento, pois quando passamos um _model_ (**Serie** no caso), o _job_ que vai pra fila armazena apenas o `ID` da série, mas como este registro é excluído, não será mais possível acessar o seu atributo `capa`. Então devemos alterar o tipo para um objeto genérico (_commit_ [8f37ac9](https://github.com/brnocesar/alura/commit/8f37ac9d26b25f78ccbce10cd74049a2b5cab462)).

## 19. Usando _jobs_<a name='19'></a>

Quando tivermos que executar um processo que é inerente a uma ação, o mais recomendável é usar _jobs_. Dessa forma deixamos explícita a execução do processo. Ao contrário dos _event-listener_ em que o código a ser processado fica em um arquivo e a execução desse processo é realizada de acordo com a "emissão" de outro, usando _jobs_ precisamos de apenas um arquivo para cada processo.

Para criar um _job_ síncrono rodamos o seguinte comando artisan:

```terminal
php artisan make:job RemoveImagemCapaJob --sync
```

como resultado teremos o arquivo `app/Jobs/RemoveImagemCapaJob.php`. Para que o _job_ seja executado de forma assíncrona, basta fazer a classe implementar a interface `ShouldQueue` ou reodar o comando sem a flag `--sync`.

No construtor do _job_ injetamos a dependência de um objeto genérico e no método `handle()` colocamos o código que deve ser executado. No _service_ que remove séries basta trocar a "emissão" do evento por um _dispatch_ do _job_ (_commit_ [3703b49](https://github.com/brnocesar/alura/commit/3703b4987740c98cd819c6c30b8eba31382376de)).
