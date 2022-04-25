<?php
namespace App\Services\MusicXML;

use ZipArchive;
use Symfony\Component\DomCrawler\Crawler as DOMCrawler;

use App\Services\MusicXML\MusicXML\ScorePartWise;

class MusicXML
{
    protected $xml;

    public function __construct(string $filename)
    {
        $zip = new ZipArchive;

        if ( ! $zip->open($filename)) {
            throw new \Exception('Error');
        }

        $this->xml = $this->getXmlData($zip);
    }

    public function getScorePartWise() : ScorePartWise
    {
        return new ScorePartWise(new DOMCrawler($this->xml));
    }

    protected function getXmlData(ZipArchive $zip) : ?string
    {
        for ($i = 0 ; $i < $zip->numFiles ; $i++) {
            $filename = $zip->getNameIndex($i);
            
            if (preg_match('/^[A-Za-z]{2}\-\d{1,}\.xml$/', $filename) === 1) {
                $fp = $zip->getStream($filename);
                $data = '';

                while ( ! feof($fp)) {
                    $data .= fgets($fp);
                }
                fclose($fp);
                
                return $data;
            }
        }

        throw new \Exception('');
    }

}