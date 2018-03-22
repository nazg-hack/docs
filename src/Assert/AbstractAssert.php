<?hh // strict

namespace App\Assert;

use App\Finder\DocumentFinder;

abstract class AbstractAssert {
  abstract const type T;

  abstract public static function assert<T>(T $t): this::T;
}
