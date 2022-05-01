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
        $filename = resource_path('musicxml/_Yume_De_Aru_You_Ni.mxl');

        $musicXml = new MusicXML($filename);
        $music = $musicXml->music();
        $scoreParts = $music->scoreParts();
        $scoreParts->each(function(ScorePart $scorePart, $partIndex) {
            $part = $scorePart->part();
            $measures = $part->measures();
            $measures->each(function(Measure $measure) use($partIndex) {
                $measure->childrenChunk()->each(function(MeasureChunk $chunk, $measureChunkIndex) use($partIndex) {
                    // if ($partIndex === 0 || $measureChunkIndex === 1) {
                    //     return;
                    // }
                    
                    // $chunk->each(function(MeasureChildrenInterface $measureChildren) {
                    //     if ($measureChildren instanceof Note) {
                    //         if ($measureChildren->isRest()) {
                    //             echo sprintf('%s %d', 'r', $measureChildren->duration());
                    //         } else {
                    //             echo sprintf('%s%d %d', $measureChildren->pitchStep(), $measureChildren->pitchOctave(), $measureChildren->duration());
                    //         }
                    //     }
                    //     echo PHP_EOL;
                    // });
                    // echo PHP_EOL;
                    echo $chunk->totalNoteDuration();
                });
                echo '----' . PHP_EOL;
            });
        });

        return 0;
    }
}
