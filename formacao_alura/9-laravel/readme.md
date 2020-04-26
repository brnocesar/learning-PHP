# Laravel
O Laravel é um _framework full stack_ do PHP, ou seja, nos oferece ferramentas para desenvolver a lógica da aplicação (_back-end_) e a interface de interação do usuário (_front-end_). Ele segue a arquitetura MVC e oferece uma série de facilidades que permitem um rápido desenvolvimento. O objetivo aqui será desenvolver uma aplicação para gerenciar as séries que o usuário estiver assistindo.

#### Índice
1. <a href='#1'>Configurando o ambiente</a>
2. <a href='#2'>_Controllers_</a>
3. <a href='#3'>_Views_</a>
4. <a href='#4'>Criando registros</a>
5. <a href='#5'>Lapidando a aplicação (parte 1)</a>
6. <a href='#6'>Destruindo registros</a>
7. <a href='#7'>Nomeando rotas</a>
8. <a href='#8'>Lapidando a aplicação (parte 2)</a>
9. <a href='#9'>Validando os dados</a>
10. <a href='#10'>Novos _models_</a>
10. <a href='#11'>CRUD de séries</a>
10. <a href='#12'>Episódios</a>
10. <a href='#13'>Autenticação</a>
10. <a href='#14'></a>
10. <a href='#15'></a>

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

## 2. _Controllers_<a name='2'></a>
Note que neste momento as rotas estão fazendo mais que sua responsabilidade, que é "levar à execução de uma ação". Como essa ação será executada é responsabilidade de outro tipo de arquivo, portanto, vamos criar um _controller_ e mover este código que foi escrito na rota.

Navegamos até a pasta `app/Http/Controllers` e criamos um arquivo chamado `SeriesController.php`. Note que o `namespace` deve reproduzir a árvore de diretórios e a nossa classe deve herdar a classe `Controller`.

Então vamos mover o código que está na rota para esta classe, fazendo as devidas modificações. Precisamos definir um método público e com nome em nossa classe para receber o código, e na rota devemos especificar o que será executado quando esta rota for acessada.  
No lugar da função na rota informamos: o caminho relativo à pasta Controllers (`SeriesController`) e o método que será executado (`listarSeries`), unidos por uma arroba (`@`) (_commit_ [b998c74](https://github.com/brnocesar/alura/commit/b998c742a14108e05ea1c8260262f10fb21726d7)).  
Feito isso basta acessar a rota novamente e conferir que está tudo certo (é para estar tudo certo, se não tiver você fez alguma coisa de errado, ou a sintaxe do Laravel mudou desde que isso foi escrito).

## 2.1. Acessando dados da requisição
Podemos [injetar uma dependência](https://github.com/brnocesar/alura/tree/master/php/formacao_php/8-mvc#9-4) no nosso método para que ele possa receber dados de uma requisição através da classe `Request`. Com isso temos acesso a várias informações interessantes como a URL da requisição e aos parâmetros passados (_commit_ [56c9087](https://github.com/brnocesar/alura/commit/56c90871bc1fef44b67b247606894d41f4d39a54)).

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

## 5. Lapidando a aplicação (parte 1)<a name='5'></a>
Podemos realizar algumas alterações que tornem a aplicação mais agradável para os usuários como: redirecionamentos (_commit_ [48db541](https://github.com/brnocesar/alura/commit/48db5414591a70c44a9515e34d713a2a800b56a5)) ou apresentar de mensagens de _feeback_ (_commit_ [b8f551f](https://github.com/brnocesar/alura/commit/b8f551fc5022fe57f5576047c744510e8daf39b7)), após uma ação ser realizada.

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

## 7. Nomeando rotas<a name='7'></a>
Isso é algo bastante simples de se fazer e ao mesmo tempo muito poderoso, pois agora não precisamos nos preocupar em alterar as rotas nos locais em que elas serão acessadas.

Para nomear uma rota basta aplicar o método `name()` nesta rota e passar o valor do nome como parâmetro. Na hora de definir uma rota para ser acessada usamos o _helper_ `route()` que recebe o nome da rota (_commit_ [d3064bc](https://github.com/brnocesar/alura/commit/d3064bcadef57a7fee84c7712f2f1a1816e19f15)).

## 8. Lapidando a aplicação (parte 2)<a name='8'></a>
Podemos mexer no estilo das _views_ para deixa-las mais bonitinhas, alinhando os elementos e adicionando ícones (_commit_ [3abd552](https://github.com/brnocesar/alura/commit/3abd552a5f6f1a4d301ad7e1bebf8bff41e658ab)).
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

## 13. Autenticação<a name='13'></a>

---
(_commit_ [](https://github.com/brnocesar/alura/commit/))
---
adicionar no composer um comando que crie o arquivo para o banco de dados e rode as migrations