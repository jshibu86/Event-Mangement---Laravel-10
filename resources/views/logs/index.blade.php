@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
    <div>
        <h2 class="mb-1">Application logs</h2>
        <div class="text-muted small">
            @if($file)
                {{ basename($file) }} &middot; {{ number_format($size / 1024, 1) }} KB &middot;
                showing {{ count($entries) }} of {{ $total }} matching entries (newest first)
            @else
                No log file found in storage/logs.
            @endif
        </div>
    </div>
    <div class="d-flex align-items-center gap-3">
        <div class="form-check form-switch mb-0">
            <input class="form-check-input" type="checkbox" id="auto-refresh">
            <label class="form-check-label" for="auto-refresh">Auto-refresh (10s)</label>
        </div>
        @if($file)
            <form method="post" action="{{ route('logs.clear') }}"
                  onsubmit="return confirm('Clear {{ basename($file) }}? This permanently deletes every entry in the file.');">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-danger">Clear log</button>
            </form>
        @endif
    </div>
</div>

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<form method="get" action="{{ route('logs.index') }}" class="card p-3 mb-3">
    <div class="row g-2 align-items-end">
        <div class="col-md-4">
            <label class="form-label mb-1">Search</label>
            <input class="form-control" name="q" value="{{ $search }}" placeholder="Text in message or stack trace">
        </div>
        <div class="col-md-3">
            <label class="form-label mb-1">Level</label>
            <select class="form-select" name="level">
                <option value="">All levels</option>
                @foreach($levels as $l)
                    <option value="{{ $l }}" @selected($level === $l)>{{ $l }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label mb-1">Entries</label>
            <select class="form-select" name="lines">
                @foreach([50, 100, 200, 500] as $n)
                    <option value="{{ $n }}" @selected($limit === $n)>{{ $n }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button class="btn btn-primary">Apply</button>
            <a class="btn btn-outline-secondary" href="{{ route('logs.index') }}">Reset</a>
        </div>
    </div>
</form>

@forelse($entries as $e)
    <div class="card p-3 mb-2">
        <div class="d-flex gap-2 align-items-center flex-wrap">
            <span class="badge bg-{{ $e['color'] }}">{{ $e['level'] }}</span>
            <small class="text-muted">{{ $e['date'] }} &middot; {{ $e['env'] }}</small>
        </div>
        <div class="mt-2" style="word-break:break-word">{{ $e['message'] }}</div>
        @if($e['stack'] !== '')
            <details class="mt-2">
                <summary class="small">Stack trace</summary>
                <pre class="mt-2 small mb-0" style="white-space:pre-wrap;word-break:break-word;max-height:360px;overflow:auto">{{ $e['stack'] }}</pre>
            </details>
        @endif
    </div>
@empty
    <div class="card p-4 text-center text-muted">No log entries match.</div>
@endforelse

<script>
(function () {
    var box = document.getElementById('auto-refresh'), key = 'festiva-log-auto', timer = null;
    function start() { timer = setTimeout(function () { location.reload(); }, 10000); }
    try { if (localStorage.getItem(key) === '1') { box.checked = true; start(); } } catch (e) {}
    box.addEventListener('change', function () {
        try { localStorage.setItem(key, box.checked ? '1' : '0'); } catch (e) {}
        clearTimeout(timer);
        if (box.checked) { start(); }
    });
})();
</script>
@endsection