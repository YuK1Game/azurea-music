import React, { Fragment, useState, useEffect } from 'react';

import MusicNote from './Notes/MusicNote';
import RestNote from './Notes/RestNote';
import DirectionNote from './Notes/DirectionNote';

const Note = ({ note : noteGroup, ...props } : any) => {
    return (
        <Fragment>
            {noteGroup && noteGroup?.map && noteGroup.map((note : any, index : number) => {
                switch (note?.type) {
                    case 'note' : return <MusicNote key={ index } note={ note } />
                    case 'rest' : return <RestNote key={ index } note={ note } />
                    case 'direction' : return <DirectionNote key={ index } note={ note } />
                    default : return `Type error [${ note?.type }]`;
                }
            })}
        </Fragment>
    )
}


export default Note;
