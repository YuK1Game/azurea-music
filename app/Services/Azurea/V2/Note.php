<?php

namespace App\Services\Azurea\V2;

use App\Services\Music\V2\MusicXML\Parts\Measures\Note as MusicXMLNote;
use App\Services\Music\V2\MusicXML\Parts\Measures\Direction as MusicXMLDirection;
use App\Services\Music\V2\MusicXML\Parts\Measures\Backup;
use App\Services\Music\V2\MusicXML\Parts\Measures\BlankNote;
use App\Services\Music\V2\MusicXML\Parts\Measures\MeasureChildrenInterface;
use Illuminate\Support\Collection;

use App\Services\Azurea\V2\Track as AzureaTrack;
use App\Services\Azurea\V2\Notes\{Duration, Key, Backup as BackupCode, Direction};
use App\Services\Music\V2\MusicXML\Parts\Measures\Forward;

use App\Services\Azurea\V2\Managers\DurationManager;

class Note
{
    protected MeasureChildrenInterface $measureChildren;

    protected AzureaTrack $azureaTrack;

    protected ?Note $prevAzureaNote;

    protected ?Collection $currentTrackProperties = null;

    protected ?DurationManager $durationManager = null;

    protected ?float $customDuration = null;


    public function __construct(MeasureChildrenInterface $measureChildren, AzureaTrack $azureaTrack)
    {
        $this->measureChildren = $measureChildren;
        $this->azureaTrack = $azureaTrack;
    }

    public function setPrevAzureaNote(?Note $prevAzureaNote)
    {
        $this->prevAzureaNote = $prevAzureaNote;
    }

    public function setCurrentTrackProperties(Collection $currentTrackProperties): void
    {
        $this->currentTrackProperties = $currentTrackProperties;
    }

    public function setCustomDuration(float $customDuration): void
    {
        $this->customDuration = $customDuration;
    }

    public function getCustomDuration(): ?float
    {
        return $this->customDuration;
    }

    public function index(): ?int
    {
        return $this->measureChildren->index();
    }

    public function getCode()
    {
        $measureChildren = $this->measureChildren;

        switch (get_class($measureChildren)) {
            case MusicXMLNote::class:
                return $this->isTieEnd() ? '' : $this->getNoteCode();

            case MusicXMLDirection::class:
                if ($dynamicKey = $measureChildren->dynamics()) {
                    switch ($dynamicKey) {
                        case 'ffff':
                        case 'fff':
                        case 'ff':
                            return 'v15';
                        case 'f':
                            return 'v14';
                        case 'mf':
                            return 'v13';
                        case 'mp':
                            return 'v11';
                        case 'p':
                            return 'v10';
                        case 'pp':
                            return 'v9';
                        case 'ppp':
                            return 'v8';
                        case 'pppp':
                            return 'v7';
                    }
                }
                return '';

            case BlankNote::class:
            case Forward::class:
            case Backup::class:
                if ($durationCodes = $this->durationManager()->getDurationCodes()) {
                    return $durationCodes->map(function ($row) {
                        $duration = $row['duration'];
                        $dot = $row['dot'];
                        return sprintf('r%s%s', $duration, str_repeat('.', $dot));
                    })->join('');
                }

                return '';
        }

        throw new \Exception(sprintf('Invalid class [%s]', get_class($measureChildren)));
    }

    public function getNoteCode(): string
    {
        if ($this->grace()) {
            return '';
        }

        $pitch = $this->isRest() ? 'r' : $this->getPhonicNotePitch();

        try {
            $durationCodes = $this->getDurationCodes();
        } catch (\Exception $e) {
            return sprintf('[ERROR %s]', $this->getCustomDuration() ?? $this->duration());
        }


        return $durationCodes->map(function ($durationCode) use ($pitch) {
            $code = sprintf('%s%s', $pitch, $durationCode);

            if ($this->measureChildren->accent()) {
                $code = sprintf('%s*14', $code);
            }

            if ($this->measureChildren->staccato()) {
                $code = sprintf('%s*13', $code);
            }

            if ($this->isTieStart() && !($this->arpeggiate() && $this->isChord())) {
                if ($tieEndNote = $this->getRelationalTieEnd()) {
                    $tieEndNoteCode = $tieEndNote->getNoteCode();
                    $code = sprintf('%s&%s', $code, $tieEndNoteCode);
                } else {
                    $code = sprintf('%s&%s', $code, '[Not found tie end]');
                }
            }

            if (
                $this->isChord() &&
                !$this->arpeggiate() &&
                !$this->isTieEnd()
            ) {
                $code = sprintf(':%s', $code);
            }

            return $code;
        })->join('&');
    }

    public function getDrumNoteCode(): string
    {
        $key = sprintf('o%d%s', $this->getMusicXMLNote()->unpitchedOctave(), $this->getMusicXMLNote()->unpitchedStep());

        switch ($key) {
            case 'o4c':
                return '[ERROR]';
            case 'o4d': /* PedalHiHat  */
                return 'o4f';
            case 'o4e': /* Rest        */
                return 'r';
            case 'o4f': /* BassDrum    */
                return 'o4c';
            case 'o4g':
                return '[ERROR]';
            case 'o4a': /* FloorTam    */
                return 'o4e';
            case 'o4b':
                return '[ERROR]';
            case 'o5c': /* SnareDrum   */
                return 'o4c+';
            case 'o5d': /* LowTam      */
                return 'o4d+';
            case 'o5e': /* HiTam       */
                return 'o4d';
            case 'o5f': /* RideCymbal  */
                return 'o4g+';
            case 'o5g': /* HiHatCymbal */
                return 'o4f';
            case 'o5a': /* CrashCymbal */
                return 'o4f+';
            case 'o5b': /* Unknown     */
                return 'o4f+';
        }

        throw new \Exception(sprintf('Drum Note Error. [%s]', $key));
    }

    public function getPitch(): string
    {
        return sprintf('o%d%s', $this->getMusicXMLNote()->pitchOctave(), $this->getMusicXMLNote()->pitchStep());
    }

    public function getPhonicNotePitches(): array
    {
        $key = new Key();
        $key->setPitchStep($this->pitchStep());
        $key->setPitchOctave($this->pitchOctave());
        $key->setPitchAlter($this->pitchAlter());

        return $key->newPitch();
    }

    protected function getPhonicNotePitch(): string
    {
        if ($this->measureChildren->hasUnpitched()) {
            return $this->getDrumNoteCode();
        }
        list($newPitchStep, $newPitchOctave) = $this->getPhonicNotePitches();
        return sprintf('o%d%s', $newPitchOctave, $newPitchStep);
    }

    public function getDurations(): Collection
    {
        if ($duration = $this->durationManager()->getDurationCodes()) {
            return $duration;
        }
        throw new \Exception('Duration codes not match.');
    }

    public function getDurationCodes(): Collection
    {
        if (!$durationCodes = $this->getDurations()) {
            $this->throwException('Duration codes not match.');
        }

        return $durationCodes->map(function ($durationCode) {
            $duration = $durationCode['duration'];
            $dot = str_repeat('.', $durationCode['dot']);
            return sprintf('%s%s', $duration, $dot);
        });
    }

    public function getCurrentDivision(): int
    {
        return (int) $this->getCurrentTrackProperty('currentDivision');
    }

    public function getCurrentBeat(): int
    {
        return (int) $this->getCurrentTrackProperty('currentBeat');
    }

    public function getCurrentBeatType(): int
    {
        return (int) $this->getCurrentTrackProperty('currentBeatType');
    }

    public function getWholeDuration(): int
    {
        return 4 * $this->getCurrentDivision();
    }

    public function getCurrentTrackProperty(string $key)
    {
        return $this->currentTrackProperties->get($key);
    }

    protected function isMusicXMLNote(): bool
    {
        return $this->measureChildren instanceof MusicXMLNote;
    }

    protected function defaultPitch(): ?string
    {
        if ($this->isMusicXMLNote()) {
            return sprintf('o%d%s', $this->getMusicXMLNote()->pitchOctave(), $this->getMusicXMLNote()->pitchStep());
        }
        return null;
    }

    protected function getRelationalTieEnd(): ?Note
    {
        if ($this->isTieStart()) {
            return $this->azureaTrack->measures()->filter(function (Collection $notes, int $measureNumber) {
                return $measureNumber >= $this->getCurrentMeasureNumber();
            })
                ->flatten()
                ->filter(function (Note $note) {
                    return $note->isTieEnd()
                        && ($this->index() < $note->index() || $this->getCurrentMeasureNumber() < $note->getCurrentMeasureNumber())
                        && $this->getPhonicNotePitch() === $note->getPhonicNotePitch();
                })
                ->first();
        }
        return null;
    }

    public function getMusicXMLNote(): MusicXMLNote
    {
        if ($this->isMusicXMLNote()) {
            return $this->measureChildren;
        }
        $this->throwException('Not a MusicXMLNote Object.');
    }

    public function getCurrentMeasureNumber(): int
    {
        $currentMeasure = $this->measureChildren->getMeasure();
        return $currentMeasure->number();
        // return $currentMeasure->index();
    }

    public function getMeasureChildren(): MeasureChildrenInterface
    {
        return $this->measureChildren;
    }

    protected function durationManager(): DurationManager
    {
        if (!$this->durationManager) {
            $this->durationManager = new DurationManager($this);
        }
        return $this->durationManager;
    }

    private function throwException(string $message): void
    {
        $errorJson = [
            'message' => $message,
            'measure_number' => $this->getCurrentMeasureNumber(),
            'note' => [
                'class' => get_class($this->measureChildren),
                'xml' => $this->measureChildren->getXml(),
            ],
        ];
        throw new \Exception(sprintf('%s%s%s', 'Error', PHP_EOL, json_encode($errorJson, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)));
    }

    public function json(): Collection
    {
        try {
            return collect([
                'pitches' => !$this->isRest() ? $this->getPhonicNotePitches() : null,
                'durations' => $this->getDurations(),
                'is_grace' => $this->grace(),
            ]);
        } catch (\Exception $e) {
            $this->throwException($e->getMessage());
        }
    }

    public function __call($name, $arguments)
    {
        if (method_exists($this, $name)) {
            return $this->{$name}(...$arguments);
        }
        if (method_exists($this->measureChildren, $name)) {
            return $this->measureChildren->{$name}(...$arguments);
        }
        $this->throwException(sprintf('Method "%s" is undefined.', $name));
    }
}
