@ECHO OFF
IF ""=="%1" GOTO NOARGS
SET OUTPUT_FILE=%2
IF ""=="%2" SET OUTPUT_FILE=%1-migrated
ECHO Output file is %OUTPUT_FILE%

php src/command-codextohelphub.php -i %1 -o %OUTPUT_FILE%
GOTO END

:NOARGS
ECHO Please specify input filename (required) and output filename (option).
GOTO END

:END
