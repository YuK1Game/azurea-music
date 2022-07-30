import { useMemo } from "react";

const useMeasureTotalDuration = (notes : any) => {

    const calcSumDurationByDurations = (durations : any) => {
        return durations.map(({ value } : any) : number => {
            return value;
        });
    }

    const calcDurationByNote = (note : any) : number => {
        const { durations, is_chord, is_tie_start, relational_tie_end } = note;

        const hasTotalDuration = durations && ! is_chord;
        const hasTieEndTotalDuration = is_tie_start && relational_tie_end;

        const totalDuration = hasTotalDuration ? calcSumDurationByDurations(durations) : 0;
        const totalTieEndDuration = hasTieEndTotalDuration ? calcDurationByNote(relational_tie_end) : 0;

        return totalDuration + totalTieEndDuration;
    }

    const calcDurationsByNotes = (_notes : any) => {
        return notes.map((noteGroup : any) => {
            return noteGroup.map(({ type, ...values } : any) => {
                return type === 'note' ? calcDurationByNote(values) : 0;
            });
        });
    };

    const totalDuration = useMemo(() => {
        const _a = calcDurationsByNotes(notes);
        console.log('_a', _a)
        return _a;
    }, [ notes ]);

    return {
        totalDuration,
    }
};

export default useMeasureTotalDuration;