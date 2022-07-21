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

        $parts = $azureaMusic->getCodes();

        // $parts->each(function($part) {
        //     $this->addText(str_repeat('=', 100));
        //     $this->addText(sprintf('Part[%s] : %s', $part->get('id'), $part->get('part_name')));
        //     $this->addText(str_repeat('=', 100));

        //     $part->get('tracks')->each(function($track, $trackIndex) {

        //         $this->addText(str_repeat('-', 100));
        //         $this->addText(sprintf('Track[%d]', $trackIndex));
        //         $this->addText(str_repeat('-', 100));

        //         $track->get('measures')->each(function($notes, $measureIndex) {

        //             $this->addText(sprintf('【%d】 ', $measureIndex), false);

        //             $this->addText($notes->flatten()->join(''));

        //         });
        //         $this->addText('');
        //         $this->addText('');
        //     });
        //     $this->addText('');
        //     $this->addText('');
        // });

        return response()->json($parts);

    }

    protected function addText(?string $text = null, ?bool $useBr = true) : void
    {
        if ( ! is_null($text)) {
            $this->text .= sprintf('%s%s', $text, $useBr ? PHP_EOL : '');
        }
    }
}
