<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index()
    {
        $quill_json = '[{"insert":"Heading"},{"insert":"\\n","attributes":{"header":1}},{"insert":"bold","attributes":{"bold":true}},{"insert":"\\n"},{"insert":"bold and italic","attributes":{"bold":true,"italic":true}},{"insert":"\\nsome code"},{"insert":"\\n","attributes":{"code-block":true}},{"insert":"A quote"},{"insert":"\\n","attributes":{"blockquote":true}},{"insert":"ordered list"},{"insert":"\\n","attributes":{"list":"ordered"}},{"insert":"unordered list"},{"insert":"\\n","attributes":{"list":"bullet"}},{"insert":"link","attributes":{"link":"pub.dev/packages/quill_markdown"}},{"insert":"\\n"}]';
        $quill_json = json_decode($quill_json);
        $parser = new \DBlackborough\Quill\Parser\Html();
        $renderer = new \DBlackborough\Quill\Renderer\Html();

        $parser->load($quill_json)->parse();

        echo $renderer->load($parser->deltas())->render();
    }
}
