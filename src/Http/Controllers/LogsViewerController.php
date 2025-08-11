<?php

namespace Iaa\LaravelLogsViewer\Http\Controllers;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Response;

class LogsViewerController extends Controller
{
    private Filesystem $files;

    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    public function index(Request $request)
    {
        $filePath = $this->resolveLogFilePath($request->get('file'));

        $availableFiles = $this->listLogFiles();
        $content = '';

        if ($filePath && $this->files->exists($filePath)) {
            $content = $this->files->get($filePath);
        }

        // Basic split by entries based on laravel log pattern
        $entries = $this->parseLogContent($content);

        return view('logs-viewer::index', [
            'entries' => $entries,
            'availableFiles' => $availableFiles,
            'activeFile' => $filePath,
        ]);
    }

    public function download(Request $request)
    {
        $filePath = $this->resolveLogFilePath($request->get('file'));
        if (!$filePath || !$this->files->exists($filePath)) {
            abort(404);
        }

        return Response::download($filePath, basename($filePath));
    }

    public function clear(Request $request)
    {
        $filePath = $this->resolveLogFilePath($request->get('file'));
        if (!$filePath || !$this->files->exists($filePath)) {
            abort(404);
        }

        $this->files->put($filePath, '');
        return redirect()->route('logs-viewer.index', ['file' => basename($filePath)])
            ->with('status', 'Log file cleared.');
    }

    private function resolveLogFilePath(?string $file): ?string
    {
        $logsDir = storage_path('logs');
        if (!$this->files->isDirectory($logsDir)) {
            return null;
        }

        if ($file) {
            $candidate = $logsDir.DIRECTORY_SEPARATOR.$file;
            if ($this->files->exists($candidate)) {
                return realpath($candidate) ?: $candidate;
            }
        }

        // default to laravel.log if exists
        $default = $logsDir.DIRECTORY_SEPARATOR.'laravel.log';
        return $this->files->exists($default) ? (realpath($default) ?: $default) : null;
    }

    private function listLogFiles(): array
    {
        $logsDir = storage_path('logs');
        if (!$this->files->isDirectory($logsDir)) {
            return [];
        }

        $files = collect($this->files->files($logsDir))
            ->filter(fn($f) => str_ends_with($f->getFilename(), '.log'))
            ->sortByDesc(fn($f) => $f->getMTime())
            ->map(fn($f) => $f->getFilename())
            ->values()
            ->all();

        return $files;
    }

    private function parseLogContent(string $content): array
    {
        if ($content === '') {
            return [];
        }

        // Split by dates like [YYYY-MM-DD ...]
        $pattern = "/\n(?=\[[0-9]{4}-[0-9]{2}-[0-9]{2}[^\n]*\])";
        $chunks = preg_split($pattern, $content);

        if ($chunks === false) {
            return [[
                'level' => 'log',
                'header' => 'Log',
                'body' => $content,
            ]];
        }

        $entries = [];
        foreach ($chunks as $chunk) {
            $chunk = trim($chunk);
            if ($chunk === '') continue;

            $header = strtok($chunk, "\n");
            $body = trim(substr($chunk, strlen($header)));

            preg_match('/\.(ERROR|WARNING|INFO|DEBUG|CRITICAL|ALERT|NOTICE|EMERGENCY)/i', $header, $m);
            $level = $m[1] ?? 'log';

            $entries[] = [
                'level' => strtolower($level),
                'header' => $header,
                'body' => $body,
            ];
        }

        return $entries;
    }
}


