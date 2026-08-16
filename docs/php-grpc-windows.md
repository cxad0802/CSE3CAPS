# Installing the PHP gRPC extension on Windows (XAMPP)

The Firestore client (`google/cloud-firestore`) communicates with Google Cloud over gRPC, so
PHP needs the `grpc` extension. It is not bundled with XAMPP and it cannot be installed with
Composer - it is a compiled C extension.

## 1. Identify your PHP build

```powershell
php -i | Select-String "Thread Safety", "Architecture", "Compiler"
php -v
```

You need three facts:

| Fact | Example value | Meaning for the download |
| --- | --- | --- |
| PHP version | 8.2.12 | pick the `8.2` DLL |
| Thread Safety | enabled | pick **ts** (if disabled, pick **nts**) |
| Architecture | x64 | pick **x64** |

The reference machine for this project is PHP **8.2.12 ZTS x64** (compiler VS16), so the correct
file is `php_grpc-1.83.0-8.2-ts-vs16-x64.zip`.

## 2. Download the DLL

Browse <https://windows.php.net/downloads/pecl/releases/grpc/> and open the latest version
folder (1.83.0 at the time of writing), or download directly:

```powershell
$url = "https://windows.php.net/downloads/pecl/releases/grpc/1.83.0/php_grpc-1.83.0-8.2-ts-vs16-x64.zip"
Invoke-WebRequest -Uri $url -OutFile "$env:TEMP\grpc.zip" -UseBasicParsing
Expand-Archive "$env:TEMP\grpc.zip" -DestinationPath "$env:TEMP\grpc" -Force
```

The archive is around 40 MB; only `php_grpc.dll` (about 10 MB) is needed.

## 3. Copy the DLL into the PHP extension directory

Find the directory with `php --ini` and the `extension_dir` setting. On the reference machine it
is `D:\TDT\XAMPP\php\ext`.

```powershell
Copy-Item "$env:TEMP\grpc\php_grpc.dll" "D:\TDT\XAMPP\php\ext\php_grpc.dll" -Force
```

## 4. Enable the extensions in php.ini

Locate `php.ini` with `php --ini`, make a backup, then in the *Dynamic Extensions* section make
sure these three lines are present and not commented out:

```ini
extension=sodium
extension=zip
extension=grpc
```

- `grpc` is required by the Firestore client.
- `zip` is required by Composer to extract packages.
- `sodium` is used by Laravel's encryption helpers.

## 5. Verify

```powershell
php -m | Select-String "grpc|zip|sodium"
php -r "echo phpversion('grpc'), PHP_EOL;"
```

Expected output: the three extension names, then the gRPC version (for example `1.83.0`).

If you run the application through Apache instead of `php artisan serve`, restart Apache from
the XAMPP control panel and confirm `grpc` appears in `phpinfo()`.

## Troubleshooting

| Symptom | Cause | Fix |
| --- | --- | --- |
| `Unable to load dynamic library 'grpc'` | Wrong DLL variant (nts vs. ts, or x86 vs. x64) | Re-check step 1 and download the matching build |
| `php -m` does not list `grpc` and no warning is shown | Edited the wrong `php.ini` | Use the path printed by `php --ini` |
| `Class "Google\Cloud\Firestore\FirestoreClient" not found` | Composer dependencies missing | Run `composer install` |
| Extremely slow Firestore calls | The pure-PHP protobuf fallback is in use | Optional: also install the `protobuf` PECL extension |
