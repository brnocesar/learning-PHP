# Programação funcional

0. [Rodando os _scripts_](#0-rodando-os-_scripts_)
1. [PHP e funções](#1-php-e-funções)  
  1.1. O tipo `callable`  
  1.2. _Closures_  
  1.3. Abordagem imperativa vs declarativa  
2. [Manipulando _arrays_](#2-manipulando-_arrays_)  
  2.1. [Mapeando um _array_ com `array_map()`](#2.1-mapeando-um-_array_-com-`array_map()`)  
  2.2. [Filtrando um _array_ com `array_filter()`](#2.2-filtrando-um-_array_-com-`array_filter()`)  
  2.3. [Reduzindo _arrays_ a um único valor com `array_reduce()`](#2.3-reduzindo-_arrays_-a-um-único-valor-com-`array_reduce()`)  
    2.3.1. `array_map()` com `array_reduce()`  
  2.4. [Ordenando _arrays_ com `usort()`](#2.4-ordenando-_arrays_-com-`usort()`)  
3. [Aplicações parciais](#3-aplicações-parciais)  
4. [Composição de funções](#4-composição-de-funções)  
  4.1. [Abordagem "manual"](#4.1-abordagem-"manual")  
  4.2. [Abordagem "genérica"](#4.2-abordagem-"genérica")  
  4.3. [Utilizando pacotes externos](#4.3-utilizando-pacotes-externos)  
5. [Mônadas](#5-mônadas)  


## 0 Rodando os _scripts_

Após realizar o clone, certifique-se de ter o composer instalado e rode o comando `composer install` dentro do diretório `17-programacao-funcional` que se encontra na raiz do diretório clonado.

Para rodar cada um dos _scripts_ (`teste.php`, `manipulandoArrays.php` e `partial.php`) basta executar `php nomeDoScript.php`.
## 1 PHP e funções

### 1.1 O tipo `callable`

O PHP lida com funções da mesma forma como faz com todos os outros tipos de dados, isso signifa que é possível passar funções como parâmetro, retornar e atribuir uma funções. No caso de funções estamos falando do tipo de dado chamado `callable`.

Vamos utilizar o fragmento de código abaixo para ilustrar a forma como devemos lidar com o tipo `callable`:

```php
<?php

function retornaString(): string
{
    return 'Olá mundo!';
}

function recebeFuncao(callable $funcao): void
{
    echo 'Executando a função recebida: ';
    echo $funcao();
    echo PHP_EOL;
}

recebeFuncao('retornaString');

recebeFuncao(function () {
    return 'Olá mundo! (passando função anônima)';
});

$funcaoAnonima = function () {
    return 'Olá mundo! (passando função anônima depois de atribuí-la a uma variável)';
};
recebeFuncao($funcaoAnonima);

recebeFuncao(fn () => 'Olá mundo! (passando função anônima em "formato" de arrow function)');
```

Definimos uma função que apenas retorna uma _string_, ela será passada como parâmetro. Após isso definimos outra função, que recebe um parâmetro do tipo `callable` e o executa, imprimindo seu retorno. Lembrando que o valor da chamada de uma função é seu valor retornado.

Para passar uma função como parâmetro temos duas opções: através de uma _string_, que seja o nome de uma função válida; ou usando uma função anônima.

Perceba que para o nosso exemplo poderíamos fazer `recebeFuncao(retornaString());` e não seria acusado nenhum erro pois seu retorno é uma _string_, o problema seria percebido na execução, quando não fosse encontrada nenhuma função com o nome retornado.

### 1.2 _Closures_

Este é um conceito que possui significados um pouco diferentes para o PHP e para linguagens de programação de forma geral.

Pensando estritamente no PHP, _Closure_ é a classe que representa uma função anônima, ou seja, funções anônimas vão produzir objetos do tipo `Closure`. Apesar de já as termos citado acima, vamos explícitar a [definição](https://www.php.net/manual/pt_BR/functions.anonymous.php) de funções anônimas: são _"(...) funções que não tem o nome especificado. Elas são mais úteis como o valor de parâmetros callback (...)"_.

```php
$funcaoAnonima = function () {
    return 'Atribui função anônima a uma variável';
};
var_dump($funcaoAnonima);
```

Assim se executamos o código acima, vamos obter a seguinte saída:

```terminal
$ php teste.php
object(Closure)#1 (0) {
}
```

Quando consideramos o conceito de _closures_ para as linguagens de programação, elas são definidas como funcões que possuem acesso ao escopo externo, ou seja, dentro de uma _closure_ podemos acessar variáveis definadas fora de seu escopo.

Para que isso ocorra com funções anônimas no PHP devemos definir isso explícitamente para as váriaveis necessárias.

```php
$variavel = 'Definida no escopo global.';

$funcaoAnonima = function () {
    echo $variavel;
};
$funcaoAnonima(); // saída: Notice: Undefined variable: variavel in <path>\teste.php on line 4

$funcaoAnonima = function () use ($variavel) {
    echo $variavel;
};
$funcaoAnonima(); // saída: Definida no escopo global.
```

Mas o que estamos fazendo aqui na verdade é uma "passagem por valor", a váriavel acessada dentro da função anônima é uma cópia da que foi definida no escopo externo. Se quisermos acessar a variável "original" devemos passar sua referência:

```php
$variavel = 'Definida no escopo global.';

$funcaoAnonima = function () use ($variavel) {
    $variavel = 'Modificada dentro da função.';
};
$funcaoAnonima();
echo $variavel; // saída: Definida no escopo global.

$funcaoAnonima = function () use (&$variavel) {
    $variavel = 'Modificada dentro da função.';
};
$funcaoAnonima();
echo $variavel; // saída: Modificada dentro da função.
```

Portanto, nem todas as funções anônimas no PHP podem ser consideradas _closures_ no sentido geral.

Para as _arrow functions_ isso é um pouco diferente. Elas também são implementadas pela Classe `Closure` e possuem as mesmas características das funções anônimas, com a diferença que possuem acesso automático ao escopo externo, dessa forma se aproximando mais do conceito geral de _closures_.

```php
$variavel = 'Definida no escopo global.';

$funcaoAnonima =  fn () => $variavel;
echo $funcaoAnonima(); // saída: Definida no escopo global.
```

[↑ voltar ao topo](#programação-funcional)

### 1.3 Abordagem imperativa vs declarativa

Na abordagem imperativa além de **o que** deve ser feito, também dizemos **como** isso deve ser feito. Uma representação dessa abordagem pode ser vista quando quero chegar a um resultado (**o que**) que vai depender de uma coleção de dados. Se escrevo uma estrutura de repetição informando o que deve ser feito a cada iteração, estou definindo **como** chegar ao resultado.

Por exemplo, no código abaixo queremos obter o número de países da coleção de dados (**o que**). Para isso estou percorrendo a coleção, a cada iteração rotulando o item iterado (`$pais`) e incrementando um contador.

```php
$dados = require 'dados.php';

$numeroPaises = 0;
foreach ($dados as $pais) {
    $numeroPaises++;
}

echo "Número de países: $numeroPaises\n"; // 4
```

Podemos tentar mudar para algo mais próximo da abordagem declarativa. Como devemos percorrer a coleção de dados, podemos reescrever o código apenas trocando a estrutura de repetição pela função `array_walk()`, em que não preciso ser específico sobre como isso vai ser feito. Em seguida usamos uma função anônima que apenas incrementa o contador: 

```php
$dados = require 'dados.php';

$numeroPaises = 0;
array_walk($dados, function () use (&$numeroPaises) {
    $numeroPaises++;
});

echo "Número de países: $numeroPaises\n"; // 4
```

O resultado está mais próximo da abordagem declarativa do que imperativa, mas no fim das contas não mudou tanta coisa assim.

Um ponto muito importante da programação funcional é se atentar em usar as ferramentas (funções) mais adequadas para cada ocasião. No nosso caso sabemos que cada item do _array_ corresponde um país, então podemos simplesmente usar a função `count()`:

```php
$dados = require 'dados.php';

$numeroPaises = count($dados);
echo "Número de países: $numeroPaises\n"; // 4
```

[↑ voltar ao topo](#programação-funcional)

## 2 Manipulando _arrays_

### 2.1 Mapeando um _array_ com `array_map()`

Esta é a função mais adequada para a situação em que precisamos mapear um _array_ e executar operações em seus elementos. Ela possui dois argumentos obrigatórios: o primeiro é a função que será executada para cada um dos elementos do _array_ passado como segundo parâmetro. A função anônima deve receber o elemento do _array_ que será manipulado e retorná-lo. `array_map()` retorna um novo _array_ com as modificações aplicadas.

Vamos considerar o exemplo em que queremos passar o nome dos países para caixa alta. Vamos passar como primeiro argumento de `array_map()` uma função anônima que recebe o elemento `$pais` (outro _array_), altera o valor do elemento de índice `'pais'` desse _array_ e o retorna. O segundo argumento deve ser o _array_ com dados dos países.

```php
<?php

$dados = require 'dados.php';

$dados = array_map(function (array $pais) {
    $pais['pais'] = strtoupper($pais['pais']);
    return $pais;
}, $dados);
```

Também podemos nomear a função responsável por alterar o nome dos países:

```php
<?php

$dados = require 'dados.php';

function convertePaisParaMaiuscula(array $pais): array {
    $pais['pais'] = strtoupper($pais['pais']);
    return $pais;
}

$dados = array_map('convertePaisParaMaiuscula', $dados);
```

### 2.2 Filtrando um _array_ com `array_filter()`

A lógica dessa função é bem similar a do `array_map()`, mas diferente da primeira função que retorna todos os elementos do _array_ de entrada com a devida modificação, o `array_filter()` vai retornar apenas os elementos que passarem no filtro. Outra diferença para `array_map()` é a ordem dos parâmetros (pelo menos até podermos usar _named parameters_) e um terceiro parâmetro opcional que indica se no _array_ de saída deve ser adicionado apenas o valor ou chave do elemento filtrado.

Os elementos que passam no filtro dependem do retorno da função que `array_filter()` recebe: se for `true`, o elemento é adicionado no _array_ retornado; e se for `false` não é adicionado.

```php
<?php

$dados = require 'dados.php';

function paisComEspacoNoNome(array $pais): bool {
    return strpos($pais['pais'], ' ') !== false;
}

$dados = array_filter($dados, 'paisComEspacoNoNome');
```

Agora que temos duas formas de manipular _arrays_ poderíamos até mesmo realizar as chamadas de forma encadeada, mas na maior parte dos casos isso diminui a legibilidade código:

```php
<?php

$dados = array_filter(array_map('convertePaisParaMaiuscula', $dados), 'paisComEspacoNoNome');
```

### 2.3 Reduzindo _arrays_ a um único valor com `array_reduce()`

Usamos essa função quando queremos reduzir toda uma lista de valores a um único valor, como por exemplo, somar todos os valores de um _array_. Então vamos começar somando a quantidade total de medalhas de um dos países.

A função `array_reduce` recebe três parâmetros: o primeiro é o _array_ de entrada; o segundo é uma função acumuladora, que recebe o valor acumulado e o valor do item atual, seu retorno será o novo valor acumulado; e o terceiro valor (opcional) é o parâmetro de inicialização, o valor que será atribuído ao montante acumulado no inicío do processo.

Aplicando apenas para o Brasil, teríamos o seguinte código:

```php
<?php

$dados = require 'dados.php';

function medalhasPorPais(int $medalhasAcumuladas, int $medalhas) {
    return $medalhasAcumuladas + $medalhas;
}

$brasil = $dados[0];
$totalBrasil = array_reduce($brasil['medalhas'], 'medalhasPorPais', 0);
echo $totalBrasil; // 11
```

Agora podemos usar isso para calcular a quantidade total de medalhas entre todos os países:

```php
<?php

$dados = require 'dados.php';

function medalhasPorPais(int $medalhasAcumuladas, int $medalhas) {
    return $medalhasAcumuladas + $medalhas;
}

$totalMedalhas = array_reduce($dados, function (int $medalhasAcumuladas, array $pais) {
    return $medalhasAcumuladas + array_reduce($pais['medalhas'], 'medalhasPorPais', 0);
}, 0);
echo $totalMedalhas; // 47
```

e podemos abstrair a função anônima que soma o total de medalhas de todos os países para uma função nomeada:

```php
<?php

$dados = require 'dados.php';

function medalhasPorPais(int $medalhasAcumuladas, int $medalhas) {
    return $medalhasAcumuladas + $medalhas;
}

function totalMedalhas(int $medalhasAcumuladas, array $pais) {
    return $medalhasAcumuladas + array_reduce($pais['medalhas'], 'medalhasPorPais', 0);
}

$totalMedalhas = array_reduce($dados, 'totalMedalhas', 0);
echo $totalMedalhas; // 47
```

[↑ voltar ao topo](#programação-funcional)

### 2.3.1 `array_map()` com `array_reduce()`

Poderíamos realizar a mesma tarefa acima usando a função `array_reduce()` em conjunto com `array_map()`:

```php
<?php

$dados = require 'dados.php';

$totalMedalhas = array_reduce(
    array_map(
        function (array $pais) {
            return array_sum($pais['medalhas']);
        }, 
        $dados
    ), 
    function (int $medalhasAcumuladas, int $totalPorPais) {
        return $medalhasAcumuladas + $totalPorPais;
    }, 
    0
);

echo $totalMedalhas; // 47
```

e até mesmo usar _arrow functions_ para tentar "melhorar" a legibilidade:

```php
<?php

$dados = require 'dados.php';

$totalMedalhas = array_reduce(
    array_map(
        fn (array $pais) => array_sum($pais['medalhas']), 
        $dados
    ), 
    fn (int $medalhasAcumuladas, int $totalPorPais) => $medalhasAcumuladas + $totalPorPais, 
    0
);

echo $totalMedalhas; // 47
```

Outra possível solução seria usando a função `medalhasPorPais()` que já foi definida:

```php
<?php

$dados = require 'dados.php';

$totalMedalhas = array_reduce(
    array_map(
        function (array $pais) {
            return array_reduce($pais, 'medalhasPorPais', 0);
        }, 
        array_column($dados, 'medalhas')
    ), 
    'medalhasPorPais', 
    0
);

echo $totalMedalhas; // 47
```

e que fica até bem "legível" quando escrita com _arrow functions_:

```php
<?php

$dados = require 'dados.php';

$totalMedalhas = array_reduce(
    array_map(
        fn (array $pais) => array_reduce($pais, 'medalhasPorPais', 0), 
        array_column($dados, 'medalhas')
    ), 
    'medalhasPorPais', 
    0
);

echo $totalMedalhas; // 47
```

[↑ voltar ao topo](#programação-funcional)

### 2.4 Ordenando _arrays_ com `usort()`

A função mais básica para ordenação no PHP é a `sort()`, que simplesmente avalia qual valor é menor que o outro. Mas no caso de _arrays_ cujos elementos não são apenas valores escalares, esse tipo de comparação não faz muito sentido, como nos nossos dados em que cada país é um _array_. Para essas situações devemos usar a função `usort()`, que recebe o _array_ a ser ordenado e uma função de comparação.

Um detalhe que devemos observar no uso dessa função é que o _array_ a ser ordenado é passado por referência, ou seja, a ordenação vai ser feita no _array_ original. Isso viola o princípio da imutabilidade da progração funcional em que não devemos alterar o estado (?), ou seja, não devemos alterar o valor de uma variável (dar uma olhada em "funções puras"), devemos sempre criar uma nova ou sobrescrever seu valor.

A função de comparação recebe dois elementos do _array_ a ser ordenado e seu retorno depende da ordem entre esses dois elementos: se o primeiro elemento for menor que o segundo o retorno deve ser um número menor que zero; se ambos os elementos forem "iguais", o retorno deve ser o número zero; e se o primeiro elemento for maior que o segundo o retorno deve ser um número maior que zero.

O PHP já possui um operador que realiza essa comparação entre duas expressões, é o operador  [espaçonave](https://www.php.net/manual/pt_BR/migration70.new-features.php#migration70.new-features.spaceship-op) (`$a <=> $b`), que retorna `-1`, `0` e `1` quando `$a` é respectivamente menor, igual ou maior que `$b`. Usando esse operador podemos construir , de forma mais "legível", nossa regra de ordenação baseada na quantidade de medalhes de cada tipo e definir o critério de desempate:

```php
<?php

$dados = require 'dados.php';
var_dump($dados);

usort($dados, function (array $primeiro, array $segundo) {
    $medalhasPrimeiro = $primeiro['medalhas'];
    $medalhasSegundo  = $segundo['medalhas'];

    $ouro  = $medalhasSegundo['ouro'] <=> $medalhasPrimeiro['ouro'];
    $prata = $medalhasSegundo['prata'] <=> $medalhasPrimeiro['prata'];
    return $ouro !== 0 ? $ouro
        : ( $prata !== 0 ? $prata
        : $medalhasSegundo['bronze'] <=> $medalhasPrimeiro['bronze'] );
});

var_dump($dados);
```

[↑ voltar ao topo](#programação-funcional)

## 3 Aplicações parciais

Vamos partir do exemplo de uma função que executa uma operação matemática como a divisão entre dois número inteiros.

```php
<?php

function dividir(int $a, int $b) {
    return $a / $b;
}

echo dividir(4, 2); // 2
echo dividir(5, 2); // 2.5
echo dividir(10, 2); // 5
```

Note que em todas as chamadas foi passado o mesmo divisor, portanto, para esse caso faz sentido definir uma função que sempre divide por 2, "fixando" esse parâmetro. O que queremos é parcializar uma função completa de forma que ela já tenha parte dos parâmetros informados, ficando parcialmente "pronta" para ser executada.

Podemos fazer isso definindo uma função que não recebe nenhum parâmetro e retorna uma função anônima. Essa função retornada recebe o dividendo como único parâmetro e retorna a chamada da função `dividir()`, passando o divisor e o número 2 (parâmetro "fixo").

```php
function dividirPor2() {
    return function ($dividendo) {
        return dividir($dividendo, 2);
    };
}

echo dividirPor2()(4); // 2
echo dividirPor2()(5); // 2.5
echo dividirPor2()(10); // 5
```

Lembre-se que o valor da chamada de uma função é seu retorno, então `dividirPor2()` "vale" a chamada da função anônima, que retona a chamada de `dividir()`. Assim, para finalizar a operação de divisão basta passar o único parâmetro que a função anônima recebe.

Esse processo de transformar uma função que recebe vários parâmetros em uma cadeia de funções que podem ser chamadas de forma encadeada, cada uma recebendo um único parâmetro, é denominado de _**currying**_.

O resurso que utilizamos para parcializar uma função completa foi o de _high-order function_ (HOF). As HOF são funções que recebem uma função por parâmetro ou retornam uma função. Ou seja, são funções que trabalham com outras funções, recebendo ou retornando.

Podemos ainda tornar genérica essa nova função, fazendo com que a divisão não necessáriamente seja por 2:

```php
function dividirPor(int $divisor): callable {
    return function ($dividendo) use ($divisor) {
        return dividir($dividendo, $divisor);
    };
}

echo dividirPor(2)(4); // 2
echo dividirPor(2)(5); // 2.5
echo dividirPor(2)(10); // 5
```

Observando o código repetido em todas as chamadas para realizar a divisão por 2, podemos abstrair isso para uma variável:

```php
$dividirPor2 = dividirPor(2);

echo $dividirPor2(4); // 2
echo $dividirPor2(5); // 2.5
echo $dividirPor2(10); // 5
```

e quando utilizamos uma _**curried function**_ para fixar um dos parâmetros isso é chamado de aplicação parcial (_partial application_).

O conceito de _partial application_ foi utilizado para refatorar a ordenação dos países de acordo com a quantidade de medalhas (_commit_ [571d3b6](https://github.com/brnocesar/alura/commit/571d3b65d4a9ed8646f6f9ed90c108b9a753a6cb)) e a funções anônimas foram reescritas como _short closures_ (_arrow functions_) no _commit_ [16a7c36](https://github.com/brnocesar/alura/commit/16a7c36d0328711cec7fd652aa20e83170871678).

[↑ voltar ao topo](#programação-funcional)

## 4 Composição de funções

### 4.1 Abordagem "manual"

Até o momento, sempre que executamos alguma ação sobre os nossos dados (quando possível) retornamos um novo _array_ e sobreescrevemos o antigo. Isso é feito para evitar que nossas funções alterem estados externos a seus escopos.

Dessa forma, a cada vez que chamamos uma função estamos passando o "atual" _array_ de dados e recebendo um novo, como ilustrado no fragmento abaixo: 

```php
$dados = array_map('convertePaisParaMaiuscula', $dados);
$dados = array_filter($dados, $paisComEspacoNoNome);
```

Então o que estamos fazendo, através de uma váriavel, é: pegar o retorno de uma função e passar como parâmetro para outra. Poderíamos fazer isso diretamente passando a chamada de uma função como parâmetro de outra, e esse é o conceito de **composição de funções**.

```php
$dados = array_filter(array_map('convertePaisParaMaiuscula', $dados), $paisComEspacoNoNome);
```

Acima é apresentada a forma mais rudimentar de implementar a uma composição de funções, fazendo de forma manual. Para o nosso exemplo com apenas duas funções, não existe um problema grava de legibilidade. Mas considerando situações em que o número de funções pode crescer muito, faz sentido pensar em uma forma de mais configurável para compor as funções.

O primeiro ponto que podemos melhorar é fazer com que cada função que venha a fazer parte da composição recebe apenas um parâmtro. No nosso exemplo cada uma das funções recebe dois parâmetros, os dados e uma função, então vamos reescrever essas chamadas utilizando _arrow functions_:

```php
$paisesEmLetraMaiuscula = fn ($dados) => array_map('convertePaisParaMaiuscula', $dados);
$removePaisesSemEspacoNoNome = fn ($dados) => array_filter($dados, $paisComEspacoNoNome);

$dados = $paisesEmLetraMaiuscula($dados);
$dados = $removePaisesSemEspacoNoNome($dados);
```

Agora ambas as funções recebem apenas um parâmetro, o conjunto de dados, e podemos compor essas funções de forma mais legível que a anterior (_commit_ [ed81417](https://github.com/brnocesar/alura/commit/ed814176015dcbb8a86f55c4bbba17ba2f9d0e55)):

```php
$dados = $removePaisesSemEspacoNoNome($paisesEmLetraMaiuscula($dados));
```

[↑ voltar ao topo](#programação-funcional)

### 4.2 Abordagem "genérica"

Para implementar uma composição que seja mais genérica e nos permita alterar a configuração de funções de forma mais simples, vamos escrever uma função para "gerenciar" a composição. Esta função deve receber várias funções e fazer com que elas sejam executadas de forma encadeada, sempre passando o retorno de uma como parâmtro para a próxima, como um _pipeline_ de funções.

Como não queremos limitar o número de funções passadas, vamos definir um número variável de argumentos usando o operador `...`, dessa forma os argumentos serão passados na forma de um _array_ de funções.

Como estamos trabalhando com um _array_ de funções em que vamos executar cada uma delas para no final retonar os dados que foram processados, ou seja, vamos passar por todos os elementos de um _array_ para retornar um valor reduzido de todos esses elementos. Então faz sentido utilizarmos o `array_reduce()`, em que a função acumuladora vai passar o valor acumulado para a função atual, até que todas as funções sejam executadas.

```php
function pipe(callable ...$funcoes): callable 
{
    return fn ($dados) => array_reduce(
        $funcoes, 
        fn ($valorAcumulado, callable $funcaoAtual) => $funcaoAtual($valorAcumulado), 
        $dados
    );
}

$composicaoFuncoes = pipe($removePaisesSemEspacoNoNome, $paisesEmLetraMaiuscula);
$dados = $composicaoFuncoes($dados);
```

O parâmetro de inicialização do `array_reduce()` são os dados originais, que passamos para a composição após atribuí-la para uma variável (_commit_ [d943bca](https://github.com/brnocesar/alura/commit/d943bca48b6922312880aada1a68f8dc13c15c14)).

O nome `pipe()` (as vezes encontrado como `pipeline()` também) dado para a função é uma conveção e indica que as funções devem ser executadas da esquerda para a direita. Outro nome comum para esse tipo de função é `compose()`, nesse caso a ordem é contrária, da direita para esquerda.

[↑ voltar ao topo](#programação-funcional)

### 4.3 Utilizando pacotes externos

Estabelecer um _pipeline_ de funções é uma ação muito comum, então é natural que as pessoas prefiram utilizar pacotes prontos para realizar essas tarefas. Existem vários pacotes bem conceituados na comunidade, então vamos utilizar apenas um como exemplo.

Para instalar o pacote execute o comando:

```terminal
composer require igorw/compose
```

Crie um arquivo `.gitignore` para manter a pasta `vendor/` fora do versionamento. No arquivo em que vamos usar a função externa devemos importar o `autoload.php` para lidar com o `namespace` do pacote e então basta fazer a chamada da função do pacote (_commit_ [24d2555](https://github.com/brnocesar/alura/commit/24d255593b12c04460249c13896860a47f160a5d)):

```php
<?php

require_once 'vendor/autoload.php';

...

$composicaoFuncoes = \igorw\pipeline($removePaisesSemEspacoNoNome, $paisesEmLetraMaiuscula);
```

[↑ voltar ao topo](#programação-funcional)

## 5 Mônadas

Quando vamos trabalhar com dados que vêm de alguma fonte externa, uma API ou um arquivo mesmo, devemos nos precaver com possíveis erros ou "formatos" indesejados desses arquivos. Por exemplo, se a fonte dos nossos dados retornar um valor numérico, uma _string_ ou `null` ao invés de um _array_, obteremos vários erros na saída padrão ao executar nosso código.

Como o PHP não é uma linguagem puramente funcional, vamos utilizar uma abordagem multiparadigma para contornar esse problema. Vamos criar um novo tipo de dado que nos permita lidar com a possibilidade de que **talvez** tenha um _array_ e **talvez** não tenha. Esse novo tipo, chamado de "tipo monádico", será uma classe que vai atuar como um _wrapper_ para nossos dados e que entregará sempre um dado válido e consistente para ser manipulado pelas nossas funções.

Na pasta `src` criamos a classe `Maybe` (ou `Optional`, são nomes bastante comuns para esses _wrappers_) com o construtor padrão apenas recebendo e atribuíndo esse valor e também definimos um "construtor" estático. Também devemos implementar um método que retorne o valor encapsulado (passado no construtor) ou, em caso de o valor ser inválido para o que queremos, retornar algum valor padrão:

```php
<?php

namespace Programacao\Funcional;

class Maybe
{
    private $valor;

    public function __construct($valor)
    {
        $this->valor = $valor;
    }

    public static function of($valor)
    {
        return new self($valor);
    }

    public function isNothing(): bool
    {
        return is_null($this->value) or !is_array($this->value);
    }

    public function getOrElse($default)
    {
        return $this->isNothing() ? $default : $this->value;
    }
}
```

Já podemos criar objetos do tipo `Maybe` e acessar seu valor válido:

```php
Maybe::of(10)->getOrElse([]); // []
Maybe::of(null)->getOrElse([1, 2, 3]); // [1, 2, 3]
Maybe::of([1, 2, 3])->getOrElse([]); // [1, 2, 3]
```

### 5.1 _Functors_

Algo interessante que podemos implementar em nossa classe é o conceito de _functors_. Em linguagem de programação um _functor_ é qualquer coisa em que pode ser realizado um _map_. Assim, em Orientação a Objetos é qualquer objeto que possui o método `map()` e em programação funcional é qualquer coisa em que o `map` pode ser aplicado.

Então vamos modificar nossa classe `Maybe` para que ela se adeque a condição de ser um _functor_. Começamos definindo um método `map()` que recebe um `callable` e dentro desse método avalio se seu valor é válido ou não para retornar uma instância de `Maybe` com o valor processado pela função recebida como parâmetro.

```php
public function map(callable $fn)
{
    if ($this->isNothing()) {
        return Maybe::of($this->value);
    }
    $value = $fn($this->value);

    return Maybe::of($value);
}
```

### 5.2 Adequando as funções ao tipo `Maybe`

Para adequar as funções que escrevemos a esse novo tipo de dado precisamos utilizar o método `getOrElse()` para acessar os dados, definir o tipo `Maybe` para os argumentos de funções que recebem os dados e retornar sempre uma instância de `Maybe` recebendo o _array_ manipulado (_commit_ [7acead2](https://github.com/brnocesar/alura/commit/7acead28de8bd398f8afef725f16fcf09ad296ef)).

Note que para a função de ordenação não acusar erro devemos passar uma cópia do _array_ que acessamos de `Maybe`, pois essa função recebe o _array_ de argumento por referência e não por valor.

[↑ voltar ao topo](#programação-funcional)