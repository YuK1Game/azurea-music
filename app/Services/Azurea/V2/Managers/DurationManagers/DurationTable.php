<?php
namespace App\Services\Azurea\V2\Managers\DurationManagers;
use Illuminate\Support\Collection;
use Generator;

class DurationTable
{
    protected int $wholeDuration;

    protected Collection $baseDurationTable;

    public function __construct(int $wholeDuration)
    {
        $this->wholeDuration = $wholeDuration;

        $this->initializeBaseDurationTable();
    }

    public function initializeBaseDurationTable() : void
    {
        $this->baseDurationTable = $this->getDurationBaseTable();
    }

    public function getDurationListByDuration(float $duration) : ?Collection
    {
        if ($duration > 0) {
            $keys = $this->baseDurationTable->keys()->values()->all();
            $createCombinationGenerator = $this->createCombination($keys, 3);

            foreach ($createCombinationGenerator as $keyList) {
                foreach ($keyList as $keys) {
                    $duratons = $this->getBaseDurationsByKeys($keys);
                    $sumDuration = $duratons->sum('value');

                    if ($sumDuration === $duration) {
                        return $duratons;
                    }
                }
            }
        }

        return null;
    }

    protected function getDurationList() : Collection
    {
        $list = collect();
        $whole = $this->wholeDuration > 64 ? $this->wholeDuration : 64;

        for ($value = 1 ; $value <= $whole ; $value++) {
            if ($duration = $this->division($this->wholeDuration, $value)) {
                $list->push($duration);
            }
        }

        return $list;
    }

    protected function getDurationBaseTable() : Collection
    {
        $list = $this->getDurationList()->map(function($value) {
            return $this->createDotTable()->map(function(Collection $dotTableRow) use($value) {
                return collect([
                    'dot'      => $dotTableRow->get('dot'),
                    'duration' => $this->wholeDuration / $value,
                    'value'    => round($value * $dotTableRow->get('ratio'), 10),
                ]);
            });
        })
        ->flatten(1)
        ->filter(function(Collection $durationTableRow) {
            return $durationTableRow->get('value') <= $this->wholeDuration;
        })
        ->sortByDesc('value')
        ->values();

        return $list;
    }

    protected function createDotTable() : Collection
    {
        return collect([
            collect(['dot' => 0, 'ratio' => 1.000 ]),
            collect(['dot' => 1, 'ratio' => 1.500 ]),
            collect(['dot' => 2, 'ratio' => 1.750 ]),
        ]);
    }

    protected function isDivisible(float $from, float $value, ?int $digits = 10) : bool
    {
        return ($from * pow(10, $digits - 1)) % $value === 0;
    }

    protected function division(float $from, float $value, ?int $digits = 10) : ?float
    {
        if ($this->isDivisible($from, $value, $digits)) {
            return round($from / $value, $digits);
        }
        return null;
    }

    protected function createCombination(array $arr, int $r) : Generator
    {
        for ($i = 1 ; $i <= $r ; $i++) {
            yield $this->combination($arr, $i);
        }
    }

    protected function combination(array $arr, int $r): ?array
    {
        $arr = array_values(array_unique($arr));

        $n = count($arr);
        $result = [];

        if($r < 0 || $n < $r){ return null; }

        if($r === 1){
            foreach($arr as $item){
                $result[] = [$item];
            }
        }

        if($r > 1){
            for($i = 0; $i < $n-$r+1; $i++){
                $sliced = array_slice($arr, $i + 1);
                $recursion = $this->combination($sliced, $r - 1);
                foreach($recursion as $one_set){
                    array_unshift($one_set, $arr[$i]);
                    $result[] = $one_set;
                }
            }
        }

        return $result;
    }

    protected function getBaseDurationsByKeys(array $keys) : Collection
    {
        return collect($keys)->map(function(int $key) {
            return $this->baseDurationTable->get($key);
        });
    }

}
