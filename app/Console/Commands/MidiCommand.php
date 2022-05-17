<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpTabs\PhpTabs;
use PhpTabs\Music\{ Track, Measure, Beat, Voice, Note };
use PhpTabs\IOFactory;

class MidiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'music:midi';

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
        $filename = resource_path('midi/sample.mid');

        $tablature = new PhpTabs($filename);

        echo $tablature->toAscii();
        // die;
        // $tablature->save('my-file.xml', 'xml');
        // die;

        $tracks = collect($tablature->getTracks());
        
        $tracks->each(function(Track $track) {

            $measures = collect($track->getMeasures());
            
            $measures->each(function(Measure $measure) {
                
                $beats = collect($measure->getBeats());

                $beats->each(function(Beat $beat) {
                    
                    $voices = collect($beat->getVoices());

                    $voices->each(function(Voice $voice) {

                        $notes = collect($voice->getNotes());

                        $notes->each(function(Note $note) {
                            
                            echo sprintf("
                            Note
                            ----
                            
                            string: %s
                            value: %s
                            velocity: %s
                            is tied note: %s
                            ",
                            
                            $note->getString(),
                            $note->getValue(),
                            $note->getVelocity(),
                            $note->isTiedNote() ? 'true' : 'false'
                            );

                        });

                    });

                });

            });
        });

        return 0;
    }
}
