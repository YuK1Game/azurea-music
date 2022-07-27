import React, { useState, useEffect } from 'react';

import Measure from './Tracks/Measure';

const Track = ({ measures, ...props } : any) => {
    return (
        <div {...props}>
            {measures.map(({ measure_id, notes } : any) => (
                <Measure key={ measure_id } notes={ notes } />
            ))}
        </div>
    )
}


export default Track;
