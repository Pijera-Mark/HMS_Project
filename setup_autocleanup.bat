@echo off
echo Setting up automatic HMS cleanup task...

REM Create a scheduled task to run cleanup daily at 2 AM
schtasks /create /sc daily /st 02:00 /tn "HMS Cleanup" /tr "php C:\xampp\htdocs\HMS\spark cleanup:system" /f

if %ERRORLEVEL% EQU 0 (
    echo Automatic cleanup task created successfully!
    echo The HMS will automatically clean up old files daily at 2:00 AM.
    echo.
    echo To run cleanup manually:
    echo   php C:\xampp\htdocs\HMS\spark cleanup:system
    echo.
    echo To view scheduled tasks:
    echo   schtasks /query /tn "HMS Cleanup"
    echo.
    echo To remove the task:
    echo   schtasks /delete /tn "HMS Cleanup" /f
) else (
    echo Failed to create scheduled task. Please run as Administrator.
)

pause
