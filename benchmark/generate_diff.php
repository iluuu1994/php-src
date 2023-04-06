<?php

require_once __DIR__ . '/shared.php';

error_reporting(E_ALL);
chdir(dirname(__DIR__));

function main(?string $headCommitHash, ?string $baseCommitHash) {
    if ($headCommitHash === null || $baseCommitHash === null) {
        fwrite(STDERR, "Usage: php generate_diff.php HEAD_COMMIT_HASH BASE_COMMIT_HASH\n");
        exit(1);
    }

    $repo = __DIR__ . '/repos/data';
    cloneRepo($repo, 'git@github.com:iluuu1994/php-benchmark-data.git');
    $headSummaryFile = $repo . '/' . substr($headCommitHash, 0, 2) . '/' . $headCommitHash . '/summary.json';
    $baseSummaryFile = $repo . '/' . substr($baseCommitHash, 0, 2) . '/' . $baseCommitHash . '/summary.json';
    if (!file_exists($headSummaryFile)) {
        return "Head commit '$headCommitHash' not found\n";
    }
    if (!file_exists($baseSummaryFile)) {
        return "Base commit '$baseCommitHash' not found\n";
    }
    $headSummary  = json_decode(file_get_contents($headSummaryFile), true);
    $baseSummary  = json_decode(file_get_contents($baseSummaryFile), true);

    $headCommitHashShort = substr($headCommitHash, 0, 7);
    $baseCommitHashShort = substr($baseCommitHash, 0, 7);
    $output = "| Benchmark | Base ($baseCommitHashShort) | Head ($headCommitHashShort) | Diff |\n";
    $output .= "|---|---|---|---|\n";
    foreach ($headSummary as $name => $headBenchmark) {
        if (null === $baseBenchmark = $baseSummary[$name] ?? null) {
            continue;
        }
        $instructionDiff = $headBenchmark['instructions'] - $baseSummary[$name]['instructions'];
        $output .= "| $name | "
            . formatInstructions($baseBenchmark['instructions']) . " | "
            . formatInstructions($headBenchmark['instructions']) . " | "
            . formatDiff($instructionDiff, $baseBenchmark['instructions']) . " |\n";
    }
    return $output;
}

function formatInstructions(int $instructions): string {
    if ($instructions > 1e6) {
        return sprintf('%.0fM', $instructions / 1e6);
    } elseif ($instructions > 1e3) {
        return sprintf('%.0fK', $instructions / 1e3);
    } else {
        return (string) $instructions;
    }
}

function formatDiff(int $instructionDiff, int $instructionBase): string {
    return sprintf('%.2f%%', $instructionDiff / $instructionBase * 100);
}

$headCommitHash = $argv[1] ?? null;
$baseCommitHash = $argv[2] ?? null;
$output = main($headCommitHash, $baseCommitHash);
fwrite(STDOUT, $output);
