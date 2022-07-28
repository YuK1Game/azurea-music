import React, { Fragment, useMemo } from 'react';

const DirectionNote = ({ note, ...props } : any) => {
    const { dynamics, tempo } = note;

    const volumeCode = useMemo(() => {
        switch (dynamics) {
            case 'fff' : return 'v15';
            case 'ff'  : return 'v14';
            case 'f'   : return 'v13';
            case 'mf'  : return 'v12';
            case 'mp'  : return 'v11';
            case 'p'   : return 'v10';
            case 'pp'  : return 'v9';
            case 'ppp' : return 'v8';
        }
        return null;

    }, [ dynamics ]);

    const tempoCode = useMemo(() => {
        return tempo ? `t${ tempo }` : null;
    }, [ tempo ]);

    return (
        <Fragment {...props}>
            { tempoCode }
            { volumeCode }
        </Fragment>
    )
}


export default DirectionNote;
