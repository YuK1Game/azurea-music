import React, { Fragment, useState, useCallback } from 'react';
import styled from 'styled-components';

import MusicNote from './Notes/MusicNote';
import RestNote from './Notes/RestNote';
import DirectionNote from './Notes/DirectionNote';

import { Button } from "@blueprintjs/core";
import { Classes, Popover2 } from "@blueprintjs/popover2";

const Note = ({ note : noteGroup, ...props } : any) => {

    return (
        <NoteComponent {...props}>

            <Popover2
                interactionKind="click"
                popoverClassName={Classes.POPOVER2_CONTENT_SIZING}
                placement="bottom"
                content={
                    <ShopJsonComponent>
                        {JSON.stringify(noteGroup, null, 2)}
                    </ShopJsonComponent>
                }
                renderTarget={({ isOpen, ref, ...targetProps }) => (
                    <span {...targetProps} ref={ ref }>
                        {noteGroup && noteGroup?.map && noteGroup.map((note : any, index : number) => {
                            switch (note?.type) {
                                case 'note' : return <MusicNote key={ index } note={ note } />
                                case 'rest' : return <RestNote key={ index } note={ note } />
                                case 'direction' : return <DirectionNote key={ index } note={ note } />
                                default : return `Type error [${ note?.type }]`;
                            }
                        })}
                    </span>
                )}
            />

        </NoteComponent>
    )
}

const NoteComponent = styled.span`
    &:hover {
        background-color : rgba(255, 0, 0, .1);
    }
`;

const ShopJsonComponent = styled.pre`
    max-height : 300px;
    overflow-y : scroll;
    overflow-x : hidden;
`;


export default Note;
