<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Services\MusicXML\MusicXML;
use App\Services\MusicXML\MusicXML\Part;
use App\Services\MusicXML\MusicXML\Measure;
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
        // $filename = resource_path('musicxml/Japanese_Army_March_Godzilla_1954.mxl');
        // $filename = resource_path('musicxml/Sakura_Sakura_Cherry_Blossoms.mxl');
        $filename = resource_path('musicxml/Doraemon_no_Uta.mxl');
        
        $musicXml = new MusicXML($filename);
        $scorePartWith = $musicXml->getScorePartWise();

        $scorePartWith->parts()->each(function(Part $part) {

            echo sprintf("%s\nTrack A\n%s\n", str_repeat('-', 100), str_repeat('-', 100));

            $part->trackA()->trackNotes()->map(function(Collection $notes, $index) {
                $notes->each(function(TrackNote $trackNote) {
                    echo $trackNote->toAzureaCode();
                });
                echo PHP_EOL;
            });

            echo sprintf("%s\nTrack B\n%s\n", str_repeat('-', 100), str_repeat('-', 100));

            $part->trackB()->trackNotes()->map(function(Collection $notes, $index) {
                $notes->each(function(TrackNote $trackNote) {
                    echo $trackNote->toAzureaCode();
                });
                echo PHP_EOL;
            });
        });

        return 0;
    }

}
