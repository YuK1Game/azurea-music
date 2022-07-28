import React, { Fragment, useMemo } from 'react';

const RestNote = ({ note, ...props } : any) => {
    const { durations } = note;

    const code = useMemo(() => {
        const code = durations?.map(({ dot, duration, value } : any) => {
            return `r${ duration }${ '.'.repeat(dot) }`;
        }).join('&');

        return code;
    }, [ durations ]);

    return (
        <Fragment {...props}>
            { code }
        </Fragment>
    )
}


export default RestNote;
