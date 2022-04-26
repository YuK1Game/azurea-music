<?php
namespace App\Services\MusicXML\MusicXML\Measures;

interface MeasureContentInterface
{
    public function isNote() : bool;

    public function isBackup() : bool;
}