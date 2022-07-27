import React, { useState, useEffect } from 'react';

import Note from './Measure/Note';

const Measure = ({ notes, ...props } : any) => {
    return (
        <div {...props}>
            {notes.map((note : any, index : number) => (
                <Note key={ index } note={ note } />
            ))}
        </div>
    )
}


export default Measure;
