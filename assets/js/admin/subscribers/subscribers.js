import domReady from '@wordpress/dom-ready';
import {createRoot} from '@wordpress/element';
import {DataView} from "./DataView";


domReady(() => {
    createRoot(
        document.getElementById('dsubscribers-subscribers')
    ).render(
        <DataView />
    );
});

