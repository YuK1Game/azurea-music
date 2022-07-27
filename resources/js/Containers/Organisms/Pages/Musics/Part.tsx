import React, { useState, useEffect } from 'react';

import Track from './Parts/Track';

const Part = ({ tracks, ...props } : any) => {
    return (
        <div {...props}>
            {tracks.map(({ measures } : any, index : number) => (
                <Track key={ index } measures={ measures } />
            ))}
        </div>
    )
}


export default Part;
