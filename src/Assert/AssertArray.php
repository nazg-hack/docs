<?hh // strict

namespace App\Assert;

final class AssertArray extends AbstractAssert {
  const type T = array<mixed, mixed>;

  public static function assert<T>(T $t): this::T  {
    invariant(is_array($t), "not array");
    return $t;
  }
}
