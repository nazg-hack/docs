# DI Container / hh-container

## Introduction

この章はNazg Frameworkのデフォルトで含まれる [hh-container](https://github.com/ytake/hh-container) を解説します。  

Nazg Frameworkは *Dependency Injection* と言われる概念に基づいて、  
さまざまなクラスが薄く結合しています。  
Nazg Frameworkには各機能を薄く結合するために、  
*Dependency Injection Container* ライブラリ(以下 DIコンテナ)を使って構築されています。  
Nazg Frameworkを使ったアプリケーション開発では、このDIコンテナライブラリを利用することができます。  

以下にシンプルな例を紹介します。  

```hack
namespace App\Module;

use Ytake\HHContainer\Scope;
use Ytake\HHContainer\ServiceModule;
use Ytake\HHContainer\FactoryContainer;
use Nazg\Log\LogServiceModule;
use Psr\Log\LoggerInterface;
use Monolog\Logger;
use Monolog\Monolog;
use Monolog\Handler\StreamHandler;

final class LoggerServiceModule extends LogServiceModule {

  <<__Override>>
  public function provide(FactoryContainer $container): void {
    $container->set(
      LoggerInterface::class,
      $container ==> $this->filesystemLogger(
        $container->get(\Nazg\Foundation\Service::CONFIG),
      ),
      \Ytake\HHContainer\Scope::SINGLETON,
    );
  }

  protected function filesystemLogger(mixed $config): LoggerInterface {
    $monolog = new Logger("nazg.doc");
    if (is_array($config)) {
      $monolog->pushHandler(
        new StreamHandler($config['log_file'], Logger::WARNING),
      );
    }
    return $monolog;
  }
}

```

## インスタンスの取得方法

[hh-container](https://github.com/ytake/hh-container) は [PSR-11: Container interface](https://www.php-fig.org/psr/psr-11/)に準拠していますので、  
DIコンテナからインスタンスを取得する場合は **getメソッド**  
DIコンテナに登録されているかどうかを調べる場合は、 **hasメソッド** を利用します。

インスタンス生成についての指定方法は次の `Basic Bindings` をご覧ください。

## Basic Bindings

DIコンテナは **Ytake\HHContainer\ServiceModule** クラスを継承したクラスから利用することができます。  
このクラスには、指定したクラスをコンテナから取り出す方法を定義するクラスです。  
DIコンテナからクラスを取り出す際に、インスタンス生成が行われ、  
クラス実行時にフレームワークが自動でインスタンスを利用します。  

インスタンス取得方法を記述する場合は、  
**Ytake\HHContainer\ServiceModuleクラスのpriovideメソッド** をオーバーライドして利用します。  
*このメソッドは必ず記述しなければなりません*  

**\Ytake\HHContainer\FactoryContainerクラスのsetメソッド** の第一引数は、  
コンテナに登録する名前を指定します。  
この名前は文字列であればどんな値でも構いません。  
インターフェース名やクラス名だとわかりやすいでしょう。  

第二引数には、無名関数で引数は 、 **\Ytake\HHContainer\FactoryContainerクラス** 自身となります。  
無名関数、またはラムダで記述し、  
戻り値は第一引数で指定した名前でコンテナにアクセスした場合に返却する値を指定してください。  

もしくはインスタンスではなく、文字列や配列、Collectionなどでも構いません。  
これらはServiceModuleクラス内ではサービスロケータとして利用する、と思った方が理解しやすいでしょう。  

### Prototype

DIコンテナで、インスタンス生成時に都度新しく生成して欲しい場合に利用します。  
デフォルトではこの方法でインスタンス生成が行われます。  

```hack
namespace App\Module;

use Ytake\HHContainer\Scope;
use Ytake\HHContainer\ServiceModule;
use Ytake\HHContainer\FactoryContainer;

final class AppServiceModule extends ServiceModule {

  <<__Override>>
  public function provide(FactoryContainer $container): void {
    $container->set(
      \stdClass::class,
      $container ==> new \stdClass,
      \Ytake\HHContainer\Scope::PROTOTYPE,
    );
  }
}

```

上記の登録方法の場合は、 \stdClassがコールされるたびに新しいインスタンスを生成します。  

### Singleton

シングルトンはインスタンスを一度だけ生成し、  
生成したインスタンスをDIコンテナから取得すると、生成済みのインスタンスを返却します。  
これは様々なクラスで同じインスタンスを共有したい場合に利用します。  
多くの場合はデータベースのコネクションを維持するために利用したり、  
配列を利用したキャッシュなどが考えられます。  

```hack
namespace App\Module;

use Ytake\HHContainer\Scope;
use Ytake\HHContainer\ServiceModule;
use Ytake\HHContainer\FactoryContainer;

final class AppServiceModule extends ServiceModule {

  <<__Override>>
  public function provide(FactoryContainer $container): void {
    $container->set(
      \stdClass::class,
      $container ==> new \stdClass,
      \Ytake\HHContainer\Scope::SINGLETON,
    );
  }
}

```

上記の登録方法の場合は、 \stdClassがコールされると生成済みのインスタンスがあればそれを共有し、  
インスタンス生成が行われていなければ一度だけ生成します。

#### Prototype or Singleton  

Nazgフレームワークでは、すでにお気付きの方もいるかもしれませんが、  
PrototypeかSingletonは、 [hh-container](https://github.com/ytake/hh-container) ではEnumsを使って選択します。  

Enumsは次の通りです。  

| enums       | type            |
|-------------|-----------------|
| \Ytake\HHContainer\PROTOTYPE | インスタンスを都度生成する  |
| \Ytake\HHContainer\SINGLETON | インスタンスを一度だけ生成し、以降は共有して利用する  |

インスタンス生成方法については、 **\Ytake\HHContainer\FactoryContainerクラスのsetメソッド** の第三引数で指定しますが、  
指定がない場合は **\Ytake\HHContainer\PROTOTYPE** がデフォルトとして利用されます。  

### Use Parameters

DIコンテナでインスタンス生成方法を指定する場合に、  
生成するインスタンスで、引数で利用するクラスの指定や、  
intやstringなどプリミティブな値を指定したい場合があれば **parametersメソッド** を利用して指定することができます。  

```hack
namespace App\Module;

use Ytake\HHContainer\Scope;
use Ytake\HHContainer\ServiceModule;
use Ytake\HHContainer\FactoryContainer;

final class AppServiceModule extends ServiceModule {

  <<__Override>>
  public function provide(FactoryContainer $container): void {
    $container->parameters(
      \Sample::class,
      'arg1',
      $container ==> 'parameter value'
    );
  }
}

```

**\Ytake\HHContainer\FactoryContainerクラスのparametersメソッド** の第一引数は、  
コンテナに登録する名前を指定します。  
**\Ytake\HHContainer\FactoryContainerクラスのsetメソッド** の指定方法と同じです。  

第二引数には、第一引数で指定した文字列(サービス)の引数名を指定します。  
第三引数には、無名関数で引数は 、 **\Ytake\HHContainer\FactoryContainerクラス** 自身となります。  
無名関数、またはラムダで記述します。

すでに登録済みのサービスを取得するなどで依存関係を解決することができます。  

以下にその例を示します。

#### Dependency Injection with parameters

```hack

final class MessageClass {
  public function __construct(protected string $message) {
  }
  public function message(): string {
    return $this->message;
  }
}

final class MessageClient {
  public function __construct(protected MessageClass $message) {

  }
  public function message(): MessageClass {
    return $this->message;
  }
}

```

MessageClientクラスはインスタンス生成に MessageClassを必要としています。  
MessageClassはインスタンス生成に文字列を必要としています。  
これを指定する場合は次の通りです。 

```hack
namespace App\Module;

use Ytake\HHContainer\Scope;
use Ytake\HHContainer\ServiceModule;
use Ytake\HHContainer\FactoryContainer;

final class AppServiceModule extends ServiceModule {

  <<__Override>>
  public function provide(FactoryContainer $container): void {
    $container->set(
      'message.class', 
      $container ==> new MessageClass('testing')
    );
    $container->parameters(
      MessageClient::class, 
      'message', 
      $container ==> $container->get('message.class')
    );
  }
}

```

上記の記述で、`$container->get(MessageClient::class)` をコールした場合に  
任意の引数でインスタンスが生成されます。  

場合によっては下記の様に記述しても構いません。  

```hack

<<__Override>>
public function provide(FactoryContainer $container): void {
  $container->set(
    MessageClient::class, 
    $container ==> new MessageClient(new MessageClass('testing'))
  );
}
```

引数に必要なインスタンスなどをDIコンテナから取得したい場合、  
複雑な依存解決を記述する場合などに利用できます。  

