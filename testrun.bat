@ECHO OFF
IF ""=="%1" GOTO NOARGS
call phpunit --bootstrap src/class-codex.php --filter="%1" tests/CodexHelpHubTest
GOTO EXIT

:NOARGS
call phpunit --bootstrap src/class-codex.php tests/CodexHelpHubTest
call phpunit --bootstrap src/class-codex.php tests/CodexJaCodexTest
GOTO EXIT

:EXIT
