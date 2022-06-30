<?php
namespace App\Services\Music\V2\MusicXML\Parts;

use App\Services\Music\V2\MusicXMLChildrenInterface;
use App\Services\Music\V2\MusicXML\Part;
use App\Services\Music\V2\MusicXML\Parts\Measure;

use Illuminate\Support\Collection;
use SimpleXMLElement;


class MeasureTrack extends Collection
{
    protected Measure $measure;

    public function setMeasure(Measure $measure)
    {
        $this->measure = $measure;
    }

    public static function create(iterable $data, Measure $parentMeasure)
    {
        $measureTracks = new self($data);
        $measureTracks->setMeasure($parentMeasure);
        return $measureTracks;
    }

}