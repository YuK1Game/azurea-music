<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Services\Parser\MusicXML;
use App\Services\Parser\MusicXML\Parts\Measures\Note;

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
        $filename = resource_path('musicxml/Dango_Daikazoku_Full_Version_-_Clannad_Original_Soundtrack.mxl');

        $musicXml = new MusicXML($filename);
        $part = $musicXml->parts()->first();
        
        foreach ($musicXml->parts() as $part) {
            dd($part->tracks());
            foreach ($part->tracks() as $track) {
                foreach ($track->notes() as $note) {
                    if ($note instanceof Note) {
                        echo $note->duration() . PHP_EOL;
                    }
                }
            }
        }

        return 0;
    }
}
