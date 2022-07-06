<?php
namespace App\Services\Music\V2\MusicXML\Parts\Measures;

use App\Services\Music\V2\MusicXML\Parts\Measure;
use SimpleXMLElement;

interface MeasureChildrenInterface
{
    public function index() : ?int;

    public function duration() : ?int;

    public function pitchStep() : ?string;

    public function pitchOctave() : ?int;

    public function pitchAlter() : ?int;

    public function hasUnpitched() : bool;

    public function isTuplet() : bool;

    public function tupletActualNotes() : ?int;

    public function tupletNormalNotes() : ?int;

    public function isRest() : bool;

    public function isChord() : bool;

    public function grace() : bool;

    public function accent() : bool;

    public function staccato() : bool;

    public function isTieStart() : bool;

    public function isTieEnd() : bool;

    public function dynamics() : ?string;

    public function getMeasure() : Measure;

    public function getXml() : ?SimpleXMLElement;
}
