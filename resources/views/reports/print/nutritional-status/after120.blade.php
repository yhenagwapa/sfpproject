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

        .header-bg{
            background-color: #ABC9B6;
        }

        .subheader-bg {
            background-color: #c9abab;
        }

        .table {
            width: 100%;
            font-family: 'Arial', sans-serif;
            font-size: 10px;
            border: 5px
        }

        .table td {
            padding: 6px;
            vertical-align: top;
        }

        .first {
            width: 20%;
        }

        .p {
            margin: 5px 0;
        }

        .nutritional-status {
            font-size: 9px;
            font-family: 'Arial', sans-serif;
            text-align: center;
            border-collapse: collapse;
            width: 100%;
        }

        .nutritional-status th, .nutritional-status td {
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
            padding: 6px;
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

    @include('reports.print.nutritional-status.after-120.height-for-age')
    <div class="page-break"></div>
    @include('reports.print.nutritional-status.after-120.weight-for-age')
    <div class="page-break"></div>
    @include('reports.print.nutritional-status.after-120.weight-for-height')

</body>
</html>
