<?php
namespace App\Services\Azurea\V2\Notes;

use Illuminate\Support\Collection;

class Backup
{
    protected int $wholeDuration;

    protected int $duration;

    public function __construct(int $wholeDuration, int $duration)
    {
        $this->wholeDuration = $wholeDuration;

        $this->duration = $duration;
    }

    public function getNoteCode() : string
    {
        $extraDuration = $this->extraDuration();

        $noteDurations = $this->createNoteDurations();
        $noteDurations = $noteDurations->filter(function(Collection $noteDuration) use(&$extraDuration) {
            if ($extraDuration >= $noteDuration->get('duration')) {
                $extraDuration -= $noteDuration->get('duration');
                return true;
            }
            return false;
        });

        if ($extraDuration === 0) {
            return $noteDurations->map(function(Collection $noteDuration) {
                return sprintf('r%s', $noteDuration->get('type'));
            })->join('');
        }
        
        throw new \Exception(sprintf('Invalid duration. [%s] and whole duration [%s]', $this->duration, $this->wholeDuration));
    }

    protected function extraDuration() : int
    {
        return $this->wholeDuration - $this->duration;
    }

    protected function createNoteDurations() : Collection
    {
        $durations = collect();
        $whole = $this->wholeDuration;
        $value = 1;

        do {
            $duration = $whole / $value;
            $duration >= 1 && $durations->push(collect(['type' => $value, 'duration' => $duration ]));
            $value *= 2;
        } while($duration >= 1);
        
        return $durations;
    }
}