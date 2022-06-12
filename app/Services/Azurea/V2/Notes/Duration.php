<?php
namespace App\Services\Azurea\V2\Notes;

use App\Services\Azurea\V2\Note as AzureaNote;

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

    public function durationDenominator() : float
    {
        return $this->wholeDuration() / $this->duration;
    }

    public function duration() : int
    {
        $durationDenominator = $this->durationDenominator();

        $notes = [ 1, 2, 4, 8, 16, 24, 32, 48, 64 ];

        foreach ($notes as $noteValue) {
            if ($durationDenominator <= $noteValue) {
                return $noteValue;
            }
        }
        
        throw new \Exception(sprintf('Invalid value [%s]', $durationDenominator));
    }

    public function dotCount() : int
    {
        $ratio = $this->duration() / $this->durationDenominator();

        switch ($ratio) {
            case 1.00  : return 0;
            case 1.50  : return 1;
            case 1.75  : return 2;
            default :
                dd([
                    '$ratio' => $ratio,
                    'wholeDuration' => $this->wholeDuration(),
                    'baseDuration' => $this->duration,
                    'calcDuration' => $this->duration(),
                    'durationDenominator' => $this->durationDenominator(),
                ]);
                throw new \Exception('Error');
        }
    }
    
}