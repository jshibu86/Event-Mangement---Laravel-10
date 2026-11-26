<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogViewercontroller extends Controller
{
    /** Read at most the last 1 MB of the log file, so big logs stay fast. */
    // Change Name
    private const READ_BYTES = 1048576;

    private const LEVELS = [
        'EMERGENCY', 'ALERT', 'CRITICAL', 'ERROR', 'WARNING', 'NOTICE', 'INFO', 'DEBUG',
    ];

    public function index(Request $request)
    {
        $limit = (int) $request->input('lines', 100);
        $limit = in_array($limit, [50, 100, 200, 500], true) ? $limit : 100;

        $level = strtoupper((string) $request->input('level', ''));
        $level = in_array($level, self::LEVELS, true) ? $level : '';

        $search = trim((string) $request->input('q', ''));

        $file    = $this->latestLogFile();
        $entries = [];
        $total   = 0;
        $size    = 0;

        if ($file) {
            $size    = (int) filesize($file);
            $entries = array_reverse($this->parse($this->readTail($file))); // newest first

            if ($level !== '') {
                $entries = array_values(array_filter($entries, fn ($e) => $e['level'] === $level));
            }
            if ($search !== '') {
                $entries = array_values(array_filter(
                    $entries,
                    fn ($e) => stripos($e['message'] . ' ' . $e['stack'], $search) !== false
                ));
            }

            $total   = count($entries);
            $entries = array_slice($entries, 0, $limit);
        }

        return view('logs.index', [
            'file'    => $file,
            'size'    => $size,
            'entries' => $entries,
            'total'   => $total,
            'limit'   => $limit,
            'level'   => $level,
            'search'  => $search,
            'levels'  => self::LEVELS,
        ]);
    }

    /** Empty the current log file. The file itself stays, so its owner and permissions are kept. */
    public function clear(Request $request)
    {
        $file = $this->latestLogFile();

        if (!$file) {
            return redirect()->route('logs.index')->with('success', 'There is no log file to clear.');
        }

        if (@file_put_contents($file, '', LOCK_EX) === false) {
            return redirect()->route('logs.index')
                ->with('error', 'Could not clear the log. Check that storage/logs is writable.');
        }

        // Leave a trace of who cleared it
        Log::info('Log file ' . basename($file) . ' was cleared by ' . ($request->user()->email ?? 'unknown user'));

        return redirect()->route('logs.index')->with('success', basename($file) . ' cleared.');
    }

    /** Newest laravel*.log file (works with both the single and daily log channels). */
    private function latestLogFile(): ?string
    {
        $files = glob(storage_path('logs/laravel*.log')) ?: [];
        if (!$files) {
            return null;
        }
        usort($files, fn ($a, $b) => filemtime($b) <=> filemtime($a));

        return $files[0];
    }

    private function readTail(string $file): string
    {
        $handle = @fopen($file, 'rb');
        if (!$handle) {
            return '';
        }

        $size  = (int) filesize($file);
        $start = max(0, $size - self::READ_BYTES);
        if ($start > 0) {
            fseek($handle, $start);
        }

        $raw = stream_get_contents($handle);
        fclose($handle);

        return $raw === false ? '' : mb_scrub($raw);
    }

    /** Split the raw text into entries: date, env, level, message, stack trace. */
    private function parse(string $raw): array
    {
        $chunks = preg_split(
            '/(?=^\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\] )/m',
            $raw,
            -1,
            PREG_SPLIT_NO_EMPTY
        );

        $entries = [];
        foreach ($chunks as $chunk) {
            // The first chunk may start in the middle of an entry; it won't match and is skipped.
            if (!preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] (\w+)\.(\w+): (.*)$/s', rtrim($chunk), $m)) {
                continue;
            }

            $parts = explode("\n", $m[4], 2);
            $level = strtoupper($m[3]);

            $entries[] = [
                'date'    => $m[1],
                'env'     => $m[2],
                'level'   => $level,
                'color'   => $this->color($level),
                'message' => mb_substr(trim($parts[0]), 0, 1000),
                'stack'   => trim($parts[1] ?? ''),
            ];
        }

        return $entries;
    }

    private function color(string $level): string
    {
        return match ($level) {
            'EMERGENCY', 'ALERT', 'CRITICAL', 'ERROR' => 'danger',
            'WARNING'                                  => 'warning',
            'NOTICE', 'INFO'                           => 'info',
            default                                    => 'secondary',
        };
    }
}