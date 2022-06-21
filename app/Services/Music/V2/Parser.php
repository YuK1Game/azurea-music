<?php
namespace App\Services\Music\V2;

use ZipArchive;
use SimpleXMLElement;

class Parser
{
    protected SimpleXMLElement $xml;

    protected ZipArchive $zip;

    public function __construct(string $filename)
    {
        $this->zip = new ZipArchive;

        if ( ! $this->zip->open($filename)) {
            throw new \Exception('Error');
        }

        $this->xml = new SimpleXMLElement($this->getXmlString($this->zip));
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