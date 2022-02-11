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

function get_matrix() {
    $result = [];

    foreach (get_branches() as $branch) {
        foreach (ARCHES as $arch) {
            foreach ([true, false] as $debug) {
                foreach ([true, false] as $zts) {
                    $branch_key = strtoupper(str_replace('.', '', $branch));
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
    }

    return ['include' => $result];
}

echo '::set-output name=matrix::' . json_encode(get_matrix(), JSON_UNESCAPED_SLASHES) . "\n";
