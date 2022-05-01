<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Services\Music\{
    MusicXML,
    ScorePart,
    Part,
    Parts\Measure,
    Parts\Measures\MeasureChildInterface,
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
        $scoreParts->each(function(ScorePart $scorePart) {
            $part = $scorePart->part();
            $measures = $part->measures();
            $measures->each(function(Measure $measure) {
                $children = $measure->children();
                $children->each(function(MeasureChildInterface $measureChild) {
                    if ($measureChild instanceof Note) {
                        echo $measureChild->pitchStep();
                    }
                });
            });
        });

        return 0;
    }
}
