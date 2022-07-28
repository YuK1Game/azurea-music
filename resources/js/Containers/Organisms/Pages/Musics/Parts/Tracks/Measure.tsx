import React, { useMemo } from 'react';
import styled from 'styled-components';

import Note from './Measure/Note';

const Measure = ({ id, notes, ...props } : any) => {

    const noteTotalDuration = useMemo(() => {
        return notes.map((noteGroup : any) => {
            return noteGroup.map(({ type, ...values } : any) => {
                if (type === 'note') {
                    const { durations, is_chord } = values;

                    return (durations && ! is_chord) ? durations.map(({ value } : any) => {
                        return value;
                    }) : 0;
                }
                return 0;
            });
        })
        .flat(4)
        .reduce(function(sum : number, element : number){
            return sum + element;
        }, 0)
    }, [ notes ]);

    return (
        <MeasureComponent {...props}>
            <MeasureHeader>
                { id }
            </MeasureHeader>
            <div>
                {JSON.stringify({ noteTotalDuration })}
            </div>
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

const MeasureNotesComponent = styled.div`
    > *:not(:first-child) {
        margin-left : 5px;
    }
`;

export default Measure;
