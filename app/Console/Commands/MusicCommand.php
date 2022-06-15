<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Services\Azurea\V2\Music as AzureaMusic;

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
        $filename = resource_path('musicxml/Kimi_no_Shiranai_Monogatari_-_Bakemonogatari_ED.mscz.mxl');

        $azureaMusic = new AzureaMusic($filename);
        $parts = $azureaMusic->getCodes();

        $parts->each(function($part) {
            $part->get('tracks')->each(function($track) {
                $track->get('measures')->each(function($notes, $measureIndex) {
                    echo sprintf('[%d] ', $measureIndex);
                    echo $notes->join('');
                    echo PHP_EOL;
                });
                echo PHP_EOL;
                echo PHP_EOL;
            });
            echo PHP_EOL;
            echo PHP_EOL;
        });

        return 0;
    }
}
