import React, { useState, useEffect } from 'react';

const RestNote = ({ note, ...props } : any) => {
    return (
        <div {...props}>
            {JSON.stringify({ note })}
        </div>
    )
}


export default RestNote;
