<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\Azurea\V2\Music as AzureaMusic;

class CreateMusicMMLController extends Controller
{
    protected string $text = '';

    public function post(Request $request)
    {
        $file = $request->file('file');
        $filename = $file->path();

        $azureaMusic = new AzureaMusic($filename);

        $parts = $azureaMusic->json();

        return response()->json($parts);

    }

    protected function addText(?string $text = null, ?bool $useBr = true) : void
    {
        if ( ! is_null($text)) {
            $this->text .= sprintf('%s%s', $text, $useBr ? PHP_EOL : '');
        }
    }
}
