import React, { useMemo, useCallback } from 'react';
import { useDropzone } from 'react-dropzone';

const DropArea = ({ onDrop : onDropFile, ...props } : any) => {

    const onDrop = useCallback((acceptedFiles : any) => {
        if (acceptedFiles?.length > 0) {
            const acceptedFile = acceptedFiles[0];
            onDropFile?.(acceptedFile)
        }
      }, [ onDropFile ]);

    const dropZoneProps = useMemo(() => ({
        onDrop,
        accept : {
            'application/*' : ['.mxl'],
        },
    }), [ onDrop ]);

    const { getRootProps, getInputProps, isDragActive } = useDropzone(dropZoneProps)

    return (
        <div {...props} className='box-border h-64 w-full p-4 border-4 border-gray-400 bg-gray-200' {...getRootProps()}>
            <input {...getInputProps()} />

            <div className='h-full w-full bg-gray-400'>
                {isDragActive ? (
                    <p>Drop the files here ...</p>
                ) : (
                    <p>Drag 'n' drop some files here, or click to select files</p>
                )}
            </div>
        </div>
    )
}

export default DropArea;
