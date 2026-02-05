<?php

const BRANCHES = [
    ['ref' => 'master', 'version' => [8, 6]],
    ['ref' => 'PHP-8.5', 'version' => [8, 5]],
    ['ref' => 'PHP-8.4', 'version' => [8, 4]],
    ['ref' => 'PHP-8.3', 'version' => [8, 3]],
    ['ref' => 'PHP-8.2', 'version' => [8, 2]],
];

function get_branch_commit_cache_file_path(): string {
    return dirname(__DIR__) . '/branch-commit-cache.json';
}

function get_branches() {
    $branch_commit_cache_file = get_branch_commit_cache_file_path();
    $branch_commit_map = [];
    if (file_exists($branch_commit_cache_file)) {
        $branch_commit_map = json_decode(file_get_contents($branch_commit_cache_file), JSON_THROW_ON_ERROR);
    }

    $changed_branches = [];
    foreach (BRANCHES as $branch) {
        $previous_commit_hash = $branch_commit_map[$branch['ref']] ?? null;
        $current_commit_hash = trim(shell_exec('git rev-parse origin/' . $branch['ref']));

        if ($previous_commit_hash !== $current_commit_hash) {
            $changed_branches[] = $branch;
        }

        $branch_commit_map[$branch['ref']] = $current_commit_hash;
    }

    file_put_contents($branch_commit_cache_file, json_encode($branch_commit_map));

    return $changed_branches;
}

function get_current_version(): array {
    $file = dirname(__DIR__) . '/main/php_version.h';
    $content = file_get_contents($file);
    preg_match('(^#define PHP_MAJOR_VERSION (?<num>\d+)$)m', $content, $matches);
    $major = (int) $matches['num'];
    preg_match('(^#define PHP_MINOR_VERSION (?<num>\d+)$)m', $content, $matches);
    $minor = (int) $matches['num'];
    return [$major, $minor];
}

function select_jobs($labels) {
    $labels = array_column($labels, 'name');
    $disable_all = in_array('CI: Disable all', $labels, true);
    $enable_all = in_array('CI: Enable all', $labels, true);
    $test_alpine = in_array('CI: Alpine', $labels, true);
    $test_benchmarking = in_array('CI: Benchmarking', $labels, true);
    $test_community = in_array('CI: Community', $labels, true);
    $test_freebsd = in_array('CI: FreeBSD', $labels, true);
    $test_libmysqlclient = in_array('CI: libmysqlclient', $labels, true);
    $test_linux_ppc64 = in_array('CI: Linux PPC64', $labels, true);
    $test_linux_x32 = in_array('CI: Linux X32', $labels, true);
    $test_linux_x64 = in_array('CI: Linux X64', $labels, true);
    $test_macos = in_array('CI: macOS', $labels, true);
    $test_minimal = in_array('CI: Minimal', $labels, true);
    $test_msan = in_array('CI: MSAN', $labels, true);
    $test_opcache_variation = in_array('CI: Opcache Variation', $labels, true);
    $test_windows = in_array('CI: Windows', $labels, true);

    $jobs = [];
    if ($enable_all || !$disable_all || $test_alpine) {
        $jobs[] = 'ALPINE';
    }
    if ($enable_all || $test_community) {
        $jobs[] = 'COMMUNITY';
    }
    if ($enable_all || $test_libmysqlclient) {
        $jobs[] = 'LIBMYSQLCLIENT';
    }
    if ($enable_all || $test_linux_ppc64) {
        $jobs[] = 'LINUX_PPC64';
    }
    if ($enable_all || !$disable_all || $test_linux_x64) {
        $jobs[] = 'LINUX_X64';
    }
    if ($enable_all || !$disable_all || $test_linux_x32) {
        $jobs[] = 'LINUX_X32';
    }
    if ($enable_all || !$disable_all || $test_macos) {
        $jobs[] = 'MACOS';
    }
    if ($enable_all || $test_msan) {
        $jobs[] = 'MSAN';
    }
    if ($enable_all || $test_opcache_variation) {
        $jobs[] = 'OPCACHE_VARIATION';
    }
    if ($enable_all || !$disable_all || $test_windows) {
        $jobs[] = 'WINDOWS';
    }
    if ($enable_all || !$disable_all || $test_benchmarking) {
        $jobs[] = 'BENCHMARKING';
    }
    if ($enable_all || !$disable_all || $test_freebsd) {
        $jobs[] = 'FREEBSD';
    }
    return $jobs;
}

$trigger = $argv[1] ?? 'schedule';
$attempt = (int) ($argv[2] ?? 1);
$sunday = date('w', time()) === '0';
$discard_cache = $sunday
    || ($trigger === 'schedule' && $attempt !== 1)
    || $trigger === 'workflow_dispatch';
if ($discard_cache) {
    @unlink(get_branch_commit_cache_file_path());
}
$branch = $argv[3] ?? 'master';
$branches = $branch === 'master'
    ? get_branches()
    : [['ref' => $branch, 'version' => get_current_version()]];

$labels = json_decode($argv[4] ?? '[]', true);
$jobs = select_jobs($labels);

echo json_encode($branches, JSON_UNESCAPED_SLASHES), "\n\n";
echo json_encode($jobs, JSON_UNESCAPED_SLASHES), "\n\n";

$f = fopen(getenv('GITHUB_OUTPUT'), 'a');
fwrite($f, 'branches=' . json_encode($branches, JSON_UNESCAPED_SLASHES) . "\n");
fwrite($f, 'jobs=' . json_encode($jobs, JSON_UNESCAPED_SLASHES) . "\n");
fclose($f);
