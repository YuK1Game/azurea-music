<?php
namespace App\Services\Music\V2\MusicXML\Parts\Measures;

use App\Services\Music\V2\MusicXML\Parts\Measure;

interface MeasureChildrenInterface
{
    public function duration() : ?int;

    public function pitchStep() : ?string;

    public function pitchOctave() : ?int;

    public function accidental() : ?string;

    public function getMeasure() : Measure;
}