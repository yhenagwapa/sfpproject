<table id="milk-attendance-table" class="table mt-3 text-center">
    <thead>
        <tr>
            <th scope="col">Feeding No.</th>
            <th scope="col">Date</th>
        </tr>
    </thead>
    <tbody id="milk-attendance-table">
        @foreach ($milkAttendances as $milkAttendance)
            <tr>
                <td>{{ $milkAttendance->attendance_no }}</td>
                <td>{{ $milkAttendance->attendance_date }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
