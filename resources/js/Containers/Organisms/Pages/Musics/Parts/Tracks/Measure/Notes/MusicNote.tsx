import React, { Fragment, useState, useEffect } from 'react';

const MusicNote = ({ note, ...props } : any) => {

    const { pitches : [ step, octave ]} = note;

    return (
        <Fragment {...props}>
            o{ octave }{ step }
        </Fragment>
    )
}


export default MusicNote;
