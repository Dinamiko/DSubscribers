import apiFetch from '@wordpress/api-fetch';
import {useState, useMemo, useEffect} from '@wordpress/element';
import {DataViews} from "@wordpress/dataviews/wp";
import {filterSortAndPaginate} from '@wordpress/dataviews';
import "../../../css/admin/subscribers/subscribers.scss";
import {
    Button,
    __experimentalHStack as HStack,
    __experimentalText as Text,
    TextControl,
    CardFooter, __experimentalHeading as Heading
} from "@wordpress/components";
import {__} from "@wordpress/i18n";

const fields = [
    {
        id: 'id',
        label: 'ID',
    },
    {
        id: 'time',
        label: 'Registration Date',
    },
    {
        id: 'email',
        label: 'Email',
        enableGlobalSearch: true,
    },
]

const primaryField = 'id';
const defaultLayouts = {
    table: {
        layout: {
            primaryField,
        },
    },
};

export function DataView() {
    const [view, setView] = useState({
        type: 'table',
        perPage: 10,
        layout: defaultLayouts.table.layout,
        fields: [
            'id',
            'time',
            'email',
        ],
    })

    const [data, setData] = useState([])
    useEffect(() => {
        apiFetch({path: '/dsubscribers/v1/subscribers'})
            .then(data => setData(data))
    }, []);

    const {data: processedData, paginationInfo} = useMemo(() => {
        return filterSortAndPaginate(data, view, fields);
    }, [view, data]);

    const actions = [
        {
            id: 'edit',
            label: __('Edit', 'dsubscribers'),
            RenderModal: ({items: [item], closeModal}) => {
                const [email, setEmail] = useState('');
                useEffect(() => {
                    setEmail(item.email)
                }, [item])

                const onSubmit = (event) => {
                    event.preventDefault();

                    apiFetch({
                        path: '/dsubscribers/v1/subscriber',
                        method: 'PUT',
                        data: {
                            "email": item.email,
                            "new_email": email
                        }
                    }).then(() => {
                        apiFetch({path: '/dsubscribers/v1/subscribers'})
                            .then((data) => {
                                setData(data)
                                closeModal()
                            });
                    })
                };

                return (
                    <form onSubmit={onSubmit}>
                        <HStack>
                            <TextControl
                                label={__('Email', 'dsubscribers')}
                                value={email}
                                onChange={(value) => setEmail(value)}
                            />
                        </HStack>
                        <HStack>
                            <Button variant="primary" type="submit">
                                Submit
                            </Button>
                        </HStack>
                    </form>
                );
            },
        },
        {
            id: 'delete',
            label: __('Delete', 'dsubscribers'),
            RenderModal: ({items: [item], closeModal}) => {
                const [email, setEmail] = useState('');
                useEffect(() => {
                    setEmail(item.email)
                }, [item])

                const onSubmit = (event) => {
                    event.preventDefault();

                    apiFetch({
                        path: `/dsubscribers/v1/subscriber/${email}`,
                        method: 'DELETE',
                    }).then(() => {
                        apiFetch({path: '/dsubscribers/v1/subscribers'})
                            .then((data) => {
                                setData(data)
                                closeModal()
                            });
                    })
                }

                return (
                    <form onSubmit={onSubmit}>
                        <HStack>
                            <Text style={{marginBottom: '20px'}}>Are you sure you want to delete
                                this <strong>{email}</strong>?</Text>
                        </HStack>
                        <HStack>
                            <Button variant="primary" type="submit">Confirm</Button>
                        </HStack>
                    </form>
                );
            }
        },
    ]

    return (
        <>
            <CardFooter>
                <Heading>{__('Subscribers', 'dsubscribers')}</Heading>
                <Button
                    variant="secondary"
                    onClick={() => location.href = dsubscribersApiSettings?.export_url}
                >
                    {__('Export .csv', 'dsubscribers')}
                </Button>
            </CardFooter>
            <DataViews
                data={processedData}
                fields={fields}
                view={view}
                onChangeView={setView}
                defaultLayouts={defaultLayouts}
                paginationInfo={paginationInfo}
                actions={actions}
            />
        </>
    )
}
