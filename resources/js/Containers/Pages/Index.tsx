import React, { useCallback } from 'react';
import { RecoilRoot } from 'recoil';
import styled from 'styled-components';

import DropArea from '../Organisms/Pages/Uploads/DropArea';
import Music from '../Organisms/Pages/Musics/Music';

import useMusicRecoil from '../../Recoils/useMusicRecoil';


const IndexWithContext = () => (
    <RecoilRoot>
        <Index />
    </RecoilRoot>
);

const Index = ({ ...props }) => {

    const { musicJson, setMusicJson } = useMusicRecoil();

    const handleDrop = useCallback((file : File) => {

        const formData = new FormData();
        formData.append('file', file);

        const param = {
            method : 'POST',
            body : formData,
        };

        fetch('/api/create_music_mml', param)
            .then(response => response.json())
            .then(json => setMusicJson(json))
            .catch(error =>{
                // エラー処理
            });
    }, []);

    return (
        <IndexComponent {...props} className='container mx-auto px-12'>
            <DropAreaComponent>
                <DropArea onDrop={ handleDrop } />
            </DropAreaComponent>

            <MusicComponent>
                {musicJson && <Music json={ musicJson } /> }
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

export default IndexWithContext;
