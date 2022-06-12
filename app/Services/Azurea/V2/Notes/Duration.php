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

    public function durationFraction() : float
    {
        $durationDecimal = $this->durationDecimal();

        if ($durationDecimal >= 1) {
            return 1;
        } else if ($durationDecimal >= 0.5) {
            return 0.5;
        } else if ($durationDecimal >= 0.25) {
            return 0.25;
        } else if ($durationDecimal >= 0.125) {
            return 0.125;
        } else if ($durationDecimal >= 0.0625) {
            return 0.0625;
        } else if ($durationDecimal >= 0.03125) {
            return 0.03125;
        }
        throw new \Exception(sprintf('Invalid value [%s]', $durationDecimal));
    }

    public function duration() : int
    {
        return (int) 1 / $this->durationFraction();
    }

    public function dotCount() : int
    {
        switch ($this->durationDecimal() / $this->durationFraction()) {
            case 1.5  : return 1;
            case 1.75 : return 2;
            default : return 0;
        }
    }
    
}