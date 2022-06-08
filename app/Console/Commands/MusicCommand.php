<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Services\Parser\MusicXML;

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
        $part = $musicXml->parts()->first();
        
        foreach ($musicXml->parts() as $part) {
            foreach ($part->measures() as $measure) {
                foreach ($measure->notes() as $note) {
                    echo $note->isRest() ? 'rest' : 'note';
                    echo PHP_EOL;
                }
            }
            echo '---------------------' . PHP_EOL;
        }

        return 0;
    }
}
