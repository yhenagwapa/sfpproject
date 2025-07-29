<style>
    .header {
        font-family: "Arial", sans-serif;
        text-align: center;
        font-size: 12px;
        margin: 0;
    }

    .header-section .footer-section {
        width: 100%;
        margin: 0 auto;
    }

    .table {
        width: 100%;
        font-family: "Arial", sans-serif;
        font-size: 10px;
        border: 5px;
    }

    .table td {
        padding: 5px;
        vertical-align: top;
    }

    .table-header {
        font-size: 12px;
        background-color: #8bbbdb;
    }

    .border-bg {
        background-color: #8bbbdb;
    }

    .border-bg-subhead {
        background-color: #edc5c9;
    }

    .nutritional-status-table {
        font-size: 10px;
        font-family: "Arial", sans-serif;
        text-align: center;
        border-collapse: collapse;
    }

    .nutritional-status-table td:nth-child(2),
    .nutritional-status-table td:nth-child(3) {
        text-align: left;
    }

    .nutritional-status-table .centered {
        text-align: center;
    }

    .nutritional-status-table th,
    .nutritional-status-table td {
        border: 1px solid rgba(0, 0, 0, 0.5);
        text-transform: uppercase;
    }

    .footer-table {
        width: 100%;
        font-family: "Arial", sans-serif;
        font-size: 12px;
        border: 5px;
    }

    .footer-table p {
        margin: 0;
        text-transform: uppercase;
    }

    .footer-table td {
        padding: 10px;
        vertical-align: top;
    }

    .no-wrap {
        white-space: nowrap;
    }

    @page {
        margin-top: 20px;
        margin-bottom: 50px;
        margin-right: 30px;
        margin-left: 30px;
    }

    .footer {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        height: 50px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 12px;
    }

    tr, td, th {
        page-break-inside: avoid;
    }
</style>
