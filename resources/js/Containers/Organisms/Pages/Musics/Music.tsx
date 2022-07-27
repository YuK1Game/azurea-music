import React from "react";

import Part from "./Part";

const Music = ({ json } : any) => {
    return (
        <div>
            {json.parts.map(({ id, tracks } : any ) => (
                <Part key={ id } tracks={ tracks } />
            ))}
        </div>
    )
};

export default Music;
