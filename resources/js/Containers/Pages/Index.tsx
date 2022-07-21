import React, { useCallback } from 'react';

import DropArea from '../Organisms/Pages/Uploads/DropArea';

const Index = ({ ...props }) => {

    const handleDrop = useCallback((file : File) => {

        const formData = new FormData();
        formData.append('file', file);

        const param = {
            method : 'POST',
            body : formData,
        };

        fetch('/api/create_music_mml', param)
            .then((res)=>{
                return( res.json() );
            })
            .then((json)=>{
                // 通信が成功した際の処理
            })
            .catch((error)=>{
                // エラー処理
            });
    }, []);

    return (
        <div className='container mx-auto px-12'>
            <DropArea onDrop={ handleDrop } />
        </div>
    )
}

export default Index;
