<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Services\MusicXML\MusicXML;
use App\Services\MusicXML\MusicXML\Note;
use App\Services\MusicXML\MusicXML\Track;
use App\Services\MusicXML\MusicXML\TrackNote;
use Illuminate\Support\Collection;

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
        $filename = resource_path('musicxml/Happiness_-_ARASHI.mxl');
        // $filename = resource_path('musicxml/Sakura_Sakura_Cherry_Blossoms.mxl');
        
        $musicXml = new MusicXML($filename);
        $scorePartWith = $musicXml->getScorePartWise();

        $scorePartWith->trackB()->trackNotes()->map(function(Collection $notes) {
            $notes->each(function(TrackNote $trackNote) {
                echo $trackNote->toAzureaCode() . ' ';
            });
            echo PHP_EOL;
        });

        return 0;
    }

}
