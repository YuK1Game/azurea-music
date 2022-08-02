import React, { useMemo } from 'react';
import styled from 'styled-components';

import Note from './Measures/Note';

import useMeasureTotalDuration from './Measures/States/useMeasureTotalDuration';

const Measure = ({ id, notes, ...props } : any) => {

    const { totalDuration } = useMeasureTotalDuration(notes);

    return (
        <MeasureComponent {...props}>
            <MeasureHeader>
                { id }
            </MeasureHeader>
            <TotalDuration>
                {totalDuration}
            </TotalDuration>
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
    width : 40px;
    background-color : rgba(0, 0, 0, .2);
    user-select : none;
    text-align : right;
    padding-right : 8px;
`;

const TotalDuration = styled.div`
    width : 40px;
`;

const MeasureNotesComponent = styled.div`
    > *:not(:first-child) {
        margin-left : 5px;
    }
`;

export default Measure;
