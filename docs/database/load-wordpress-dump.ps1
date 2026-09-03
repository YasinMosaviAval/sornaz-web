param(
    [string]$DumpPath = "$PSScriptRoot\sornaz database\sornazco_maindb.sql",
    [string]$Database = 'sornaz_wp_import_20260903'
)

$ErrorActionPreference = 'Stop'
$mysql = 'C:\xampp\mysql\bin\mysql.exe'
if (-not (Test-Path -LiteralPath $mysql)) { throw "mysql.exe was not found at $mysql" }
if (-not (Test-Path -LiteralPath $DumpPath)) { throw "WordPress dump was not found at $DumpPath" }
if ($Database -notmatch '^sornaz_wp_import_[0-9]+$') { throw 'Temporary database name is outside the allowed pattern.' }

& $mysql '--host=localhost' '--port=3306' '--user=root' --execute="SET GLOBAL max_allowed_packet=536870912; DROP DATABASE IF EXISTS ``$Database``; CREATE DATABASE ``$Database`` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }

$process = Start-Process -FilePath $mysql -ArgumentList @(
    '--host=localhost', '--port=3306', '--user=root', '--default-character-set=utf8mb4',
    '--max_allowed_packet=512M', "--database=$Database"
) -RedirectStandardInput $DumpPath -NoNewWindow -Wait -PassThru
exit $process.ExitCode
