<?php
namespace App\Services\Azurea\V2\Notes;

class Duration
{
    protected int $duration;

    protected int $division;

    protected int $beatType;

    public function __construct(int $duration, int $division, int $beatType)
    {
        $this->duration = $duration;
        $this->division = $division;
        $this->beatType = $beatType;
    }

    public function wholeDuration() : int
    {
        return $this->division * $this->beatType;
    }

    public function durationDecimal() : float
    {
        return $this->duration / $this->wholeDuration();
    }

    public function durationFraction() : int
    {
        $durationDecimal = $this->durationDecimal();

        if ($durationDecimal >= 1) {
            return 1;
        } else if ($durationDecimal >= 0.5) {
            return 2;
        } else if ($durationDecimal >= 0.25) {
            return 4;
        } else if ($durationDecimal >= 0.125) {
            return 8;
        } else if ($durationDecimal >= 0.0625) {
            return 16;
        } else if ($durationDecimal >= 0.03125) {
            return 32;
        }
        throw new \Exception(sprintf('Invalid value [%s]', $durationDecimal));
    }

    public function duration() : int
    {
        return $this->durationFraction();
    }
}