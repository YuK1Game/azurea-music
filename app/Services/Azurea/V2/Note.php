<?php
namespace App\Services\Azurea\V2;

use App\Services\Music\V2\MusicXML\Parts\Measures\Note as MusicXMLNote;
use App\Services\Music\V2\MusicXML\Parts\Measures\Direction as MusicXMLDirection;
use App\Services\Music\V2\MusicXML\Parts\Measures\Backup;
use App\Services\Music\V2\MusicXML\Parts\Measures\BlankNote;
use App\Services\Music\V2\MusicXML\Parts\Measures\MeasureChildrenInterface;
use Illuminate\Support\Collection;

use App\Services\Azurea\V2\Track as AzureaTrack;
use App\Services\Azurea\V2\Notes\{ Duration, Key, Backup as BackupCode, Direction};
use App\Services\Music\V2\MusicXML\Parts\Measures\Forward;

use App\Services\Azurea\V2\Managers\DurationManager;

class Note
{
    protected MeasureChildrenInterface $measureChildren;

    protected AzureaTrack $azureaTrack;

    protected ?Note $prevAzureaNote;

    protected ?Collection $currentTrackProperties = null;

    protected ?DurationManager $durationManager = null;


    public function __construct(MeasureChildrenInterface $measureChildren, AzureaTrack $azureaTrack)
    {
        $this->measureChildren = $measureChildren;
        $this->azureaTrack = $azureaTrack;
    }

    public function setPrevAzureaNote(?Note $prevAzureaNote)
    {
        $this->prevAzureaNote = $prevAzureaNote;
    }

    public function setCurrentTrackProperties(Collection $currentTrackProperties) : void
    {
        $this->currentTrackProperties = $currentTrackProperties;
    }

    public function index() : ?int
    {
        return $this->measureChildren->index();
    }

    public function isChord() : bool
    {
        return $this->measureChildren->isChord();
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
                        case 'ff' : return 'v15';
                        case 'f'  : return 'v14';
                        case 'mf' : return 'v13';
                        case 'mp' : return 'v11';
                        case 'p'  : return 'v10';
                        case 'pp' : return 'v9';
                    }
                }
                return '';

            case BlankNote::class:
            case Forward::class:
            case Backup::class:
                if ($durationCodes = $this->durationManager()->getDurationCodes()) {
                    return $durationCodes->map(function($row) {
                        $duration = $row['duration'];
                        $dot = $row['dot'];
                        return sprintf('r%s', $duration, str_repeat('.', $dot));
                    })->join('');
                }return '';
        }

        throw new \Exception(sprintf('Invalid class [%s]', get_class($measureChildren)));
    }

    public function getNoteCode() : string
    {
        if ($this->measureChildren->grace()) {
            return '';
        }

        $pitch =  $this->measureChildren->isRest() ? 'r' : $this->getPhonicNotePitch();

        $code = sprintf('%s%s', $pitch, $this->getDurationCode());

        if ($this->measureChildren->accent()) {
            $code = sprintf('%s*14', $code);
        }

        if ($this->measureChildren->staccato()) {
            $code = sprintf('%s*13', $code);
        }

        if ($this->isTieStart()) {
            if ($tieEndNote = $this->getRelationalTieEnd()) {
                $tieEndNoteCode = $tieEndNote->getNoteCode();
                $code = sprintf('%s&%s', $code, $tieEndNoteCode);

            } else {

                // dd([
                //     'index' => $this->index(),
                //     'measure_index' => $this->getCurrentMeasureNumber(),
                //     'pitch' => $this->defaultPitch(),
                // ]);

                $code = sprintf('%s&%s', $code, '[Not found tie end]');
            }
        }


        if ($this->measureChildren->isChord() && ! $this->measureChildren->isTieEnd()) {
            $code = sprintf(':%s', $code);
        }

        return $code;
    }

    public function getDrumNoteCode() : string
    {
        $key = sprintf('o%d%s', $this->getMusicXMLNote()->unpitchedOctave(), $this->getMusicXMLNote()->unpitchedStep());

        switch ($key) {
            case 'o4c' :                   return '[ERROR]';
            case 'o4d' : /* PedalHiHat  */ return 'o4f';
            case 'o4e' : /* Rest        */ return 'r';
            case 'o4f' : /* BassDrum    */ return 'o4c';
            case 'o4g' :                   return '[ERROR]';
            case 'o4a' : /* FloorTam    */ return 'o4e';
            case 'o4b' :                   return '[ERROR]';
            case 'o5c' : /* SnareDrum   */ return 'o4c+';
            case 'o5d' : /* LowTam      */ return 'o4d+';
            case 'o5e' : /* HiTam       */ return 'o4d';
            case 'o5f' : /* RideCymbal  */ return 'o4g+';
            case 'o5g' : /* HiHatCymbal */ return 'o4f';
            case 'o5a' : /* CrashCymbal */ return 'o4f+';
            case 'o5b' : /* Unknown     */ return 'o4f+';
        }

        throw new \Exception(sprintf('Drum Note Error. [%s]', $key));
    }

    public function getPitch() : string
    {
        return sprintf('o%d%s', $this->getMusicXMLNote()->pitchOctave(), $this->getMusicXMLNote()->pitchStep());
    }

    protected function getPhonicNotePitch() : string
    {
        if ($this->measureChildren->hasUnpitched()) {
            return $this->getDrumNoteCode();
        }

        $key = new Key();
        $key->setPitchStep($this->measureChildren->pitchStep());
        $key->setPitchOctave($this->measureChildren->pitchOctave());
        $key->setPitchAlter($this->measureChildren->pitchAlter());

        list($newPitchStep, $newPitchOctave) = $key->newPitch();

        return sprintf('o%d%s', $newPitchOctave, $newPitchStep);
    }

    public function getDurationCode() : string
    {
        $durationCodes = $this->durationManager()->getDurationCodes();

        if ($durationCodes->count() === 1) {
            $durationCode = $durationCodes->first();
            $duration = $durationCode['duration'];
            $dot      = $durationCode['dot'];
            return sprintf('%s%s', $duration, str_repeat('.', $dot));
        }

        if ($this->measureChildren->isTuplet()) {
            return $this->measureChildren->tupletActualNotes() * $this->measureChildren->tupletNormalNotes();
        }

        return sprintf('[ERROR %d/%d]', $this->measureChildren->duration(), $this->getWholeDuration());
    }

    public function getWholeDuration() : int
    {
        $currentDivision = (int) $this->getCurrentTrackProperty('currentDivision');
        $currentBeat     = (int) $this->getCurrentTrackProperty('currentBeat');
        $currentBeatType = (int) $this->getCurrentTrackProperty('currentBeatType');
        return $currentDivision * 4 / $currentBeatType * $currentBeat;
    }

    public function getCurrentTrackProperty(string $key)
    {
        return $this->currentTrackProperties->get($key);
    }

    public function isTieStart() : bool
    {
        return $this->measureChildren->isTieStart();
    }

    public function isTieEnd() : bool
    {
        return $this->measureChildren->isTieEnd();
    }

    protected function isMusicXMLNote() : bool
    {
        return $this->measureChildren instanceof MusicXMLNote;
    }

    protected function defaultPitch() : ?string
    {
        if ($this->isMusicXMLNote()) {
            return sprintf('o%d%s', $this->getMusicXMLNote()->pitchOctave(), $this->getMusicXMLNote()->pitchStep());
        }
        return null;
    }

    protected function getRelationalTieEnd() : ?Note
    {
        if ($this->isTieStart()) {
            return $this->azureaTrack->measures()->filter(function(Collection $notes, int $measureNumber) {
                return $measureNumber >= $this->getCurrentMeasureNumber();
            })
            ->flatten()
            ->filter(function(Note $note) {
                return $note->isTieEnd()
                    && ($this->index() < $note->index() || $this->getCurrentMeasureNumber() < $note->getCurrentMeasureNumber())
                    && $this->getPhonicNotePitch() === $note->getPhonicNotePitch();
            })
            ->first();
        }
        return null;
    }

    public function getMusicXMLNote() : MusicXMLNote
    {
        if ($this->isMusicXMLNote()) {
            return $this->measureChildren;
        }
        $this->throwException('Not a MusicXMLNote Object.');
    }

    public function getCurrentMeasureNumber() : int
    {
        $currentMeasure = $this->measureChildren->getMeasure();
        return $currentMeasure->number();
    }

    public function getMeasureChildren() : MeasureChildrenInterface
    {
        return $this->measureChildren;
    }

    protected function durationManager() : DurationManager
    {
        if ( ! $this->durationManager) {
            $this->durationManager = new DurationManager($this);
        }
        return $this->durationManager;
    }

    private function throwException(string $message) : void
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

}
