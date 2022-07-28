import React, { useState, useEffect } from 'react';
import styled from 'styled-components';

import Note from './Measure/Note';

import Tooltip from './Measure/Notes/Tooltips/Tooltip';

const Measure = ({ id, notes, ...props } : any) => {
    return (
        <MeasureComponent {...props}>
            <MeasureHeader>
                { id }
            </MeasureHeader>
            <MeasureNotesComponent>
                {notes.map((note : any, index : number) => (
                    <Note key={ index } note={ note } />
                ))}
            </MeasureNotesComponent>
        </MeasureComponent>
    )
}

const MeasureComponent = styled.div`
    display : flex;
    flex-direction : row;

    > *:first-child {
        margin-right : 20px;
    }

    > * {
        padding : 2px;
    }
`;

const MeasureHeader = styled.div`
    width : 50px;
    background-color : rgba(0, 0, 0, .2);
    user-select : none;
    text-align : right;
    padding-right : 8px;
`;

const MeasureNotesComponent = styled.div`
    > *:not(:first-child) {
        margin-left : 5px;
    }
`;

export default Measure;
