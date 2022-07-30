import React, { Fragment, useState, useMemo, useCallback } from 'react';

const MusicNote = ({ note, enableTieEnd, ...props } : any) => {

    const { pitches, durations, is_chord, is_tie_start, is_tie_end, relational_tie_end } = note;

    const code = useMemo(() => {
        if ( ! pitches) {
            console.warn('Invalid pitched', note);
            return null;
        }

        if (is_tie_end && ! enableTieEnd) {
            return null;
        }

        const step = pitches?.[0];
        const octave = pitches?.[1];

        const code = durations?.map(({ dot, duration, value } : any) => {
            return `o${ octave }${ step }${ duration }${ '.'.repeat(dot) }`;
        }).join('&');

        return code;
    }, [ pitches, durations, is_tie_end, enableTieEnd ]);

    const chord = useMemo(() => {
        return (is_chord && ! is_tie_end) ? ':' : null;
    }, [ is_chord, is_tie_end ]);

    const TieEndComponent = useCallback(({ ...props }) => {
        if (is_tie_start && relational_tie_end) {
            return (
                <Fragment>
                    &amp;<MusicNote note={ relational_tie_end } enableTieEnd={ true } />
                </Fragment>
            );
        }
        return null;

    }, [ is_tie_start, is_tie_end, relational_tie_end ]);

    return (
        <Fragment {...props}>
            { chord }{ code }<TieEndComponent />
        </Fragment>
    )
}


export default MusicNote;
