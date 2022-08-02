import { useMemo } from "react";

const useMeasureTotalDuration = (notes : any) => {

    const calcSumDurationByDurations = (durations : any) => {
        return durations.map(({ value } : any) : number => {
            return value;
        })
        .reduce((acc : number, curr : any) => {
            return acc + parseInt(curr);
        }, 0);
    }

    const calcDurationByNote = (note : any) : number => {
        const { durations, is_chord, is_tie_start, relational_tie_end } = note;

        if (is_chord || ! durations) {
            return 0;
        }

        const totalDuration = calcSumDurationByDurations(durations);
        const totalTieEndDuration = (is_tie_start && relational_tie_end) ? calcDurationByNote(relational_tie_end) : 0;

        return totalDuration + totalTieEndDuration;
    }

    const calcDurationsByNotes = (_notes : any) => {
        return notes.map((noteGroup : any) => {
            return noteGroup.map(({ type, ...values } : any) => {
                return ['note', 'rest'].includes(type) ? calcDurationByNote(values) : 0;
            });
        });
    };

    const totalDuration = useMemo(() => {
        return calcDurationsByNotes(notes)
            .flat(4)
            .reduce((acc : number, curr : any) => {
                return acc + parseInt(curr);
            }, 0);
    }, [ notes ]);

    return {
        totalDuration,
    }
};

export default useMeasureTotalDuration;