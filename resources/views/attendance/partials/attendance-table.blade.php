<table id="attendance-table" class="table datatable mt-3">
    <thead>
        <tr>
            <th scope="col">Feeding No.</th>
            <th scope="col">Date</th>
            <th scope="col">With Milk</th>
        </tr>
    </thead>
    <tbody id="attendance-table">
        @foreach ($attendances as $attendance)
            <tr>
                <td>{{ $attendance->feeding_no }}</td>
                <td>{{ $attendance->feeding_date }}</td>
                <td><input type="checkbox" id="milk{{ $attendance->id }}"
                        value="{{ $attendance->with_milk }}"
                        {{ $attendance->with_milk ? 'checked' : '' }} disabled>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>