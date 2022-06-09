<?php
namespace App\Services\Music\V2;

use SimpleXMLElement;

interface MusicXMLChildrenInterface
{
    public function __construct(SimpleXMLElement $xml, $parent);
}