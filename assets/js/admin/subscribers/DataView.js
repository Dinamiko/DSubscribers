import {useState, useMemo, useEffect} from '@wordpress/element';
import {DataViews} from "@wordpress/dataviews/wp";
import {filterSortAndPaginate} from '@wordpress/dataviews';
import "../../../css/admin/subscribers/subscribers.scss";

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
        fetch('http://localhost:8888/wp-json/dsubscribers/v1/subscribers', {
            headers: {
                'X-WP-Nonce': dsubscribersApiSettings.nonce
            }
        }).then(response => response.json())
            .then((data) => {
                setData(data)
            })
    }, []);

    const {data: processedData, paginationInfo} = useMemo(() => {
        return filterSortAndPaginate(data, view, fields);
    }, [view, data]);

    return (
        <DataViews
            data={processedData}
            fields={fields}
            view={view}
            onChangeView={setView}
            defaultLayouts={defaultLayouts}
            paginationInfo={paginationInfo}
        />
    )
}
