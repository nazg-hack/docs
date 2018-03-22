<?hh // strict

namespace App\Assert;

use App\Finder\DocumentFinder;

class AssertDocumentFinder extends AbstractAssert {
  const type T = DocumentFinder;

  public static function assert<T>(T $t): this::T  {
    invariant($t instanceof DocumentFinder, "not DocumentFinder class");
    return $t;
  }
}
