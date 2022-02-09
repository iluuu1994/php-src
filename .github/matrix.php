<?php

function generate_push() {
    return [
         linux_x64(
            name: 'LINUX_X64_DEBUG_NTS',
            configuration_parameters: '--enable-debug --disable-zts',
            extended_tests: false,
        ),
        linux_x64(
            name: 'LINUX_X64_RELEASE_ZTS',
            configuration_parameters: '--disable-debug --enable-zts',
            extended_tests: false,
        ),
        // apt broken, not sure why
        // linux_i386(
        //     name: 'LINUX_I386_DEBUG_ZTS',
        //     configuration_parameters: '--enable-debug --enable-zts',
        //     extended_tests: false,
        // ),
        macos(
            name: 'MACOS_DEBUG_NTS',
            configuration_parameters: '--enable-debug --disable-zts',
            extended_tests: false,
        ),
    ];

}

function generate_nightly() {
    $result = [
        'name' => 'Nightly',
        //'on' => ['schedule' => [['cron' => '0 1 * * *']]],
        'on' => ['push'],
        'jobs' => [],
    ];

    foreach (['PHP-7.4', 'PHP-8.0', 'PHP-8.1', 'master'] as $branch) {
        $branch_key = strtoupper(str_replace('.', '', $branch));

        foreach (['linux_x64', 'linux_i386', 'macos'] as $plf) {
            foreach ([true, false] as $debug) {
                foreach ([true, false] as $zts) {
                    $job_name = $branch_key . '_' . strtoupper($plf) . '_' . ($debug ? 'DEBUG' : 'RELEASE') . '_' . ($zts ? 'ZTS' : 'NTS');
                    $configuration_parameters = '--' . ($debug ? 'enable' : 'disable') . '-debug --' . ($zts ? 'enable' : 'disable') . '-zts';

                    $result['jobs'][] = $plf(
                        name: $job_name,
                        configuration_parameters: $configuration_parameters,
                        extended_tests: true,
                        branch: $branch,
                    );
                }
                $result['jobs'][] = linux_x64(
                    name: $branch_key .  '_DEBUG_ZTS_ASAN_UBSAN',
                    configuration_parameters: '--enable-debug --enable-zts --enable-address-sanitizer --enable-undefined-sanitizer',
                    extended_tests: true,
                    run_tests_parameters: '--asan',
                    branch: $branch,
                );
            }
        }
    }

    return $result;

    echo "::set-output name=matrix::" . json_encode(['include' => $result], JSON_UNESCAPED_SLASHES);
}

function job($name, $runs_on, $branch, $steps) {
    $checkout = ['name' => 'git checkout', 'uses' => 'actions/checkout@v2'];
    if ($branch !== null) $checkout['with']['ref'] = $branch;
    return [
        'name' => $name,
        'runs-on' => $runs_on, 
        'steps' => [$checkout, ...$steps],
    ];
}

function linux_x64(
    $name,
    $configuration_parameters = '',
    $extended_tests = false,
    $run_tests_parameters = '',
    $branch = null,
) {
    return job($name, 'ubuntu-20.04', $branch, [
        create_mssql_container(),
        apt_x64(),
        configure_x64($configuration_parameters),
        make_linux(),
        install_linux(),
        setup_x64(),
        ...tests_linux($extended_tests, run_tests_parameters: $run_tests_parameters),
    ]);
}

function linux_i386(
    $name,
    $configuration_parameters,
    $extended_tests,
    $branch = null,
) {
    return job($name, 'ubuntu-20.04', $branch, [
        apt_i386(),
        configure_i386($configuration_parameters),
        make_linux(),
        install_linux(),
        setup_i386(),
        ...tests_linux($extended_tests),
    ]);
}

function macos(
    $name,
    $configuration_parameters,
    $extended_tests,
    $branch = null,
) {
    return job($name, 'macOS-10.15', $branch, [
        brew(),
        configure_macos($configuration_parameters),
        make_macos(),
        install_macos(),
        ...tests_macos($extended_tests),
    ]);
}

function create_mssql_container() {
    return ['name' => 'Create mssql container', 'uses' => './.github/actions/mssql'];
}

function apt_x64() {
    return ['name' => 'apt', 'uses' => './.github/actions/apt-x64'];
}

function apt_i386() {
    return ['name' => 'apt', 'uses' => './.github/actions/apt-i386'];
}

function brew() {
    return ['name' => 'brew', 'uses' => './.github/actions/brew'];
}

function configure_x64($configuration_parameters) {
    return ['name' => './configure', 'uses' => './.github/actions/configure-x64', 'with' => ['configurationParameters' => $configuration_parameters]];
}

function configure_i386($configuration_parameters) {
    return ['name' => './configure', 'uses' => './.github/actions/configure-i386', 'with' => ['configurationParameters' => $configuration_parameters]];
}

function configure_macos($configuration_parameters) {
    return ['name' => './configure', 'uses' => './.github/actions/configure-macos', 'with' => ['configurationParameters' => $configuration_parameters]];
}

function make_linux() {
    return ['name' => 'make', 'run' => 'make -j$(/usr/bin/nproc) >/dev/null'];
}

function make_macos() {
    return ['name' => 'make', 'run' => <<<BASH
        export PATH="/usr/local/opt/bison/bin:\$PATH"
        make -j$(sysctl -n hw.logicalcpu) >/dev/null
    BASH];
}

function install_linux() {
    return ['name' => 'make install', 'uses' => './.github/actions/install-linux'];
}

function install_macos() {
    return ['name' => 'make install', 'run' => 'sudo make install'];
}

function setup_x64() {
    return ['name' => 'Setup', 'uses' => './.github/actions/setup-x64'];
}

function setup_i386() {
    return ['name' => 'Setup', 'uses' => './.github/actions/setup-i386'];
}

function tests_linux($extended_tests, $run_tests_parameters = '') {
    $opcache_param = '-d zend_extension=opcache.so';

    $result = [
        test_linux('Test', $run_tests_parameters),
        test_linux('Test Tracing JIT', "$run_tests_parameters $opcache_param -d opcache.jit_buffer_size=16M"),
    ];

    if ($extended_tests) {
        $result[] = test_linux('Test OpCache', "$run_tests_parameters $opcache_param");
        $result[] = test_linux('Test Function JIT', "$run_tests_parameters $opcache_param -d opcache.jit_buffer_size=16M -d opcache.jit=1205");
    }

    return $result;
}

function test_linux($step_name, $run_tests_parameters) {
    return ['name' => $step_name, 'uses' => './.github/actions/test-linux', 'with' => ['runTestsParameters' => $run_tests_parameters]];
}

function tests_macos($extended_tests) {
    $opcache_param = '-d zend_extension=opcache.so -d opcache.protect_memory=1';

    $result = [
        test_macos('Test', ''),
        test_macos('Test Tracing JIT', "$opcache_param -d opcache.jit_buffer_size=16M"),
    ];

    if ($extended_tests) {
        $result[] = test_macos('Test OpCache', $opcache_param);
        $result[] = test_macos('Test Function JIT', "$opcache_param -d opcache.jit_buffer_size=16M -d opcache.jit=1205");
    }

    return $result;
}

function test_macos($step_name, $run_tests_parameters) {
    return ['name' => $step_name, 'uses' => './.github/actions/test-macos', 'with' => ['runTestsParameters' => $run_tests_parameters]];
}

$result = match ($argv[1]) {
    'push' => generate_push(),
    'schedule' => generate_nightly(),
    default => throw new \InvalidArgumentException('Event name should be "push" or "schedule"'),
};
echo '::set-output name=matrix::' . json_encode(['include' => $result], JSON_UNESCAPED_SLASHES) . "\n";
