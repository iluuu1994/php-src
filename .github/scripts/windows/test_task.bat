if /i "%GITHUB_ACTIONS%" neq "True" (
    echo for CI only
    exit /b 3
)

set NO_INTERACTION=1
set REPORT_EXIT_STATUS=1
set SKIP_IO_CAPTURE_TESTS=1

call %~dp0find-target-branch.bat
if "%BRANCH%" neq "master" (
	set STABILITY=stable
) else (
	set STABILITY=staging
)
set DEPS_DIR=%PHP_BUILD_CACHE_BASE_DIR%\deps-%BRANCH%-%PHP_SDK_VS%-%PHP_SDK_ARCH%
if not exist "%DEPS_DIR%" (
	echo "%DEPS_DIR%" doesn't exist
	exit /b 3
)

rem prepare for OPcache
if "%OPCACHE%" equ "1" set OPCACHE_OPTS=-d opcache.enable=1 -d opcache.enable_cli=1 -d opcache.protect_memory=1 -d opcache.jit_buffer_size=64M -d opcache.jit=tracing

set PHP_BUILD_DIR=%PHP_BUILD_OBJ_DIR%\Release
if "%THREAD_SAFE%" equ "1" set PHP_BUILD_DIR=%PHP_BUILD_DIR%_TS

rem remove ext dlls for which tests are not supported
for %%i in (ldap) do (
	del %PHP_BUILD_DIR%\php_%%i.dll
)

rem reduce excessive stack reserve for testing
editbin /stack:8388608 %PHP_BUILD_DIR%\php.exe
editbin /stack:8388608 %PHP_BUILD_DIR%\php-cgi.exe

copy /-y %DEPS_DIR%\bin\*.dll %PHP_BUILD_DIR%\*

if "%ASAN%" equ "1" set ASAN_OPTS=--asan

mkdir c:\tests_tmp

nmake test TESTS="%OPCACHE_OPTS% -g FAIL,BORK,LEAK,XLEAK %ASAN_OPTS% --no-progress -q --offline --show-diff --show-slow 1000 --set-timeout 120 --temp-source c:\tests_tmp --temp-target c:\tests_tmp %PARALLEL% %GITHUB_WORKSPACE%\ext\opcache\tests\jit\gh15834.phpt"

set EXIT_CODE=%errorlevel%

exit /b %EXIT_CODE%
