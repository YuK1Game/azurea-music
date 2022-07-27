import React, { useState, useEffect, Fragment } from 'react';

import MusicNote from './Notes/MusicNote';
import RestNote from './Notes/RestNote';
import DirectionNote from './Notes/DirectionNote';

const Note = ({ note, ...props } : any) => {
    return (
        <Fragment>
            {JSON.stringify(note)}
            {/* {json.notes.map((note : any) => {
                switch (note?.type) {
                    case 'note' : return <MusicNote json={ json } />
                    case 'rest' : return <RestNote json={ json } />
                    case 'direction' : return <DirectionNote json={ json } />
                    default : return `Type error [${ note?.type }]`;
                }
            })} */}
        </Fragment>
    )
}


export default Note;
