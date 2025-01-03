import apiFetch from '@wordpress/api-fetch';
import {useEffect, useState} from '@wordpress/element';

export function App() {
    const [ message, setMessage ] = useState();

    useEffect( () => {
        apiFetch( { path: '/wp/v2/settings' } ).then( ( settings ) => {
            setMessage( settings.dsubscribers_options.dont_exists_msg );
        } );
    }, [] );

    return <div>{message}</div>
}
