<?php
namespace App\Services\Azurea\V2\Notes;

use Illuminate\Support\Collection;

class Backup
{
    protected ?int $wholeDuration;

    protected int $duration;

    protected ?bool $isForward;

    public function __construct(int $wholeDuration, int $duration, ?bool $isForward = false)
    {
        $this->wholeDuration = $wholeDuration;
        $this->duration = $duration;
        $this->isForward = $isForward;
    }

    public function getNoteCodes() : Collection
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
            });
        }
        
        throw new \Exception(sprintf('Invalid duration. [%s] and whole duration [%s]', $this->duration, $this->wholeDuration));
    }

    protected function extraDuration() : int
    {
        if ($this->isForward) {
            return $this->duration;
        }
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