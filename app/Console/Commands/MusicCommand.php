<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Services\Azurea\V2\Music as AzureaMusic;
use App\Services\Azurea\V2\Notes\Duration;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

use App\Services\Azurea\V2\Managers\DurationManagers\DurationTable;

class MusicCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'music:run {--no-debug} {--no-warning}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $text = '';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        ini_set('memory_limit', '8G');
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $filename = resource_path('musicxml/brave_heart.mxl');

        $azureaMusic = new AzureaMusic($filename);
        $parts = $azureaMusic->getCodes();

        $parts->each(function($part) {
            $this->addText(str_repeat('=', 100));
            $this->addText(sprintf('Part[%s] : %s', $part->get('id'), $part->get('part_name')));
            $this->addText(str_repeat('=', 100));

            $part->get('tracks')->each(function($track, $trackIndex) {

                $this->addText(str_repeat('-', 100));
                $this->addText(sprintf('Track[%d]', $trackIndex));
                $this->addText(str_repeat('-', 100));

                $track->get('measures')->each(function($notes, $measureIndex) {

                    ! $this->option('no-debug') && $this->addText(sprintf('【%d】 ', $measureIndex), false);

                    $this->addText($notes->flatten()->join(''));

                    ! $this->option('no-debug') && ! $this->option('no-warning') && $this->addText($this->validateNotes($notes->flatten()));

                });
                $this->addText('');
                $this->addText('');
            });
            $this->addText('');
            $this->addText('');
        });

        File::put(base_path('music.txt'), $this->text);

        return 0;
    }

    protected function addText(?string $text = null, ?bool $useBr = true) : void
    {
        if ( ! is_null($text)) {
            $this->text .= sprintf('%s%s', $text, $useBr ? PHP_EOL : '');
        }
    }

    protected function validateNotes(Collection $notes) : ?string
    {
        $notesValues = $notes
            ->map(function($note) {
                $note = preg_replace('/\:.*?$/', '', $note);
                $_notes = preg_split('/\&/', $note);
                return collect($_notes);
            })
            ->map(function($_notes) {
                return $_notes->map(function($note) {
                    $note = preg_replace('/\*\d{1,2}/', '', $note);
                    $note = preg_replace('/^o\d{1}[a-g]\+?(.*?)$/', '$1', $note);
                    $note = preg_replace('/^r(.*?)$/', '$1', $note);
                    return $note;
                });
            })
            ->flatten(1)
            ->filter(function($note) {
                return $note !== '';
            });

        try {
            $totalDuration = (float) $notesValues->sum(function(string $duration) {
                if (preg_match('/^(\d{1,})(\.)?(\.)?$/', $duration, $matches) === 1) {
                    $duration = (int) $matches[1];
                    $dot1 = ($matches[2] ?? null) === '.';
                    $dot2 = ($matches[3] ?? null) === '.';

                    $base = 1 * ($dot2 ? 1.75 : ($dot1 ? 1.5 : 1));

                    return $base / $duration;
                }

                return 0;
            });

            if (round($totalDuration, 10) !== 0.75) {
                return sprintf('[Warn] Total duration miss match. (%s)', $totalDuration, $notes->join(''));
            }

        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return null;

    }

}
