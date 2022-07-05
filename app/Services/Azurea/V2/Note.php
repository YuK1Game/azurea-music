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

    protected DurationManager $durationManager;


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
        return $this->getMusicXMLNote() && $this->getMusicXMLNote()->index();
    }

    public function getCode()
    {
        $measureChildren = $this->measureChildren;

        $durationManager = $this->createDurationManager();

        if ($measureChildren instanceof MusicXMLNote) {
            return $this->getNoteCode();
        }

        if ($measureChildren instanceof MusicXMLDirection) {
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
        }

        if ($measureChildren instanceof BlankNote) {
            if ($durationManager->hasDurationCode()) {
                return sprintf('r%s', $durationManager->getDurationCode());
            }
            return '';
        }

        if ($measureChildren instanceof Forward) {
            // if($duration = $this->measureChildren->duration()) {
            //     $backupCode = new BackupCode($this->getWholeDuration(), $duration, true);
            //     return $backupCode->getNoteCodes();
            // }

            // return '';
            if ($durationManager->hasDurationCode()) {
                return sprintf('r%s', $durationManager->getDurationCode());
            }
            return '';
        }

        if ($measureChildren instanceof Backup) {

            // try {
            //     if($duration = $this->measureChildren->duration()) {
            //         $backupCode = new BackupCode($this->getWholeDuration(), $duration);
            //         return $backupCode->getNoteCodes();
            //     }
            // } catch (\Exception $e) {
            //     $this->throwException($e->getMessage());
            // }
            if ($durationManager->hasDurationCode()) {
                return sprintf('r%s', $durationManager->getDurationCode());
            }
            return '';
        }

        throw new \Exception(sprintf('Invalid class [%s]', get_class($measureChildren)));
    }

    public function getNoteCode() : string
    {
        $pitch =  $this->isTieEnd() || $this->getMusicXMLNote()->isRest() ? 'r' : $this->getPhonicNotePitch();

        $code = sprintf('%s%s', $pitch, $this->getDurationCode());

        if ($this->isChord()) {
            $code = sprintf(':%s', $code);
        }

        if ($this->isAccent()) {
            $code = sprintf('%s*14', $code);
        }

        if ($this->isStaccato()) {
            $code = sprintf('%s*13', $code);
        }

        if ($this->isChord() && $this->isTieEnd()) {
            return '';
        }

        if ($this->isGrace()) {
            return '';
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

    protected function getBlankCode() : string
    {
        return 'r';
    }

    protected function getBackupCode() : string
    {
        return '';
    }

    public function getDurationCode() : string
    {
        // if ($this->measureChildren->isTuplet()) {
        //     return $this->measureChildren->tupletActualNotes() * $this->measureChildren->tupletNormalNotes();
        // }

        // if ($duration = $this->measureChildren->duration()) {
        //     return $this->createDuration($duration);
        // }
        // return '0';
        return $this->createDurationManager()->getDurationCode();
    }

    protected function createDuration(int $duration) : string
    {
        try {
            $durationManager = new Duration(
                $duration,
                (int) $this->currentTrackProperties->get('currentDivision'),
                (int) $this->currentTrackProperties->get('currentBeat'),
                (int) $this->currentTrackProperties->get('currentBeatType'),
            );

            return sprintf('%s%s', $durationManager->duration(), str_repeat('.', $durationManager->dotCount()));

        } catch (\Exception $e) {
            $this->throwException($e->getMessage());
        }
    }

    public function isChord() : bool
    {
        return $this->isMusicXMLNote() && $this->getMusicXMLNote()->isChord();
    }

    public function isAccent() : bool
    {
        return $this->isMusicXMLNote() && $this->getMusicXMLNote()->accent();
    }

    public function isStaccato() : bool
    {
        return $this->isMusicXMLNote() && $this->getMusicXMLNote()->staccato();
    }

    public function isGrace() : bool
    {
        return $this->isMusicXMLNote() && $this->getMusicXMLNote()->grace();
    }

    public function isTieStart() : bool
    {
        return $this->isMusicXMLNote() && $this->getMusicXMLNote()->isTieStart();
    }

    public function isTieEnd() : bool
    {
        return $this->isMusicXMLNote() && $this->getMusicXMLNote()->isTieEnd();
    }

    public function getType() : ?string
    {
        if ($this->isMusicXMLNote()) {
            return $this->getMusicXMLNote()->type();
        }
        return null;
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

    protected function createDurationManager() : DurationManager
    {
        return new DurationManager($this);
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
