<?php
namespace App\Services\MusicXML\MusicXML;

interface MeasureContentInterface
{
    public function isNote() : bool;

    public function isBackup() : bool;
}