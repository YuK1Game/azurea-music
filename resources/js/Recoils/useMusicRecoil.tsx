import { atom, selector, useRecoilState, useRecoilValue, useRecoilCallback } from 'recoil';

const useMusicRecoil = () => {
    const musicJsonState = atom({
        key : 'musicJsonState',
        default : { parts : []},
    });

    const [ musicJson, setMusicJson ] = useRecoilState(musicJsonState);

    const handleUpdatePart = useRecoilCallback(() => (part : any, index : number) => {
        setMusicJson((_json : any) => {
            return {..._json, parts : _json.parts.map(({..._data} : any, _index : number) => {
                return _index === index ? part : _data;
            })}
        });
    }, []);

    return {
        musicJson,
        setMusicJson,
    }
};

export default useMusicRecoil;
