import React, { useCallback } from 'react';
import { Popover2 } from "@blueprintjs/popover2";

const Tooltip = ({ json, children, ...props } : any) => {

    const Content = useCallback(() => {
        return (
            <div>{JSON.stringify(json)}</div>
        )
    }, [ json ]);

    return (
        <Popover2 {...props}
            interactionKind={'click'}
            content={ <Content /> }
            renderTarget={({ isOpen, ref, ...popoverProps }) => {
                return React.cloneElement(children, { ref, ...popoverProps });
            }}
        />
    )
}


export default Tooltip;
