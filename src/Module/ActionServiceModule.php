<?hh // strict

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
namespace App\Module;

use App\Action;
use App\Assert\{AssertArray, AssertDocumentFinder};
use App\Finder\DocumentFinder;
use App\Responder\XHPResponder;
use Ytake\HHContainer\Scope;
use Ytake\HHContainer\ServiceModule;
use Ytake\HHContainer\FactoryContainer;
use Nazg\Foundation\Service;

final class ActionServiceModule extends ServiceModule {
  <<__Override>>
  public function provide(FactoryContainer $container): void {
    $container->set(
      Action\IndexAction::class,
      $container ==> new Action\IndexAction(
        new XHPResponder(),
        AssertDocumentFinder::assert($container->get(DocumentFinder::class)),
      ),
      Scope::PROTOTYPE,
    );
    $container->set(
      Action\Document\ReadAction::class,
      $container ==> new Action\Document\ReadAction(
        new XHPResponder(),
        AssertDocumentFinder::assert($container->get(DocumentFinder::class)),
      ),
      Scope::PROTOTYPE,
    );
    $container->set(
      \App\Finder\DocumentFinder::class,
      $container ==> AssertArray::assert($container->get(Service::CONFIG))
      |>new \App\Finder\DocumentFinder(strval($$['doc_path'])),
      Scope::SINGLETON,
    );
  }
}
