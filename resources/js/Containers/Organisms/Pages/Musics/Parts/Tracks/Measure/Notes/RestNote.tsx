import React, { useState, useEffect } from 'react';

const RestNote = ({ json, ...props } : any) => {
    return (
        <div {...props}>
            {JSON.stringify({ json })}
        </div>
    )
}


export default RestNote;
