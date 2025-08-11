<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel Logs Viewer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .level-badge { text-transform: uppercase; }
        .level-error { background: #dc3545; }
        .level-warning { background: #ffc107; color: #000; }
        .level-info { background: #0dcaf0; color: #000; }
        .level-debug { background: #6c757d; }
        .log-body { white-space: pre-wrap; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace; }
    </style>
    </head>
<body>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 m-0">Laravel Logs Viewer</h1>
        <div class="d-flex gap-2">
            <form method="GET" action="" class="d-flex align-items-center gap-2">
                <select name="file" class="form-select form-select-sm" onchange="this.form.submit()">
                    @foreach($availableFiles as $file)
                        <option value="{{ $file }}" @selected($activeFile && str_ends_with($activeFile, $file))>{{ $file }}</option>
                    @endforeach
                </select>
            </form>
            @if($activeFile)
                <a class="btn btn-sm btn-outline-primary" href="{{ route('logs-viewer.download', ['file' => $activeFile ? basename($activeFile) : null]) }}">Download</a>
                <form method="POST" action="{{ route('logs-viewer.clear', ['file' => $activeFile ? basename($activeFile) : null]) }}" onsubmit="return confirm('Clear current log file?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger">Clear</button>
                </form>
            @endif
        </div>
    </div>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @if(empty($entries))
        <div class="alert alert-info">No log entries.</div>
    @else
        <div class="accordion" id="logsAccordion">
        @foreach($entries as $i => $entry)
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading{{ $i }}">
                    <button class="accordion-button collapsed d-flex align-items-center gap-2" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $i }}" aria-expanded="false" aria-controls="collapse{{ $i }}">
                        <span class="badge level-badge level-{{ $entry['level'] }}">{{ $entry['level'] }}</span>
                        <span class="text-truncate">{{ $entry['header'] }}</span>
                    </button>
                </h2>
                <div id="collapse{{ $i }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $i }}" data-bs-parent="#logsAccordion">
                    <div class="accordion-body">
                        <pre class="log-body mb-0">{{ $entry['body'] }}</pre>
                    </div>
                </div>
            </div>
        @endforeach
        </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


