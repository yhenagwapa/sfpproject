@php
    session()->reflash();
@endphp
<form id="redirectForm" action="{{ route('nutritionalstatus.index') }}" method="POST">
    @csrf
    <input type="hidden" name="child_id" value="{{ session('child_id') }}">
</form>

<script>
    document.getElementById('redirectForm').submit(); // Auto-submit the form
</script>
