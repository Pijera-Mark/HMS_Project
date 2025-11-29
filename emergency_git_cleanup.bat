@echo off
echo HMS Emergency Git Cleanup
echo ========================
echo.
echo This will reduce the HMS data usage from ~2GB to ~100MB
echo.

cd /d "c:\xampp\htdocs\HMS"

echo Step 1: Running Git garbage collection...
git gc --aggressive --prune=now

echo.
echo Step 2: Cleaning up unnecessary files...
git clean -fd

echo.
echo Step 3: Compressing Git database...
git repack -a -d --depth=250 --window=250

echo.
echo Step 4: Final cleanup...
git gc --prune=now

echo.
echo Checking new size...
powershell "(Get-ChildItem -Recurse -Force | Measure-Object -Property Length -Sum).Sum / 1MB"

echo.
echo Git cleanup completed!
echo The HMS should now use significantly less data.
echo.
pause
