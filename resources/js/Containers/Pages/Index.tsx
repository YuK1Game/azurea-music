import React, { useState, useCallback } from 'react';
import styled from 'styled-components';

import DropArea from '../Organisms/Pages/Uploads/DropArea';
import Music from '../Organisms/Pages/Musics/Music';

import dummyJson from './music.json';

const Index = ({ ...props }) => {

    const [ musicJson, setMusicJson ] = useState(dummyJson);

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
        <IndexComponent {...props} className='container mx-auto px-12'>
            <DropAreaComponent>
                <DropArea onDrop={ handleDrop } />
            </DropAreaComponent>

            <div>
                ここにテキストを<span>書きます！</span>よろしく
            </div>

            <MusicComponent>
                {musicJson && (
                    <Music json={ musicJson } />
                )}
            </MusicComponent>
        </IndexComponent>
    )
}

const IndexComponent = styled.div`
    & > :not(:first-child) {
        margin-top : 32px;
    }
`;

const DropAreaComponent = styled.div``;

const MusicComponent = styled.div``;

export default Index;
