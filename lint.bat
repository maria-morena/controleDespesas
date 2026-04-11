@echo off
echo Verificando arquivos PHP...

for /r %%f in (*.php) do (
    C:\xampp\php\php.exe -l "%%f"
)

echo.
echo Verificacao concluida!
pause