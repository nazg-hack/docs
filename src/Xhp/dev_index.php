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
final class :div:index extends :x:element {
  attribute :div;

  use XHPHelpers;

  protected function render(): XHPRoot {
    $id = $this->getID();
    return (
            <div id={$id}>
              <h1>Nazg</h1>
              Begin developing HHVM/Hack Http Application
            </div>
    );
  }
}

class UnsafeXHP implements XHPUnsafeRenderable {
  public function __construct(
    private string $html,
  ) {}

  public function toHTMLString(): string {
    return $this->html;
  }
}
