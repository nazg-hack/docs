<?hh // strict

namespace App\Finder;

use Facebook\Markdown;

class DocumentFinder {
  
  protected string $lang = 'ja';
  
  public function __construct(private string $path) {}

  public function readMarkdown(string $markdown): string {
    $markdown = sprintf("%s/%s", $this->lang, $markdown);
    $ast = Markdown\parse(
      new Markdown\ParserContext(), 
      file_get_contents($this->path . $markdown)
    );
    return (new Markdown\HTMLRenderer(
      new Markdown\RenderContext()
    ))->render($ast);
  }
}
