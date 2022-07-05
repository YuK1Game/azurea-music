<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Services\Azurea\V2\Music as AzureaMusic;
use App\Services\Azurea\V2\Notes\Duration;
use Illuminate\Support\Collection;
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
        $filename = resource_path('musicxml/Dragon_Quest_V_Overture.mxl');

        $azureaMusic = new AzureaMusic($filename);
        $parts = $azureaMusic->getCodes();

        $parts->each(function($part) {
            echo str_repeat('-', 100) . PHP_EOL;
            echo sprintf('Part[%s] : %s' . PHP_EOL, $part->get('id'), $part->get('part_name'));
            echo str_repeat('-', 100) . PHP_EOL;

            $part->get('tracks')->each(function($track, $trackIndex) {
                $track->get('measures')->each(function($notes, $measureIndex) use($trackIndex) {
                    echo sprintf('[%d] ', $measureIndex);
                    echo $notes->flatten()->join('');
                    echo PHP_EOL;

                    $this->validateNotes($notes->flatten());
                });
                echo PHP_EOL;
                echo PHP_EOL;
            });
            echo PHP_EOL;
            echo PHP_EOL;
        });

        return 0;
    }

    protected function validateNotes(Collection $notes)
    {
        $notes->transform(function($note) {
            $note = preg_replace('/\*\d{1,2}/', '', $note);
            $note = preg_replace('/\:.*?$/', '', $note);
            $note = preg_replace('/\&.*?$/', '', $note);
            $note = preg_replace('/^o\d{1}[a-gr](.*?)$/', '$1', $note);
            return $note;
        });

        try {
            $totalDuration = (float) $notes->sum(function(string $duration) {
                if (preg_match('/^(\d{1,})(\.)?(\.)?$/', $duration, $matches) === 1) {
                    $duration = (int) $matches[1];
                    $dot1 = ($matches[2] ?? null) === '.';
                    $dot2 = ($matches[3] ?? null) === '.';

                    $base = 1 * ($dot2 ? 1.75 : ($dot1 ? 1.5 : 1));

                    return $base / $duration;
                }

                return 0;
            });

            $totalDuration = round($totalDuration, 10);

            if (round($totalDuration, 10) !== 1.0) {
                // echo sprintf('[Warn] Total duration miss match. (%s)', $totalDuration, $notes->join('')) . PHP_EOL;
            }
            return true;

        } catch (\Exception $e) {
            echo $e->getMessage() . PHP_EOL;
        }

    }

}
