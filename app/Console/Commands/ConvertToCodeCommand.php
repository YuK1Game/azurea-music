<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Services\MusicXML\MusicXML;
use App\Services\MusicXML\MusicXML\Note;
use App\Services\MusicXML\MusicXML\Track;

class ConvertToCodeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'convert:to_code';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $filename = resource_path('musicxml/Sakura_Sakura_Cherry_Blossoms.mxl');
        $musicXml = new MusicXML($filename);
        $scorePartWith = $musicXml->getScorePartWise();
        $trackA = $scorePartWith->trackA();
        
        $trackA->flatten()->each(function(Note $note) {
            echo $note->toAzureaCode();
        });

        return 0;
    }

}
