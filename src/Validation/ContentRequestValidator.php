<?hh // strict

namespace App\Validation;

use Facebook\TypeAssert;
use Nazg\Foundation\Validation\Validator;
use Psr\Http\Message\ServerRequestInterface;

final class ContentRequestValidator extends Validator {
  
  const type ContentRequestShape = shape(
    'content' => string,
  );
  
  protected bool $shouldThrowException = true;
  
  protected Vector<string> $errors = Vector{};

  <<__Override>>
  protected function assertStructure(): void {
    try {
      TypeAssert\matches_type_structure(
        type_structure(self::class, 'ContentRequestShape'),
        $this->request?->getAttributes(),
      );
    } catch (TypeAssert\IncorrectTypeException $e) {
      $this->errors->add("type error");
    }
  }

  protected function assertValidateResult(): Vector<string> {
    return $this->errors;
  }
}
