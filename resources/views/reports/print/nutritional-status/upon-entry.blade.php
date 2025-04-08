<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        .header {
            font-family: 'Arial', sans-serif;
            text-align: center;
            font-size: 12px;
        }

        .header-section .footer-section {
            width: 100%;
            margin: 0 auto;
        }

        .table {
            width: 100%;
            font-family: 'Arial', sans-serif;
            font-size: 10px;
            border: 5px
        }

        .table td {
            padding: 10px;
            vertical-align: top;
        }

        .td-width {
            width: 30px;
        }

        .p {
            margin: 5px 0;
        }

        .undernourished-upon-entry-table {
            font-size: 9px;
            font-family: 'Arial', sans-serif;
            text-align: center;
            border-collapse: collapse;
        }

        .undernourished-upon-entry-table th, .undernourished-upon-entry-table td {
            border: 1px solid rgba(0, 0, 0, 0.5);
            text-transform: uppercase;
        }

        .footer-table {
            width: 100%;
            font-family: 'Arial', sans-serif;
            font-size: 10px;
            border: 5px
        }

        .footer-table p{
            margin: 0;
        }

        .footer-table td {
            padding: 10px;
            vertical-align: top;
        }

        .no-wrap {
            white-space: nowrap;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    {{-- <input type="hidden" id="funded_center_name" name="funded_center_name" value="{{ $center->id}}"> --}}


    @include('reports.print.nutritional-status.upon-entry.height-for-age')
    <div class="page-break"></div>
    @include('reports.print.nutritional-status.upon-entry.weight-for-age')
    <div class="page-break"></div>
    @include('reports.print.nutritional-status.upon-entry.weight-for-height')

</body>
</html>
