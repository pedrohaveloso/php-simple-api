Artigo postado no TabNews. Acesso em: [TabNews - Criando uma API simples com PHP puro](https://www.tabnews.com.br/pedrohaveloso/criando-uma-api-simples-com-php-puro).

# Criando uma API simples com PHP puro

Habitualmente, aprendemos a criar sites em PHP com resquícios de um velho e antiquado modo de programar na linguagem — embora (infelizmente) ainda usado hoje em dia. 

**Quer ir direto ao ponto? [Criando a API](#criando-a-api).**

Pois o desenvolvedor PHP que jamais fez uma página de formulário com a conexão do banco, injeção de dados nos inputs, validação e recebimento do POST, além de sua ação, juntos no mesmo arquivo PHP, que jogue a primeira pedra.

Coisas como essa já foram tão comuns... Por bem, podemos considerar isso abominável nos dias atuais. Para fazer um grande projeto na linguagem, passamos a utilizar frameworks como o [Laravel](https://laravel.com/), o [Symfony](https://symfony.com/) e o [Zend](https://www.zend.com/). 

Esses frameworks usam, no geral, uma estrutura MVC. Há a completa separação da sua lógica de negócio, da sua tela etc. Claramente, tendemos a usá-los na criação de aplicações web completas, mesmo que também possamos usar para criar APIs.

Entretanto, esse artigo não pretende te ensinar algo com um desses framework, muito menos em criar uma aplicação em PHP que possua telas. O objetivo aqui é fazer uma API (recebendo chamadas e retornando alguma resposta), sem nenhuma dependência, apenas o mais puro PHP. 

## Criando a API

Esse tutorial tem alguns requisitos, sendo eles:

- Tenha o PHP 8.0 ou superior instalado em sua máquina.
- Tenha o Composer instalado em sua máquina.
- Tenha algum editor de código.

Se você não sabe o que é o [Composer](https://getcomposer.org/), deveria. Vamos utilizar ele para algo interessante: o autoload dos arquivos PHP. Se ainda não entende o que é isso, tente ler um pouco da documentação: [Autoloading Classes](https://www.php.net/manual/pt_BR/language.oop5.autoload.php). 

Se não se importar muito com autoload e quiser fazer seus includes/requires por conta própria, fique a vontade em pular a instalação do Composer.

### Criando a pasta e instalando o Composer

Dentro de alguma área do seu computador, crie uma pasta com o nome que você quiser, criaremos cá uma nomeada de _./example_ (no Ubuntu):

`mkdir example && cd ./example`

Entre nessa pasta e abrá no seu editor de código preferido, utilizo como padrão o VSCode, então:

`code .`

Aberto em seu editor, você deve criar um arquivo chamado _composer.json_ e uma pasta chamada _src_, seu projeto deve ficar com essa estrutura:

```
/example
  |--/src/
  |--/composer.json
```

Dentro do _composer.json_, cole a seguinte estrutura para o autoload:

```json
# composer.json
{
    "autoload": {
        "psr-4": {"SimpleApi\\": "src/"}
    }
}
```

Após isso, vamos instalar as dependências do Composer, no terminal:

`composer install`

### Entendendo como rodar e iniciando a API

Pronto, concluímos a primeira etapa, agora vamos pensar mais na criação da API. Caso você esteja iniciando no PHP (que é o público que esse artigo pretende alcançar) já deve ter utilizado o Xampp, Wamp ou Laragon. Não vamos rodar nosso projeto em algum deles, você percebeu que os requisitos requeriam apenas o PHP puro em sua máquina, com ele já poderemos rodar.

O próprio PHP possuí um servidor embutido, vou mostrar como executar esse projeto com ele, mas antes, vamos criar um _index.php_ dentro de uma pasta _/public_ para iniciar a API nele:

```
/example
  |--/public/index.php
  |--/src/
  |--/composer.json
```

Agora, para iniciar com o servidor embutido do PHP, volte para o terminal no projeto e faça:

`php -S localhost:4321 ./public/index.php`

Ao abrir seu navegador e ir para o _localhost:4321_, você verá que a página está sendo aberta — se quiser, pode dar um `echo 'Hello World';` dentro do seu _index.php_ para confirmar que está rodando corretamente nele.

Com o index criado e o projeto rodando, vamos começar a criar a base da nossa API... Por onde começamos? Pelas rotas.

Você já deve entender bem de rotas na web, o seu /users, /profiles etc. Além dos métodos HTTP como 2xx, 4xx... Precisamos de algo que faça o controle das rotas em nossa aplicação, então vamos lá:

Crie, dentro da sua pasta _/src_, um arquivo chamado _Router.php_, com o seguinte conteúdo:

```php
# /src/Router.php

<?php

namespace SimpleApi;

class Router {
  protected $routes = [];
}

```

Qual será nosso objetivo com esse _Router_? A criação das rotas (com seu método, sua rota e a função na rota) e a execução dessas rotas. Vamos começar com uma função para criar uma rota:

```php
# /src/Router.php

<?php

namespace SimpleApi;

class Router 
{
  protected $routes = [];

  public function create(
    string $method, // Método HTTP.
    string $path, // URL/rota.
    callable $callback // Função executada nessa rota.
  ) 
  {
    $this->routes[$method][$path] = $callback;
  }
}

```

Pronto, temos nosso método para criar essas rotas, agora precisamos de outro que execute essas rotas:

```php
# /src/Router.php

<?php

namespace SimpleApi;

class Router 
{
  protected $routes = [];

  ...

  public function init() 
  {
    // Colocamos o content-type da resposta para JSON.
    header('Content-Type: application/json; charset=utf-8');

    $httpMethod = $_SERVER["REQUEST_METHOD"];

    // O método atual existe em nossas rotas?
    if (isset($this->routes[$httpMethod])) {

      // Percore as rotas com o método atual:
      foreach (
        $this->routes[$httpMethod] as $path => $callback
      ) {
        
        // Se a rota atual existir, retorna a função...
        if ($path === $_SERVER["REQUEST_URI"]) {
          return $callback();
        }
      }
    }

    // Caso não exista a rota/método atual: 
    http_response_code(404);
    return;
  }
}

```

Pronto, nosso roteador agora possui um método para iniciar suas rotas. Devemos testar se isso está funcionando, para isso, crie um outro arquivo chamado _application.php_:

```
/example
  |--/src/
       |--/Router.php
       |--/Application.php
```

Ele será o início do nosso programa, onde criaremos nossas rotas etc. (talvez você possa fazer isso com outra abordagem, se preferir). Dentro dele:

```php
# /src/Application.php

<?php

namespace SimpleApi;

class Application
{
  public function start() {
  }
}
```

Devemos chamar esse método `start` dentro do nosso _index.php_, além de fazer o include do autoload:

```php
# /public/index.php

<?php

use SimpleApi\Application;

include __DIR__ . '/../vendor/autoload.php';

$app = new Application();

$app->start();

```

Voltando para a classe da nossa aplicação, podemos agora criar algumas rotas de teste e iniciar nosso Router.

```php
# /src/Application.php

<?php

namespace SimpleApi;

class Application
{
  public function start() {
    $router = new Router();

    $router->create("GET", "/", function () {
      http_response_code(200);
      return;
    });    

    $router->create("GET", "/hello", function () {
      http_response_code(200);
      echo json_encode(["hello" => "world"]);
      return;
    });

    $router->init();
  }
}

```

Ao salvar os arquivos, você pode dar uma testada se essas duas rotas estão funcionando. Pode usar o [curl](https://curl.se/), o [Postman](https://www.postman.com/) ou outro meio que desejar, usarei o CLI do [httpie](https://httpie.io/):

`http GET "http://localhost:4321/hello"`

O retorno deverá ser algo mais ou menos assim:

```
HTTP/1.1 200 OK
Connection: close
Content-Type: application/json; charset=utf-8
Date: Thu, 00 Jan 2000 00:00:00 GMT
Host: localhost:4321
X-Powered-By: PHP/8.2.10-2ubuntu2.1

{
    "hello": "world"
}
```

Isso é um sinal de sucesso, você tem uma "API" primitiva funcionando com um roteador.

Podemos fazer mais algumas coisas nesse código, mas o básico já está finalizado, você pode ir agregando mais coisas em cima disso, criar controllers, ou quem sabe aceitar uma rota com parâmetros na query? São várias coisas que podem ser adicionadas nesse código. Faça! Ou quem sabe criamos um artigo para extender esse futuramente...

---

### Se quiser clonar o projeto criado aqui, acesse: [pedrohaveloso/php-simple-api](https://github.com/pedrohaveloso/php-simple-api)
