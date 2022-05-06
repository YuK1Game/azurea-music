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
use App\Services\Azurea\Notes\NotePitchTable;

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
        $filename = resource_path('musicxml/_Yume_De_Aru_You_Ni.mxl');

        $musicXml = new MusicXML($filename);
        $music = $musicXml->music();
        $scoreParts = $music->scoreParts();

        try {
            $scoreParts->slice(1, 1)->each(function(ScorePart $scorePart, int $scorePartIndex) {
                echo join(PHP_EOL, [
                    str_repeat('=', 100),
                    sprintf('ScorePart [%d]', $scorePartIndex + 1),
                    str_repeat('=', 100),
                ]) . PHP_EOL;
                $azureaScorePart = new AzureaScorePart($scorePart);
                $azureaScorePart->exportCode();
            });
        } catch (\InvalidArgumentException $e) {
            echo $e;
            die;
        }

        return 0;
    }
}
