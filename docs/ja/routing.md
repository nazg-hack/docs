# ルーティング

ルーティングの指定は、[スケルトン](https://github.com/ytake/nazg-skeleton)を使ったプロジェクトでは簡単に記載できます。  

NazgフレームワークのMiddlewareクラス、Actionクラスは、  
[PSR-15: HTTP Server Request Handlers](https://www.php-fig.org/psr/psr-15/) を実装したクラスであることが必須ですが、  
それ以外の決まりはありません。  

[PSR-7: HTTP message interfaces](https://www.php-fig.org/psr/psr-7/)が実装されたクラス、ライブラリであれば  
開発者が任意のライブラリを組み込むことができます。  
デフォルトでは [zendframework/zend-diactoros](https://github.com/zendframework/zend-diactoros) を利用しています。  

*このルーティングについては後から仕様変更が行われる予定です*

## ルーティング記述方法

デフォルトでは　`config/routes.global.php` に記述するようになっています。  
*このファイルを含むディレクトリ構造は任意で変更できます*

```hack
return [
  \Nazg\Foundation\Service::ROUTES => ImmMap {
    \Nazg\Http\HttpMethod::GET => ImmMap {
      '/' => ImmVector {App\Action\IndexAction::class},
    },
  },
];

```

あらかじめ用意されているルーティングは上記の通りです。  
記述方法はシンプルですが、  
**\Nazg\Foundation\Service::ROUTES** を配列のキーとし、  
ImmMapを使ってルーティングを記述しなければなりません。  

それ以外の記述のルールは次の通りです。  

```
HttpMethod => ImmMap {
  endpoint => ImmVector {
    指定したendpointに反応するミドルウェア、またはアクションクラスを実行したい順番で記述する  
  } 
}

```

### HTTP Method
HttpMethodはフレームワークでEnumsとして用意されています。  

| HTTP Method | Enums | 
|------------------|--------------------|
| HEAD   | \Nazg\Http\HttpMethod::HEAD |
| GET   | \Nazg\Http\HttpMethod::GET |
| POST   | \Nazg\Http\HttpMethod::POST |
| PATCH   | \Nazg\Http\HttpMethod::PATCH |
| PUT   | \Nazg\Http\HttpMethod::PUT |
| DELETE   | \Nazg\Http\HttpMethod::DELETE |

デフォルトで記述されているルーティングの意味は下記の通りです。  

```hack
return [
  // \Nazg\Foundation\Service::ROUTESはフレームワークで用意されているEnumsです
  \Nazg\Foundation\Service::ROUTES => ImmMap {
    // GETリクエストで動作するクラスフループを指定
    \Nazg\Http\HttpMethod::GET => ImmMap {
      // `/` にアクセスすると、ImmVectorに記述したクラスが実行される
      // 以下の場合は `/` にアクセスすると \App\Action\IndexAction::class が実行されます
      '/' => ImmVector {App\Action\IndexAction::class},
    },
  },
];

```

## ルートに対応するActionクラスを用意

Actionクラスとは、MVCを採用しているフレームワークで云うControllerに該当し、  
多くのフレームワークのControllerクラスと異なる点は、  
クラス自体が一つのルートに対応することしかできません。  

Controllerクラスに様々なルートを実装し、巨大で複雑なクラスになるケースがありますが、  
Actionクラスでは単一のルートの実装のみとなりますので、  
巨大で複雑なクラスに発展することはあまり無いのが特徴です。  

Actionクラスは　**Psr\Http\Server\MiddlewareInterface** を実装したクラスであればどんなクラスでも構いません。  

### Hello World Action Class

ここからはHello Worldを返却するActionクラスを実装します。  

レスポンスは [zendframework/zend-diactoros](https://github.com/zendframework/zend-diactoros) の  
**Zend\Diactoros\Response\HtmlResponseクラス** を利用して実装してみましょう。

```hack
namespace App\Action;

use App\Responder\IndexResponder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\HtmlResponse;

final class HelloAction implements MiddlewareInterface {

  public function process(
    ServerRequestInterface $request,
    RequestHandlerInterface $handler,
  ): ResponseInterface {
    return new HtmlResponse('Hello World');
  }
}
```

processメソッドで **Zend\Diactoros\Response\HtmlResponse** インスタンスを返却します。  
このActionクラスはHTMLでHello Worldを返却する、という動作になります。  

### Register Container

Actionクラスを作成しただけではフレームワークで利用することができません。  
デフォルトで用意されている **App\Module\ActionServiceModuleクラス** に、  
このActionクラスのインスタンス生成方法を記述します。  
*ServiceModuleクラスは役割ごとに作成したり、アプリケーションの整理で任意で作成したり自由に利用できます*  

```hack
namespace App\Module;

use App\Action\{IndexAction, HelloAction};
use App\Responder\IndexResponder;
use Ytake\HHContainer\Scope;
use Ytake\HHContainer\ServiceModule;
use Ytake\HHContainer\FactoryContainer;

final class ActionServiceModule extends ServiceModule {
  <<__Override>>
  public function provide(FactoryContainer $container): void {
    $container->set(
      IndexAction::class,
      $container ==> new IndexAction(new IndexResponder()),
      Scope::PROTOTYPE,
    );
    $container->set(
      HelloAction::class,
      $container ==> new HelloAction(),
      Scope::PROTOTYPE,
    );
  }
}

```

次にルーティングを追加します。  
デフォルトで用意されている `config/routes.global.php` に追記します。

```hack
return [
  \Nazg\Foundation\Service::ROUTES => ImmMap {
    \Nazg\Http\HttpMethod::GET => ImmMap {
      '/' => ImmVector {App\Action\IndexAction::class},
      '/hello' => ImmVector {App\Action\HelloAction::class},
    },
  },
];

```

これで `/hello` にアクセスすると、Hello Worldが表示されます。  

