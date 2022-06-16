<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Services\Azurea\V2\Music as AzureaMusic;
use App\Services\Azurea\V2\Notes\Duration;
use Illuminate\Support\Facades\File;

class MusicCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'music:run {--test}';

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
        // $duration = new Duration(2, 'eighth', 4, 4);
        // dd($duration->duration());
        // die;

        if ($this->option('test')) {
            return $this->test();
        }

        $filename = resource_path('musicxml/Donten_Gintama_Opening_5.mxl');

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

    public function test()
    {
        $path = resource_path('musicxml');
        $files = File::files($path);

        foreach ($files as $file) {

            echo $file->getPathname();

            try {
                $azureaMusic = new AzureaMusic($file->getPathname());
                $parts = $azureaMusic->getCodes();
            } catch (\Exception $e) {
                echo ' [NG]' . PHP_EOL;
                throw $e;
            }

            echo ' [OK]' . PHP_EOL;
        }

        return 0;
    }
}
