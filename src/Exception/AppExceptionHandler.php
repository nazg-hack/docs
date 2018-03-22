<?hh 

/**
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 *
 * Copyright (c) 2017-2018 Yuuki Takezawa
 */
namespace App\Exception;

use Nazg\Http\StatusCode;
use Nazg\Foundation\Validation\ValidationException;
use Nazg\Types\ExceptionImmMap;
use Nazg\Foundation\Exception\ExceptionHandler;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\JsonResponse;

class AppExceptionHandler extends ExceptionHandler {
  <<__Override>>
  protected function render(
    ExceptionImmMap $em,
    \Exception $e
  ): ResponseInterface {
    $message = $em->toArray();
    if($e instanceof ValidationException) {
      $message = $e->errors();
    }
    return new JsonResponse(
      $message,
      StatusCode::StatusInternalServerError,
    );
  }
}
