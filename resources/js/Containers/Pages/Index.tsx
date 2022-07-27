import React, { useState, useCallback } from 'react';

import DropArea from '../Organisms/Pages/Uploads/DropArea';
import Music from '../Organisms/Pages/Musics/Music';

const Index = ({ ...props }) => {

    const [ musicJson, setMusicJson ] = useState(null);

    const handleDrop = useCallback((file : File) => {

        const formData = new FormData();
        formData.append('file', file);

        const param = {
            method : 'POST',
            body : formData,
        };

        fetch('/api/create_music_mml', param)
            .then(response => response.json())
            .then(json => {
                setMusicJson(json);
            })
            .catch(error =>{
                // エラー処理
            });
    }, []);

    return (
        <div {...props} className='container mx-auto px-12'>
            <DropArea onDrop={ handleDrop } />

            {musicJson && (
                <Music json={ musicJson } />
            )}
        </div>
    )
}

export default Index;
