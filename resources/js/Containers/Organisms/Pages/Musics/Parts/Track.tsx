import React, { useState, useEffect } from 'react';
import styled from 'styled-components';

import Measure from './Tracks/Measure';

const Track = ({ measures, ...props } : any) => {
    return (
        <TrackComponent {...props}>
            {measures.map(({ measure_id, notes } : any) => (
                <Measure key={ measure_id } id={ measure_id } notes={ notes } />
            ))}
        </TrackComponent>
    )
}

const TrackComponent = styled.div`
    & > *:not(:first-child) {
        margin-top : 10px;
    }
`;


export default Track;
