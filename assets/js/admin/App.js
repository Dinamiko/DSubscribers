import apiFetch from '@wordpress/api-fetch';
import {useEffect, useState} from '@wordpress/element';
import {useDispatch, useSelect} from '@wordpress/data';
import {__} from '@wordpress/i18n';
import {store as noticesStore} from '@wordpress/notices';
import {
    __experimentalHeading as Heading,
    Button,
    Panel,
    PanelBody,
    PanelRow,
    TextareaControl,
    ToggleControl,
    TextControl,
    NoticeList
} from "@wordpress/components";

export function App() {
    const [sendEmail, setSendEmail] = useState(false)
    const [emailSubject, setEmailSubject] = useState('');
    const [emailMessage, setEmailMessage] = useState('');
    const [subscribedMessage, setSubscribedMessage] = useState('');
    const [existMessage, setExistMessage] = useState('');
    const [unsubscribedMessage, setUnsubscribedMessage] = useState('');
    const [doNotExistMessage, setDoNotExistMessage] = useState('');

    const {createSuccessNotice} = useDispatch(noticesStore);

    useEffect(() => {
        apiFetch({path: '/wp/v2/settings'}).then((settings) => {
            setSendEmail(settings.dsubscribers_options.send_email_checkbox);
            setEmailSubject(settings.dsubscribers_options.email_subject);
            setEmailMessage(settings.dsubscribers_options.email_msg);
            setSubscribedMessage(settings.dsubscribers_options.subscribed_msg);
            setExistMessage(settings.dsubscribers_options.exists_msg);
            setUnsubscribedMessage(settings.dsubscribers_options.unsubscribed_msg);
            setDoNotExistMessage(settings.dsubscribers_options.dont_exists_msg);
        });
    }, []);

    const saveSettings = () => {
        apiFetch({
            path: '/wp/v2/settings',
            method: 'POST',
            data: {
                dsubscribers_options: {
                    send_email_checkbox: sendEmail,
                    email_subject: emailSubject,
                    email_msg: emailMessage,
                    subscribed_msg: subscribedMessage,
                    exists_msg: existMessage,
                    unsubscribed_msg: unsubscribedMessage,
                    dont_exists_msg: doNotExistMessage
                },
            },
        }).then(() => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth',
            });

            createSuccessNotice(
                __('Settings saved.', 'dsuscribers')
            );
        });
    };

    const Notices = () => {
        const {removeNotice} = useDispatch(noticesStore);
        const notices = useSelect((select) =>
            select(noticesStore).getNotices()
        );

        if (notices.length === 0) {
            return null;
        }

        return <NoticeList notices={notices} onRemove={removeNotice}/>;
    }

    return (
        <>
            <Heading level={3}
                     adjustLineHeightForInnerControls="large">{__('DSubscribers Settings', 'dsubscribers')}</Heading>
            <Notices/>
            <Panel>
                <PanelBody
                    title={__('Subscriber E-mail', 'dsubscribers')}
                >
                    <PanelRow>
                        <ToggleControl
                            checked={sendEmail}
                            label={__('Send E-mail to subscriber', 'dsubscribers')}
                            onChange={() => setSendEmail((state) => !state)}
                        />
                    </PanelRow>
                    <PanelRow>
                        <TextControl
                            label={__('Email Subject', 'dsubscribers')}
                            value={emailSubject}
                            onChange={(value) => {
                                setEmailSubject(value)
                            }}
                        />
                    </PanelRow>
                    <PanelRow>
                        <TextareaControl
                            label={__('Message', 'dsubscribers')}
                            value={emailMessage}
                            onChange={(value) => setEmailMessage(value)}
                            help={__('This box accepts HTML tags', 'dsubscribers')}
                        />
                    </PanelRow>
                </PanelBody>
                <PanelBody
                    title={__('Form Messages', 'dsubscribers')}
                >
                    <PanelRow>
                        <TextControl
                            label={__('Subscribed', 'dsubscribers')}
                            value={subscribedMessage}
                            onChange={(value) => {
                                setSubscribedMessage(value)
                            }}
                        />
                    </PanelRow>
                    <PanelRow>
                        <TextControl
                            label={__('Subscriber already exists', 'dsubscribers')}
                            value={existMessage}
                            onChange={(value) => {
                                setExistMessage(value)
                            }}
                        />
                    </PanelRow>
                    <PanelRow>
                        <TextControl
                            label={__('Unsubscribed', 'dsubscribers')}
                            value={unsubscribedMessage}
                            onChange={(value) => {
                                setUnsubscribedMessage(value)
                            }}
                        />
                    </PanelRow>
                    <PanelRow>
                        <TextControl
                            label={__('Subscriber don\'t exists', 'dsubscribers')}
                            value={doNotExistMessage}
                            onChange={(value) => {
                                setDoNotExistMessage(value)
                            }}
                        />
                    </PanelRow>
                </PanelBody>
            </Panel>
            <Button variant="primary" onClick={saveSettings}>
                {__('Save', 'dsubscribers')}
            </Button>
        </>
    )
}
