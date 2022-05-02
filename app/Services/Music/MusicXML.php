<?php
namespace App\Services\Music;

use App\Services\Music\Music;
use ZipArchive;
use Symfony\Component\DomCrawler\Crawler as DOMCrawler;

class MusicXML
{
    protected $xml;

    public function __construct(string $filename)
    {
        $zip = new ZipArchive;

        if ( ! $zip->open($filename)) {
            throw new \Exception('Error');
        }

        $zip->extractTo(storage_path(sprintf('musicxml/%s', basename($filename))));

        $this->xml = $this->getXmlData($zip);
    }

    public function music() : Music
    {
        return new Music(new DOMCrawler($this->xml));
    }


    protected function getXmlData(ZipArchive $zip) : ?string
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