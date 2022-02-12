<?php

if (!function_exists('yaml_emit')) {
    fwrite(STDERR, "yaml extension is required\n");
    exit(1);
}

const GENERATION_WARNING = <<<COMMENT
#############################################################
#   WARNING: automatically generated file, DO NOT CHANGE!   #
#############################################################

# This file was automatically generated. Adjust and run the
# .github/generate_ci.php file to make changes to the
# pipeline.


COMMENT;

function generate(): void {
    generate_push();
}

function generate_push(): void {
    $result = [
        'name' => 'Push',
        'on' => ['push' => ['paths-ignore' => [
            'docs/*',
            'NEWS',
            'UPGRADING',
            'UPGRADING.INTERNALS',
            'README.md',
            'CONTRIBUTING.md',
            'CODING_STANDARDS.md',
        ]]],
        'jobs' => [
            'test' => [
                'strategy' => [
                    'matrix' => [
                        'include' => [
                            // [
                            //     'arch' => 'linux-x64',
                            //     'debug' => true,
                            //     'zts' => false,
                            // ],
                            // [
                            //     'arch' => 'linux-x64',
                            //     'debug' => false,
                            //     'zts' => true,
                            // ],
                            // apt broken, not sure why
                            // [
                            //     'arch' => 'linux-i386',
                            //     'debug' => true,
                            //     'zts' => true,
                            // ],
                            [
                                'arch' => 'macos',
                                'debug' => true,
                                'zts' => false,
                            ],
                        ]
                    ],
                    'fail-fast' => false,
                ],
                // Generate
                'name' => "\${{ matrix.arch }}-\${{ matrix.debug && 'debug' || 'release' }}-\${{ matrix.zts && 'zts' || 'nts' }}",
                'runs-on' => "\${{ startsWith(matrix.arch, 'linux-') && 'ubuntu-20.04' || 'macOS-10.15' }}",
                'env' => ['ARCH' => '${{ matrix.arch }}'],
                'steps' => [
                    step_checkout(),
                    step_mssql(),
                    step_deps(),
                    step_configure(),
                    step_make(),
                    step_make_install(),
                    step_setup(),
                    ...step_tests(extended: false),
                ],
            ],
        ],
    ];

    file_put_contents(__DIR__ . '/workflows/push.yml', GENERATION_WARNING . yaml_emit($result));
}

function step_checkout(?string $branch = null): array {
    $step = ['name' => 'git checkout', 'uses' => 'actions/checkout@v2'];
    if ($branch !== null) $step['with']['ref'] = $branch;
    return $step;
}

function step_mssql(): array {
    return ['name' => 'Create mssql container', 'if' => "env.ARCH != 'macos'", 'uses' => './.github/actions/mssql'];
}

function step_deps(): array {
    return ['name' => 'Install dependencies', 'uses' => './.github/actions/deps'];
}

function step_configure(): array {
    return ['name' => './configure', 'uses' => './.github/actions/configure', 'with' => [
        'configuration-parameters' => <<<'BASH'
            --${{ inputs.debug && 'enable' || 'disable' }}-debug
            --${{ inputs.zts && 'enable' || 'disable' }}-zts
        BASH,
    ]];
}

function step_make(): array {
    return ['name' => 'make', 'run' => <<<'BASH'
        if [ "$ARCH" == "macos" ]; then
            export PATH="/usr/local/opt/bison/bin:$PATH"
        fi
        make -j$(${{ env.ARCH != 'macos' && '/usr/bin/nproc' || 'sysctl -n hw.logicalcpu' }}) >/dev/null
    BASH];
}

function step_make_install(): array {
    return ['name' => 'make install', 'uses' => './.github/actions/install'];
}

function step_setup(): array {
    return ['name' => 'Setup', 'uses' => './.github/actions/setup'];
}

function step_tests(bool $extended = false): array {
    $protect_memory = "\${{ env.ARCH == 'macos' && '-d opcache.protect_memory=1' }}";

    $steps = [
        step_test('Test'),
        step_test('Tracing JIT', <<<ARGS
            -d zend_extension=opcache.so \\
            -d opcache.jit_buffer_size=16M \\
            $protect_memory
        ARGS),
    ];

    if ($extended) {
        $steps = [
            ...$steps,
            step_test('OpCache', <<<ARGS
                -d zend_extension=opcache.so \\
                $protect_memory
            ARGS),
            step_test('OpCache', <<<ARGS
                -d zend_extension=opcache.so \\
                -d opcache.jit_buffer_size=16M \\
                -d opcache.jit=1205 \\
                $protect_memory
            ARGS),
        ];
    }

    return $steps;
}

function step_test(string $name, ?string $run_tests_parameters = null): array {
    $step = [
        'name' => $name,
        'uses' => './.github/actions/test',
    ];
    if ($run_tests_parameters) {
        $step['with']['run-tests-parameters'] = $run_tests_parameters;
    }
    return $step;
}

generate();
