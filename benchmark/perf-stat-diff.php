#!/usr/bin/env php
<?php

// Useful setup for more stable local measurements:
// echo performance | sudo tee /sys/devices/system/cpu/cpu*/cpufreq/scaling_governor
// echo 0 | sudo tee /sys/devices/system/cpu/cpufreq/boost
// echo off | sudo tee /sys/devices/system/cpu/smt/control
// echo 0 | sudo tee /proc/sys/kernel/randomize_va_space
// echo -1 | sudo tee /proc/sys/kernel/perf_event_paranoid
// echo 0 | sudo tee /proc/sys/kernel/nmi_watchdog

const DEFAULT_RUNS = 10;
const DEFAULT_WARMUP_RUNS = 2;
const DEFAULT_CPU = '7';
const DEFAULT_WINSOR_PERCENT = 25.0;

const METRICS = [
    'wall_time' => 'wall time',
    'L1-dcache-load-misses' => 'L1 misses',
    'cache-misses' => 'LL misses',
    'de_no_dispatch_per_slot.no_ops_from_frontend' => 'frontend stalls',
    'de_no_dispatch_per_slot.backend_stalls' => 'backend stalls',
    'branch-misses' => 'branch misses',
];

function fail(string $message, int $statusCode = 1): never {
    fwrite(STDERR, $message . "\n");
    exit($statusCode);
}

function usage(int $statusCode = 2): never {
    fail(
        "Usage: ./perf-stat-diff.php [options] <base command> [more commands]\n" .
        "\n" .
        "Options:\n" .
        "  --runs=N       Measured runs per command after warmup (default: " . DEFAULT_RUNS . ")\n" .
        "  --warmup=N     Warmup runs per command to discard (default: " . DEFAULT_WARMUP_RUNS . ")\n" .
        "  --cpu=LIST     Pin commands with taskset -c LIST (default: " . DEFAULT_CPU . ")\n" .
        "  --winsor=PCT   Winsorize PCT from each tail before averaging (default: " . DEFAULT_WINSOR_PERCENT . ")\n" .
        "  --aslr         Run measured commands with ASLR enabled\n" .
        "  --control      Use perf FIFO control, compatible with PERF_CTL_FIFO/PERF_CTL_ACK_FIFO\n" .
        "  --help         Show this help",
        $statusCode,
    );
}

function parse_positive_int(string $name, string $value, bool $allowZero = false): int {
    if (!ctype_digit($value)) {
        fail("$name must be an integer" . ($allowZero ? ' >= 0' : ' > 0'));
    }

    $value = (int) $value;
    if ($allowZero ? $value < 0 : $value <= 0) {
        fail("$name must be an integer" . ($allowZero ? ' >= 0' : ' > 0'));
    }

    return $value;
}

function parse_percent(string $name, string $value): float {
    if (!is_numeric($value)) {
        fail("$name must be a percentage >= 0 and < 50");
    }

    $value = (float) $value;
    if ($value < 0.0 || $value >= 50.0) {
        fail("$name must be a percentage >= 0 and < 50");
    }

    return $value;
}

function parse_options(array $argv): array {
    array_shift($argv);

    $options = [
        'runs' => DEFAULT_RUNS,
        'warmup' => DEFAULT_WARMUP_RUNS,
        'cpu' => DEFAULT_CPU,
        'winsor' => DEFAULT_WINSOR_PERCENT,
        'aslr' => false,
        'control' => false,
    ];
    $commands = [];

    foreach ($argv as $arg) {
        if ($arg === '--help' || $arg === '-h') {
            usage(0);
        }
        if (preg_match('(^--runs=(?<runs>.*)$)', $arg, $matches)) {
            $options['runs'] = parse_positive_int('--runs', $matches['runs']);
            continue;
        }
        if (preg_match('(^--warmup=(?<warmup>.*)$)', $arg, $matches)) {
            $options['warmup'] = parse_positive_int('--warmup', $matches['warmup'], true);
            continue;
        }
        if (preg_match('(^--cpu=(?<cpu>.*)$)', $arg, $matches)) {
            $options['cpu'] = $matches['cpu'];
            continue;
        }
        if (preg_match('(^--winsor=(?<winsor>.*)$)', $arg, $matches)) {
            $options['winsor'] = parse_percent('--winsor', $matches['winsor']);
            continue;
        }
        if ($arg === '--aslr') {
            $options['aslr'] = true;
            continue;
        }
        if ($arg === '--control') {
            $options['control'] = true;
            continue;
        }
        if (str_starts_with($arg, '--')) {
            fail("Unknown option: $arg");
        }

        $commands[] = $arg;
    }

    if (count($commands) === 0) {
        usage();
    }

    return [$options, $commands];
}

function mean(array $values): float {
    return array_sum($values) / count($values);
}

function percentile(array $values, float $percentile): float {
    $count = count($values);
    $index = ($count - 1) * $percentile;
    $lower = (int) floor($index);
    $upper = (int) ceil($index);

    if ($lower === $upper) {
        return $values[$lower];
    }

    $weight = $index - $lower;
    return $values[$lower] * (1.0 - $weight) + $values[$upper] * $weight;
}

function iqr(array $values): float {
    $q1 = percentile($values, 0.25);
    $q3 = percentile($values, 0.75);

    return $q3 - $q1;
}

function winsorized_mean(array $values, float $percent): float {
    $count = count($values);
    $tailCount = (int) floor($count * $percent / 100.0);
    if ($tailCount !== 0) {
        $low = $values[$tailCount];
        $high = $values[$count - $tailCount - 1];
        for ($i = 0; $i < $tailCount; $i++) {
            $values[$i] = $low;
            $values[$count - $i - 1] = $high;
        }
    }

    return mean($values);
}

function format_value(float $value): string {
    $metricPrefixes = [
        [1e12, 'T'],
        [1e9, 'G'],
        [1e6, 'M'],
        [1e3, 'k'],
        [1, ''],
        [1e-3, 'm'],
        [1e-6, 'u'],
        [1e-9, 'n'],
        [1e-12, 'p'],
    ];

    $absValue = abs($value);
    foreach ($metricPrefixes as $i => [$factor, $prefix]) {
        if ($absValue >= $factor || $i === count($metricPrefixes) - 1) {
            return number_format($value / $factor, 3) . ' ' . $prefix;
        }
    }
}

function format_duration(float $seconds): string {
    $absSeconds = abs($seconds);

    if ($absSeconds >= 1.0) {
        return number_format($seconds, 6) . ' s';
    }
    if ($absSeconds >= 1e-3) {
        return number_format($seconds * 1e3, 3) . ' ms';
    }
    if ($absSeconds >= 1e-6) {
        return number_format($seconds * 1e6, 3) . ' us';
    }
    return number_format($seconds * 1e9, 3) . ' ns';
}

function format_metric_value(string $name, float $value): string {
    if ($name === 'wall_time') {
        return format_duration($value);
    }

    return format_value($value);
}

function format_percentage(float $value, bool $signed = true): string {
    return ($signed && $value >= 0.0 ? '+' : '') . number_format($value, 3, '.', '') . '%';
}

function is_output_terminal(): bool {
    return function_exists('stream_isatty') && stream_isatty(STDOUT);
}

function use_ansi_colors(): bool {
    static $useColors = null;
    if ($useColors !== null) {
        return $useColors;
    }

    $term = getenv('TERM');
    $useColors = is_output_terminal()
        && getenv('NO_COLOR') === false
        && $term !== false
        && $term !== 'dumb';

    return $useColors;
}

function colorize(string $value, string $code): string {
    if (!use_ansi_colors()) {
        return $value;
    }

    return "\033[{$code}m{$value}\033[0m";
}

function print_temp(string $message): void {
    if (!is_output_terminal()) {
        return;
    }

    static $lineLength = 0;
    if ($lineLength !== 0) {
        echo use_ansi_colors() ? "\033[2K\r" : str_repeat(' ', $lineLength) . "\r";
    }
    echo $message, "\r";
    flush();
    $lineLength = strlen($message);
}

function print_progress(int $max, int $current): void {
    $length = 30;
    $progress = (int) floor($length / $max * $current);
    $filledLength = min($length, $progress);
    $emptyLength = max(0, $length - $filledLength);

    $bar = str_repeat('█', $filledLength);
    $bar .= str_repeat('░', $emptyLength);

    print_temp("  $bar $current/$max");
}

function color_for_relative_delta(float $relative): string {
    if (abs($relative) < 0.1) {
        return '';
    }
    if ($relative < -0.5) {
        return '32;1';
    }
    if ($relative > 0.5) {
        return '31;1';
    }
    return $relative < 0 ? '36;1' : '33;1';
}

function normalize_perf_event(string $event): string {
    return preg_replace('(:u$)', '', trim($event));
}

function parse_counter_value(string $value, string $event): float {
    $value = trim($value);
    if ($value === '<not counted>' || $value === '<not supported>') {
        fail("perf event is not available: $event");
    }

    $value = str_replace([',', "'"], '', $value);
    if (!is_numeric($value)) {
        fail("Could not parse perf value for $event: $value");
    }

    return (float) $value;
}

function parse_perf_running_percent(array $parts, string $event): void {
    if (!isset($parts[4]) || $parts[4] === '') {
        return;
    }

    $runningPercent = rtrim(trim($parts[4]), '%');
    if (!is_numeric($runningPercent)) {
        return;
    }

    if ((float) $runningPercent < 99.999) {
        fail("perf event was multiplexed: $event ran for $runningPercent% of enabled time");
    }
}

function parse_perf_stat(string $stderr, array $expectedEvents): array {
    $counters = [];

    foreach (explode("\n", $stderr) as $line) {
        $line = trim($line);
        if ($line === '') {
            continue;
        }

        $parts = explode(';', $line);
        if (count($parts) < 3) {
            continue;
        }

        $event = normalize_perf_event($parts[2]);
        if (!in_array($event, $expectedEvents, true)) {
            continue;
        }

        parse_perf_running_percent($parts, $event);
        $counters[$event] = ($counters[$event] ?? 0.0) + parse_counter_value($parts[0], $event);
    }

    foreach ($expectedEvents as $event) {
        if (!array_key_exists($event, $counters)) {
            fail("perf output did not contain event: $event");
        }
    }

    return $counters;
}

function parse_elapsed_time(string $stderr): ?float {
    if (!preg_match('(Elapsed time:\s+(?<seconds>[0-9]+(\.[0-9]+)?)\s+sec)', $stderr, $matches)) {
        return null;
    }
    return (float) $matches['seconds'];
}

function parse_duration_time(string $stderr): ?float {
    foreach (explode("\n", $stderr) as $line) {
        $line = trim($line);
        if ($line === '') {
            continue;
        }

        $parts = explode(';', $line);
        if (count($parts) < 3) {
            continue;
        }

        if (normalize_perf_event($parts[2]) !== 'duration_time') {
            continue;
        }

        return parse_counter_value($parts[0], 'duration_time') / 1e9;
    }

    return null;
}

function perf_events(): array {
    return array_values(array_diff(array_keys(METRICS), ['wall_time']));
}

function run_shell_command(string $cmd, array $env): string {
    $pipes = null;
    $descriptorSpec = [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
    $processHandle = proc_open($cmd, $descriptorSpec, $pipes, getcwd(), $env);
    if (!$processHandle) {
        fail("Could not start command: $cmd");
    }

    fclose($pipes[0]);
    stream_set_blocking($pipes[1], false);
    stream_set_blocking($pipes[2], false);

    $stderrBuffer = '';

    do {
        $read = [];
        if (!feof($pipes[1])) {
            $read[] = $pipes[1];
        }
        if (!feof($pipes[2])) {
            $read[] = $pipes[2];
        }

        if ($read !== []) {
            $write = null;
            $except = null;
            $ready = stream_select($read, $write, $except, 1, 0);
            if ($ready !== false && $ready > 0) {
                foreach ($read as $stream) {
                    $chunk = fread($stream, 8192);
                    if ($chunk === false || $chunk === '') {
                        continue;
                    }
                    if ($stream === $pipes[2]) {
                        $stderrBuffer .= $chunk;
                    }
                }
            }
        }
    } while (!feof($pipes[1]) || !feof($pipes[2]));

    fclose($pipes[1]);
    fclose($pipes[2]);
    $statusCode = proc_close($processHandle);

    if ($statusCode !== 0) {
        fwrite(STDERR, $stderrBuffer);
        fwrite(STDERR, 'Exited with status code ' . $statusCode . "\n");
        exit($statusCode);
    }

    return $stderrBuffer;
}

function build_perf_command(string $cmd, array $options, array $events): string {
    $eventSpec = implode(',', $events);
    if (count($events) > 1) {
        $eventSpec = '{' . $eventSpec . '}';
    }

    $perf = 'perf stat -x ' . escapeshellarg(';') . ' -e ' . escapeshellarg($eventSpec) . ' -e duration_time';
    if ($options['control']) {
        $perf .= ' -D -1 --control fifo:/tmp/perfctl,/tmp/perfack';
    }

    $perf .= ' -- ' . $cmd;

    if (!$options['aslr']) {
        $perf = 'setarch -R ' . $perf;
    }

    if ($options['cpu'] !== '') {
        $perf = 'taskset -c ' . escapeshellarg($options['cpu']) . ' ' . $perf;
    }

    return $perf;
}

function run_perf_stat(string $cmd, array $options): array {
    $env = array_merge(getenv(), [
        'PHP_INI_SCAN_DIR' => '',
        'PERF_CTL_FIFO' => '/tmp/perfctl',
        'PERF_CTL_ACK_FIFO' => '/tmp/perfack',
    ]);

    $events = perf_events();
    $stderr = run_shell_command(build_perf_command($cmd, $options, $events), $env);
    $elapsedTime = parse_elapsed_time($stderr) ?? parse_duration_time($stderr);

    if ($elapsedTime === null) {
        fail("perf output did not contain duration_time: $cmd");
    }

    return ['wall_time' => $elapsedTime] + parse_perf_stat($stderr, $events);
}

function aggregate_runs(array $runs, float $winsorPercent): array {
    $results = [];
    foreach (METRICS as $name => $_label) {
        $values = array_column($runs, $name);
        sort($values, SORT_NUMERIC);
        $iqr = iqr($values);
        $mean = winsorized_mean($values, $winsorPercent);
        $results[$name] = [
            'mean' => $mean,
            'iqr' => $iqr,
        ];
    }
    return $results;
}

function print_command_result(
    string $cmd,
    array $result,
    float $winsorPercent,
    ?string $baseCmd = null,
    ?array $base = null,
): void {
    echo colorize('$', '2'), ' ', colorize($cmd, '1');
    if ($baseCmd !== null) {
        echo colorize(' vs ', '2'), colorize($baseCmd, '1');
    }
    echo colorize(" (winsor mean, {$winsorPercent}% tails, iqr)", '2'), "\n";

    foreach (METRICS as $name => $label) {
        $mean = $result[$name]['mean'];
        $iqr = $result[$name]['iqr'];
        $value = str_pad(format_metric_value($name, $mean), 12, ' ', STR_PAD_LEFT);
        $iqrValue = '(' . format_percentage($mean != 0.0 ? 100.0 / $mean * $iqr : 0.0, signed: false) . ')';

        echo '  ', colorize(str_pad($label, 16), '36'), colorize(' = ', '2'), colorize($value, '1'), ' ', colorize($iqrValue, '2');
        if ($base !== null) {
            $baseMean = $base[$name]['mean'];
            $delta = $mean - $baseMean;
            $relative = $baseMean != 0.0 ? (($mean / $baseMean) - 1.0) * 100.0 : 0.0;
            $value = str_pad(format_metric_value($name, $delta) . ' (' . format_percentage($relative) . ')', 22, ' ', STR_PAD_LEFT);
            $color = color_for_relative_delta($relative);
            echo $color !== '' ? colorize($value, $color) : $value;
        }
        echo "\n";
    }
}

function main(array $argv): void {
    [$options, $commands] = parse_options($argv);

    $total = ($options['warmup'] + $options['runs']) * count($commands);
    $current = 0;
    $runGroups = [];
    $results = [];

    for ($run = 0; $run < $options['warmup']; $run++) {
        for ($i = 0; $i < count($commands); $i++) {
            print_progress($total, $current++);
            run_perf_stat($commands[$i], $options);
        }
    }

    for ($run = 0; $run < $options['runs']; $run++) {
        for ($i = 0; $i < count($commands); $i++) {
            print_progress($total, $current++);
            $runGroups[$i][] = run_perf_stat($commands[$i], $options);
        }
    }

    print_temp('');
    foreach ($commands as $i => $_cmd) {
        $results[$i] = aggregate_runs($runGroups[$i], $options['winsor']);
    }

    foreach ($results as $i => $result) {
        print_command_result(
            $commands[$i],
            $result,
            $options['winsor'],
            $i === 0 ? null : $commands[0],
            $i === 0 ? null : $results[0],
        );
        if ($i !== count($results) - 1) {
            echo "\n";
        }
    }
}

main($argv);
