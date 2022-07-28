import React, { Fragment, useState } from 'react';
import styled from 'styled-components';

import MusicNote from './Notes/MusicNote';
import RestNote from './Notes/RestNote';
import DirectionNote from './Notes/DirectionNote';

const Note = ({ note : noteGroup, ...props } : any) => {

    const [ showJson, setShowJson ] = useState(false);

    return (
        <NoteComponent {...props} onClick={() => setShowJson(_showJson => ! _showJson)}>
            {showJson ? (
                <Fragment>
                    {JSON.stringify({ noteGroup })}
                </Fragment>
            ) : (
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
            )}
        </NoteComponent>
    )
}

const NoteComponent = styled.span`
    &:hover {
        background-color : rgba(255, 0, 0, .1);
    }
`;


export default Note;
