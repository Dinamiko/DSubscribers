import {useState, useMemo} from '@wordpress/element';
import {DataViews} from "@wordpress/dataviews/wp";
import {filterSortAndPaginate} from '@wordpress/dataviews';

const data = [
    {
        id: 1,
        title: 'Foo'
    },
    {
        id: 2,
        title: 'Bar'
    },
]

const fields = [
    {
        id: 'id',
        label: 'ID',
    },
    {
        id: 'title',
        label: 'Title',
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
            'title',
        ],
    })

    const {data: processedData, paginationInfo} = useMemo(() => {
        return filterSortAndPaginate(data, view, fields);
    }, [view]);

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
