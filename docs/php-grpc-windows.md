# Running PHP with gRPC on Windows

The Firestore client (`google/cloud-firestore`) talks to Google Cloud over gRPC, so PHP needs the
`grpc` extension. It is a compiled C extension: Composer cannot install it.

## The XAMPP problem: PHP must be non-thread-safe

XAMPP ships a **thread-safe (ZTS)** build of PHP, because Apache's `mod_php` requires one. The
gRPC extension does not support ZTS. It loads and connects, but the moment it calls back into PHP
to attach the authentication metadata to a request, the process dies:

```
php artisan firestore:init
INFO  Connecting to Firestore project [cse3cap-equipment-borrowing].
Segmentation fault      (exit code 139)
```

There is no configuration that fixes this - not the credentials, not the service account, not
`php.ini`. The crash happens inside the extension.

This affects Apache too, not only the command line, so **the application cannot be served through
XAMPP's Apache**. Use a separate **non-thread-safe (NTS)** PHP build for both `php artisan` and
`php artisan serve`. XAMPP stays installed and untouched; only MySQL/phpMyAdmin would still use
it, and this project needs neither.

## 1. Install a standalone NTS PHP

Reference layout used by the team: `D:\php-nts`. Download the **nts**, **vs16**, **x64** build of
PHP 8.2 and the matching gRPC DLL - all three properties must match or the DLL will not load.

```powershell
$php  = "https://downloads.php.net/~windows/releases/php-8.2.33-nts-Win32-vs16-x64.zip"
$grpc = "https://downloads.php.net/~windows/pecl/releases/grpc/1.83.0/php_grpc-1.83.0-8.2-nts-vs16-x64.zip"

Invoke-WebRequest $php  -OutFile "$env:TEMP\php-nts.zip"  -UseBasicParsing
Invoke-WebRequest $grpc -OutFile "$env:TEMP\grpc-nts.zip" -UseBasicParsing

Expand-Archive "$env:TEMP\php-nts.zip"  -DestinationPath "D:\php-nts" -Force
Expand-Archive "$env:TEMP\grpc-nts.zip" -DestinationPath "$env:TEMP\grpc" -Force
Copy-Item "$env:TEMP\grpc\php_grpc.dll" "D:\php-nts\ext\php_grpc.dll" -Force
```

## 2. Configure php.ini

```powershell
Copy-Item "D:\php-nts\php.ini-development" "D:\php-nts\php.ini"
```

Edit `D:\php-nts\php.ini` and make sure these lines are active (remove the leading `;`):

```ini
extension_dir = "D:\php-nts\ext"

extension=openssl
extension=curl
extension=mbstring
extension=fileinfo
extension=zip
extension=pdo_sqlite
extension=sqlite3
extension=sodium
extension=intl
extension=grpc
```

## 3. Give it a CA bundle

A fresh PHP build has no root certificates, so every HTTPS call fails with
`cURL error 60: unable to get local issuer certificate`. gRPC ships its own roots, which is why
Firestore reads can work while token requests fail.

```powershell
New-Item -ItemType Directory -Force -Path "D:\php-nts\extras\ssl"
Invoke-WebRequest "https://curl.se/ca/cacert.pem" -OutFile "D:\php-nts\extras\ssl\cacert.pem" -UseBasicParsing
```

Then in `php.ini`:

```ini
curl.cainfo="D:\php-nts\extras\ssl\cacert.pem"
openssl.cafile="D:\php-nts\extras\ssl\cacert.pem"
```

## 4. Verify

```powershell
D:\php-nts\php.exe -v          # must print NTS, not ZTS
D:\php-nts\php.exe -m | Select-String "grpc|curl|openssl"
D:\php-nts\php.exe artisan firestore:init
```

## 5. Use it for everything in this project

```powershell
D:\php-nts\php.exe artisan serve
D:\php-nts\php.exe artisan firebase:make-admin admin@example.com
```

To avoid typing the full path, put `D:\php-nts` **before** the XAMPP entry in the `Path`
environment variable, then open a new terminal and confirm with:

```powershell
php -v
```

Composer keeps working with either build; only the runtime matters.

## Troubleshooting

| Symptom | Cause | Fix |
| --- | --- | --- |
| `Segmentation fault`, exit code 139 | Thread-safe (ZTS) PHP, usually XAMPP's | Use the NTS build, as above |
| `Unable to load dynamic library 'grpc'` | DLL variant does not match the PHP build (ts/nts, x86/x64, version) | Re-download the matching file |
| `cURL error 60 ... local issuer certificate` | No CA bundle configured | Step 3 |
| `php -m` does not list `grpc`, no warning | The wrong `php.ini` was edited | Check the path printed by `php --ini` |
| Very slow Firestore calls | The pure-PHP protobuf fallback is in use | Optional: also install the `protobuf` PECL extension |
| `Class "Google\Cloud\Firestore\FirestoreClient" not found` | Dependencies missing | `composer install` |
