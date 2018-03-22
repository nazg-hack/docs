# DI Container / hh-container

## Introduction

この章はNazg Frameworkのデフォルトで含まれる [hh-container](https://github.com/ytake/hh-container) を解説します。  

Nazg Frameworkは *Dependency Injection* と言われる概念に基づいて、  
さまざまなクラスが薄く結合しています。  
Nazg Frameworkには各機能を薄く結合するために、  
*Dependency Injection Container* ライブラリ(以下 DIコンテナ)を使って構築されています。  
Nazg Frameworkを使ったアプリケーション開発では、このDIコンテナライブラリを利用することができます。  

以下にシンプルな例を紹介します。  

```php
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


## Basic Bindings

DIコンテナは **Ytake\HHContainer\ServiceModule** クラスを継承したクラスから利用することができます。  
このクラスには、指定したクラスをコンテナから取り出す方法を定義するクラスです。  
DIコンテナからクラスを取り出す際に、インスタンス生成が行われ、  
クラス実行時にフレームワークが自動でインスタンスを利用します。  

### Prototype

DIコンテナで、インスタンス生成時に都度新しく生成して欲しい場合に利用します。  
デフォルトではこの方法でインスタンス生成が行われます。  

```php
namespace App\Module;

use Ytake\HHContainer\Scope;
use Ytake\HHContainer\ServiceModule;
use Ytake\HHContainer\FactoryContainer;

final class AppServiceModule extends LogServiceModule {

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
