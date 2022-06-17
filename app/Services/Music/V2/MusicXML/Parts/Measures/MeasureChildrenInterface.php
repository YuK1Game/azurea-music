<?php
namespace App\Services\Music\V2\MusicXML\Parts\Measures;

use App\Services\Music\V2\MusicXML\Parts\Measure;
use SimpleXMLElement;

interface MeasureChildrenInterface
{
    public function duration() : ?int;

    public function pitchStep() : ?string;

    public function pitchOctave() : ?int;

    public function pitchAlter() : ?int;

    public function hasUnpitched() : bool;

    public function getMeasure() : Measure;

    public function getXml() : ?SimpleXMLElement;
}