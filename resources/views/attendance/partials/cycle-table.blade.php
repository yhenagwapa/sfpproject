<table id="cycle-attendance-table" class="table mt-3">
    <thead>
        <tr>
            <th scope="col">Feeding No.</th>
            <th scope="col">Date</th>
        </tr>
    </thead>
    <tbody id="cycle-attendance-table">
        @foreach ($cycleAttendances as $cycleAttendance)
            <tr>
                <td>{{ $cycleAttendance->attendance_no }}</td>
                <td>{{ $cycleAttendance->attendance_date }}</td>
            </tr>
        @endforeach
    </tbody>
</table>