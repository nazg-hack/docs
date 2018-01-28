<?hh 

namespace App\Exception;

use Nazg\Http\StatusCode;
use Nazg\Foundation\Validation\ValidationException;
use Nazg\Foundation\Exception\ExceptionMap;
use Nazg\Foundation\Exception\ExceptionHandler;
use Zend\Diactoros\Response\JsonResponse;

final class ApplicationExceptionHandler extends ExceptionHandler {
  <<__Override>>
  public function render(ExceptionMap $em, \Exception $e): void {
    $message = $em->toArray();
    if($e instanceof ValidationException) {
      $message = $e->errors();
    }
    $this->emitter->emit(
      new JsonResponse(
        $message,
        StatusCode::StatusInternalServerError,
      ),
    );
  }
}
