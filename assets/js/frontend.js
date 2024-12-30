document.addEventListener('DOMContentLoaded', () => {

    const handleSubscribeForm = (formId, msgId) => {
        const form = document.getElementById(formId)
        form?.addEventListener('submit', (event) => {
            event.preventDefault();

            const data = new FormData();
            data.append('action', 'dsubscribers_ajax');
            data.append('dsubscribers_action', document.querySelector(`#${formId} input#dsubscribers_action`).value);
            data.append('dsubscribers_email', document.querySelector(`#${formId} input#dsubscribers_email`).value);
            data.append('dsubscribers_nonce', document.querySelector(`#${formId} input#dsubscribers_form_nonce`).value);

            fetch(dsubscribers_data.ajax_url, {
                method: 'POST',
                body: data
            }).then((response) => {
                return response.json();
            }).then((data) => {
                document.getElementById(msgId).innerHTML = data.msg;
                document.querySelector(`#${formId} input#dsubscribers_email`).value = '';
            });
        });
    }

    handleSubscribeForm('form-validation', 'dsubscribers_msg');
    handleSubscribeForm('form-validation-unsubscribe', 'dsubscribers_unsubscribe_msg');
    handleSubscribeForm('form-validation-widget', 'dsubscribers_msg_widget');
});
