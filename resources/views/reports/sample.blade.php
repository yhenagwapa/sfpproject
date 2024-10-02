<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Tahoma, sans-serif';
            font-size: 12px;
        }
        .header {
            margin-top: 0px;
            text-align: center;
        }
        .section {
            width: 100%;
            margin: 0 auto;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table td {
            padding: 10px;
            vertical-align: top;
        }

        .table td:first-child {
            width: 70%;
        }

        

        p {
            margin: 0px ;
        }

    </style>
</head>
<body>
    <div class="header">
        <p>Department of Social Welfare and Development, Field Office XI</p>
        <p>Supplementary Feeding Program</p>
        <p>{{ $cycleImplementation }} SY {{ $cycleImplementation->cycle_school_year }}</p>
        <p><b>MASTERLIST OF BENEFICIARIES</b></p>
        <br>
    </div>
    <div class="section">
        <table class="table">
            <tr>
                <td>
                    <p>Province: _____________________________</p>
                    <p>Child Development Center: _____________________________</p>
                </td>
                <td>
                    <p>City / Municipality: _____________________________</p>
                    <p>Barangay: _____________________________</p>
                </td>
            </tr>
        </table>
    </div>
    <div style="overflow-x: auto; max-width: 100%;">
        @include('reports.partials.funded-table', [
            'isFunded' => $isFunded,
        ])
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
