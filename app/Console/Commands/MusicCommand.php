<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;

use App\Services\Music\{
    MusicXML,
    ScorePart,
    Part,
    Parts\Measure,
    Parts\MeasureChunk,
    Parts\Measures\MeasureChildrenInterface,
    Parts\Measures\Note,
    Parts\Measures\Backup,
};

use App\Services\Azurea\Note as AzureaNote;


class MusicCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'music:run';

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
        $filename = resource_path('musicxml/Tokyo_Revengers_OP.mxl');

        $musicXml = new MusicXML($filename);
        $music = $musicXml->music();
        $scoreParts = $music->scoreParts();
        $scoreParts->each(function(ScorePart $scorePart, $partIndex) {

            echo sprintf('Part [%d]' . PHP_EOL, $partIndex + 1);
            echo str_repeat('-', 80) . PHP_EOL;

            $part = $scorePart->part();
            $maxDuration = $part->maxDuration();

            $part->tracks()->each(function(Collection $track, string $trackName) use($maxDuration, $partIndex) {
                echo sprintf('TrackName [%s]' . PHP_EOL . PHP_EOL, $trackName);

                $track->each(function(?MeasureChunk $measureChunk) use($maxDuration) {

                    if ($measureChunk) {
                        $measureChunk->notes()->each(function(Note $note) use($maxDuration) {
                            $azureaNote = new AzureaNote($note, $maxDuration);
                            echo $azureaNote->code();

                        });
                    } else {
                        $azureaNote = new AzureaNote(null, $maxDuration);
                        echo $azureaNote->code();
                    }
                    
                    echo PHP_EOL;
                });

                echo PHP_EOL;

            });

            echo str_repeat('=', 80) . PHP_EOL;
            
        });

        return 0;
    }
}
