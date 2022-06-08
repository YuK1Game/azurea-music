<?php
namespace App\Services\Parser;

use SimpleXMLElement;

interface MusicXMLChildrenInterface
{
    public function __construct(SimpleXMLElement $xml, $parent);
}