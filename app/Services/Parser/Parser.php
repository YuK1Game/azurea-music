<?php
namespace App\Services\Parser;

use ZipArchive;
use SimpleXMLElement;

class Parser
{
    protected SimpleXMLElement $xml;

    public function __construct(string $filename)
    {
        $zip = new ZipArchive;

        if ( ! $zip->open($filename)) {
            throw new \Exception('Error');
        }

        $zip->extractTo(storage_path(sprintf('musicxml/%s', basename($filename))));

        $this->xml = new SimpleXMLElement($this->getXmlString($zip));
    }

    public function getXml() : SimpleXMLElement
    {
        return $this->xml;
    }

    protected function getXmlString(ZipArchive $zip) : ?string
    {
        for ($i = 0 ; $i < $zip->numFiles ; $i++) {
            $filename = $zip->getNameIndex($i);
            
            if (preg_match('/^.*?.xml$/', $filename) === 1 && $filename !== 'META-INF/container.xml') {
                $fp = $zip->getStream($filename);
                $data = '';

                while ( ! feof($fp)) {
                    $data .= fgets($fp);
                }
                fclose($fp);
                
                return $data;
            }
        }

        throw new \Exception('Can\'t find xml data.');
    }

}