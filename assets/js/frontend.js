document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('form-validation');

    form?.addEventListener('submit', (event) => {
        event.preventDefault();

        const data = new FormData();
        data.append('action', 'dsubscribers_ajax');
        data.append('dsubscribers_action', document.getElementById('dsubscribers_action').value);
        data.append('dsubscribers_email', document.getElementById('dsubscribers_email').value);
        data.append('dsubscribers_nonce', document.getElementById('dsubscribers_form_nonce').value);

        fetch(dsubscribers_data.ajax_url, {
            method: 'POST',
            body: data
        }).then((response) => {
            return response.json();
        }).then((data) => {
            document.getElementById('dsubscribers_msg').innerHTML = data.msg;
            document.getElementById('dsubscribers_email').value = '';
        });
    });

    const formUnsubscribe = document.getElementById('form-validation-unsubscribe');

    formUnsubscribe.addEventListener('submit', (event) => {
        event.preventDefault();

        const data = new FormData();
        data.append('action', 'dsubscribers_ajax');
        data.append('dsubscribers_action', document.querySelector('#form-validation-unsubscribe input#dsubscribers_action').value);
        data.append('dsubscribers_email', document.querySelector('#form-validation-unsubscribe input#dsubscribers_email').value);
        data.append('dsubscribers_nonce', document.querySelector('#form-validation-unsubscribe input#dsubscribers_form_nonce').value);

        fetch(dsubscribers_data.ajax_url, {
            method: 'POST',
            body: data
        }).then((response) => {
            return response.json();
        }).then((data) => {
            console.log(data)
            document.getElementById('dsubscribers_unsubscribe_msg').innerHTML = data.msg;
            document.querySelector('#form-validation-unsubscribe input#dsubscribers_email').value = '';
        });
    });

    const formWidget = document.getElementById('form-validation-widget');

    formWidget?.addEventListener('submit', (event) => {
        event.preventDefault();

        const data = new FormData();
        data.append('action', 'dsubscribers_ajax');
        data.append('dsubscribers_action', document.querySelector('#form-validation-widget input#dsubscribers_action').value);
        data.append('dsubscribers_email', document.querySelector('#form-validation-widget input#dsubscribers_email').value);
        data.append('dsubscribers_nonce', document.querySelector('#form-validation-widget input#dsubscribers_form_nonce').value);

        fetch(dsubscribers_data.ajax_url, {
            method: 'POST',
            body: data
        }).then((response) => {
            return response.json();
        }).then((data) => {
            document.getElementById('dsubscribers_msg_widget').innerHTML = data.msg;
            document.getElementById('dsubscribers_email_widget').value = '';
        });
    });
});
