<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Draivi Backend Task</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>

    /* Basic styling */
    body {
        font-family: "Open Sans", sans-serif;
        font-size: 14px;
    }

    .wrapper {
        max-width: 1300px;
        margin: 0 auto;
        padding: 20px;
    }

    /* Styling for DataTables search box */
    .dataTables_wrapper .dataTables_filter input {
        margin-left: 0.5em;
        display: inline-block;
        width: auto;
    }

    /* Styling for DataTables length menu */
    .dataTables_wrapper .dataTables_length select {
        margin-right: 1em;
        display: inline-block;
        width: auto;
    }

    /* Styling for DataTables pagination buttons */
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        box-sizing: border-box;
        display: inline-block;
        min-width: 1.5em;
        padding: 0.5em 1em;
        margin-left: 2px;
        text-align: center;
        text-decoration: none !important;
        cursor: pointer;
        *cursor: hand;
        color: #333 !important;
        border: 1px solid transparent;
        border-radius: 2px;
    }

    /* Styling for DataTables pagination buttons - disabled */
    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
        cursor: default;
        color: #666 !important;
        border: 1px solid transparent;
        background: transparent;
        box-shadow: none;
    }

    /* Styling for DataTables pagination buttons - active */
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        color: #fff !important;
        border: 1px solid #1e88e5;
        background: #1e88e5;
        box-shadow: none;
    }

    /* Styling for DataTables pagination buttons - hover */
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        color: #fff !important;
        border: 1px solid #1e88e5;
        background: #1e88e5;
        box-shadow: none;
    }

    /* Styling for DataTables info label */
    .dataTables_wrapper .dataTables_info {
        margin-left: 0.5em;
        display: inline-block;
        width: auto;
    }

    /* Styling for DataTables pagination div */
    .dataTables_wrapper .dataTables_paginate {
        margin-right: 0.5em;
        display: inline-block;
        width: auto;
    }
    </style>

</head>
<body>

    <div class="wrapper">
        <table id="priceListTable" class="display">
            <thead>
                <tr>
                    <th>Number</th>
                    <th>Name</th>
                    <th>Bottle Size</th>
                    <th>Price</th>
                    <th>Price GBP</th>
                    <th>Order Amount</th>
                    <th>Updated At</th>
                </tr>
            </thead>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js"></script>

    <script>
        $(document).ready(function() {
            $('#priceListTable').DataTable({
                "processing": true,
                "serverSide": true,
                "pageLength": 25,
                "ajax": "{{ route('price-list.data') }}",
                "columns": [
                    { "data": "number" },
                    { "data": "name" },
                    { "data": "bottle_size" },
                    { "data": "price", render: function (data, type, row) {
                        return "{{ env('PRICE_LIST_CURRENCY_FROM_SYMBOL') }}" + data;
                    } },
                    { "data": "price_gbp", render: function (data, type, row) {
                        return "{{ env('PRICE_LIST_CURRENCY_to_SYMBOL') }}" + data;
                    }  },
                    { "data": "order_amount" },
                    { "data": "updated_at", render: function (data, type, row) {
                        return new Date(data).toLocaleDateString();
                    } }
                ],
                "language": {
                    "emptyTable": "No data found. Please run the fetch command first.",
                }
            });
        });
    </script>
    
</body>
</html>