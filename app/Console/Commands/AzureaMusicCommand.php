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

use App\Services\Azurea\{
    ScorePart as AzureaScorePart,
};

use App\Services\Azurea\Note as AzureaNote;

class AzureaMusicCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'music:azurea';

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
            $azureaScorePart = new AzureaScorePart($scorePart);
        });

        return 0;
    }
}
