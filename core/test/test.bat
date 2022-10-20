@echo off

cls

phpunit . --bootstrap ..\autoload.php --colors "always" --do-not-cache-result --testdox
