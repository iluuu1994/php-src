<?php

const BRANCHES = ['master', 'PHP-8.1', 'PHP-8.0', 'PHP-7.4'];
const ARCHES = ['linux-x64', /*'linux-i386',*/ 'macos'];

function get_branches() {
    // FIXME: Check if branch has changed in the last 24 hours
    // return ['master', 'PHP-8.1', 'PHP-8.0', 'PHP-7.4'];
    return ['master'];

    return array_filter(BRANCHES, function ($branch) {
        return shell_exec('git rev-list  --after="24 hours" origin/' . $branch) !== null;
    });
}

function get_test_matrix($branches) {
    $result = [];

    foreach ($branches as $branch) {
        $branch_key = strtoupper(str_replace('.', '', $branch));

        foreach (ARCHES as $arch) {
            foreach ([true, false] as $debug) {
                foreach ([true, false] as $zts) {
                    $arch_key = strtoupper($arch);
                    $debug_key = $debug ? 'DEBUG' : 'RELEASE';
                    $zts_key = $zts ? 'ZTS' : 'NTS';
                    $name = "{$branch_key}_{$arch_key}_{$debug_key}_{$zts_key}";

                    $debug_parameter = $debug ? '--enable-debug' : '--disable-debug';
                    $zts_parameter = $debug ? '--enable-zts' : '--disable-zts';
                    $configuration_parameters = "$debug_parameter $zts_parameter";

                    $result[] = [
                        'name' => $name,
                        'branch' => $branch,
                        'arch' => $arch,
                        'configurationParameters' => $configuration_parameters,
                    ];
                }
            }
        }

        $result[] = [
            'name' => $branch_key . '_LINUX_X64_DEBUG_ZTS_ASAN_UBSAN',
            'branch' => $branch,
            'arch' => 'linux-x64',
            'configurationParameters' => '--enable-debug --enable-zts --enable-address-sanitizer --enable-undefined-sanitizer',
            'runTestsParameters' => '--asan',
        ];

        $result[] = [
            'name' => $branch_key . '_LINUX_X64_DEBUG_NTS_REPEAT',
            'branch' => $branch,
            'arch' => 'linux-x64',
            'configurationParameters' => '--enable-debug --disable-zts',
            'runTestsParameters' => '--repeat 2',
        ];

        $result[] = [
            'name' => $branch_key . '_LINUX_X64_VARIATION_DEBUG_ZTS',
            'branch' => $branch,
            'arch' => 'linux-x64',
            'configurationParameters' => '--enable-debug --enable-zts CFLAGS="-DZEND_RC_DEBUG=1 -DPROFITABILITY_CHECKS=0 -DZEND_VERIFY_FUNC_INFO=1"',
        ];
    }

    return ['include' => $result];
}

function get_branch_matrix($branches) {
    $result = array_map(function ($branch) {
        $branch_key = strtoupper(str_replace('.', '', $branch));
        return [
            'name' => $branch_key,
            'ref' => $branch,
        ];
    }, $branches);

    return ['branch' => $result];
}

$branches = get_branches();
echo '::set-output name=branch_matrix::' . json_encode(get_branch_matrix($branches), JSON_UNESCAPED_SLASHES) . "\n";
echo '::set-output name=test_matrix::' . json_encode(get_test_matrix($branches), JSON_UNESCAPED_SLASHES) . "\n";
