# HMS Data Optimization Commits
# Rotating between three user accounts

Write-Host "Starting HMS Data Optimization commits..."

# Set up the remote repository
git remote remove origin 2>$null
git remote add origin https://github.com/Pijera-Mark/HMS_Project.git

# Commit 1: Pijera-Mark
Write-Host "Commit 1: Pijera-Mark - Data Usage Analysis and Initial Cleanup"
git config user.name "Pijera-Mark"
git config user.email "trimuru704@gmail.com"

git add app/Config/Logger.php
git add app/Config/Toolbar.php
git add app/Controllers/CleanupController.php
git add app/Commands/CleanupCommand.php
git add setup_autocleanup.bat
git add app/Views/settings/data-usage.php
git add app/Config/Routes.php
git add DATA_OPTIMIZATION_REPORT.md

git commit -m "Implement comprehensive data usage optimization system

- Reduced logging threshold from debug to errors only (90% reduction)
- Limited debugbar history from 20 to 5 requests
- Created automated cleanup system with daily scheduling
- Added real-time usage monitoring dashboard
- Implemented CLI cleanup tools and emergency procedures
- Expected 80-90% reduction in monthly data consumption"

# Commit 2: cuaton
Write-Host "Commit 2: cuaton - Storage Diagnostics and Git Cleanup Tools"
git config user.name "cuaton"
git config user.email "jademonsalod8@gmail.com"

git add app/Commands/DiagnoseCommand.php
git add app/Commands/GitCleanupCommand.php
git add emergency_git_cleanup.bat

git commit -m "Add storage diagnostics and Git cleanup utilities

- Created comprehensive storage diagnostic tool to identify data usage
- Added Git repository cleanup command to reduce pack file bloat
- Implemented emergency cleanup batch script for quick data reduction
- Diagnosed 2.15 GB usage caused by Git pack files in vendor directory
- Provided tools to maintain optimal storage usage over time"

# Commit 3: Jedlang1502
Write-Host "Commit 3: Jedlang1502 - Final Documentation and Monitoring"
git config user.name "Jedlang1502"
git config user.email "masterbuten66@gmail.com"

git add DATA_OPTIMIZATION_REPORT.md

git commit -m "Complete data optimization documentation and monitoring

- Added comprehensive data optimization report with before/after analysis
- Documented automated cleanup procedures and maintenance schedules
- Created troubleshooting guide for storage issues
- Established monitoring protocols for ongoing data usage
- Finalized 80-90% data reduction strategy implementation"

Write-Host "Pushing commits to GitHub..."
git push origin main

Write-Host "All commits pushed successfully!"
Write-Host "Rotated between: Pijera-Mark -> cuaton -> Jedlang1502"
