# Known issues and handover notes

This file records bugs and traps we actually hit while wiring Laravel, Firebase Auth
and Firestore, plus the fix that landed in the repo. Read it before changing auth,
Firestore queries, or anything that runs PHP on Windows.

Related guides:

- [firebase-setup.md](firebase-setup.md) - project, credentials, first admin
- [php-grpc-windows.md](php-grpc-windows.md) - NTS PHP and the `grpc` extension
- [firestore-schema.md](firestore-schema.md) - collection shapes and indexes

The live Firebase project id is `cse3cap-equipment-borrowing`. Do not create a
second project unless the team agrees; `.firebaserc` and `.env` already point here.

---

## Quick diagnosis

| What you see | Likely cause | Fix |
| --- | --- | --- |
| Process dies with exit `3221225477` / `0xC0000005`, or a silent crash on catalog / health | Thread-safe (ZTS) PHP, usually XAMPP's `php.exe` | Run `D:\php-nts\php.exe`. Confirm `php -v` prints **NTS**, not ZTS. See [php-grpc-windows.md](php-grpc-windows.md) |
| `TypeError`: `withVerifierCache()` must be `CacheItemPoolInterface`, `Repository` given | Laravel's cache is PSR-16; Kreait wants PSR-6 | Already fixed in `FirebaseServiceProvider` with `Psr16Adapter` and `symfony/cache` |
| `The database (default) does not exist` | Firestore was never created in the console | Console → Firestore Database → Create database, production, `australia-southeast1` |
| `auth/operation-not-allowed` on login | Email/Password provider is off | Console → Authentication → Sign-in method → Email/Password → Enable (leave passwordless **off**) |
| Login bounces back to `/login` with "could not be verified", token is otherwise valid | `verifyIdToken(..., checkIfRevoked: true)` plus leeway | Do **not** turn `checkIfRevoked` back on. See below |
| Catalog / listings hang then 500: `Maximum execution time exceeded` in `ExponentialBackoff` | Composite index missing; Firestore retries until PHP times out | Query with a single `orderBy`, sort in PHP, **or** deploy `firestore.indexes.json` |
| `The query requires an index` | Same as above | Open the URL in the exception, or `firebase deploy --only firestore:indexes` |
| `/admin/*` returns **403** for a student account | Expected | Only accounts with the `admin` custom claim (from `firebase:make-admin`) may enter |
| `/admin/*` returns **403** for a real admin | Token was minted before the claim was set, or the account was created in the console | Sign out and back in. Recreate the admin with `php artisan firebase:make-admin ...` |
| `firebase:make-admin --password=...` does not change an existing password | Command promotes existing users; it never updates the password | Reset in the console, or `changeUserPassword` via tinker. See [firebase-setup.md](firebase-setup.md) |
| Catalog empty | Collections exist but have no equipment | `php artisan firestore:init --demo` |

---

## 1. Windows: gRPC + XAMPP PHP (fatal)

**Symptom:** `php artisan serve` using the `php` on PATH crashes the first time a
request touches Firestore. Early on that was only `/_health/firebase`. After the
UI was wired, catalog, my-requests and every admin screen also crash.

**Cause:** XAMPP's PHP is **ZTS**. The `grpc` extension is **NTS** only. The
extension loads, then segfaults when it attaches auth metadata.

**Fix:** Always use the NTS build:

```powershell
D:\php-nts\php.exe artisan serve
D:\php-nts\php.exe artisan firestore:init
```

`php -v` must print `NTS`. If it prints `ZTS`, you are on the wrong binary.

This is documented in more depth in [php-grpc-windows.md](php-grpc-windows.md).
Do not serve this app through XAMPP Apache.

---

## 2. Token verifier cache type error (fixed)

**Symptom:** `/_health/firebase` returned 503:

```
Factory::withVerifierCache(): Argument #1 ($cache) must be of type
Psr\Cache\CacheItemPoolInterface, Illuminate\Cache\Repository given
```

**Cause:** Laravel's cache repository is PSR-16. `kreait/firebase-php` 7.x
expects a PSR-6 pool.

**Fix (already in `develop`):** wrap the store in `Psr16Adapter` and require
`symfony/cache`. Do not pass `$app->make('cache')->store(...)` straight into
`withVerifierCache()`.

---

## 3. Firestore database missing (console, once)

**Symptom:** `php artisan firestore:init` failed with
`The database (default) does not exist for project cse3cap-equipment-borrowing`.

**Cause:** Authentication can exist without a Firestore database. The health
check used to look "healthy" because a missing document is a normal read.

**Fix:** In project `cse3cap-equipment-borrowing`, create the **(default)**
database, production mode, region `australia-southeast1`. Then:

```powershell
D:\php-nts\php.exe artisan firestore:init --demo
```

`--demo` seeds four sample assets so the catalogue is not empty.

---

## 4. Email/Password sign-in disabled (console, once)

**Symptom:** browser login reports `auth/operation-not-allowed`.

**Fix:** Authentication → Sign-in method → **Email/Password** → Enable.
Leave **Email link (passwordless)** off. Do not rely on the Dynamic Links
banner; it does not apply to password sign-in.

---

## 5. `checkIfRevoked` false positive (fixed, do not revert)

**Symptom:** a freshly created admin signed in at Firebase, Laravel rejected
the ID token as `RevokedIdToken`.

**Cause:** Kreait subtracts `leewayInSeconds` from the token's `auth_time`
before comparing it with `tokensValidAfterTime`. A token minted within 60
seconds of account creation looks older than the account and is treated as
revoked. That is exactly the first login after `firebase:make-admin`.

**Fix:** `FirebaseSessionController` calls `verifyIdToken` with
`checkIfRevoked: false`. Signature and expiry are still verified. A disabled
account still cannot obtain a new token from Firebase.

**Do not** set `checkIfRevoked: true` again without a different revocation
strategy. The trade-off is documented in the controller: a revoked session
may still exchange an existing token for up to one hour.

---

## 6. Composite indexes not deployed (worked around)

`firestore.indexes.json` declares three composite indexes:

| Collection | Fields | Used for |
| --- | --- | --- |
| `borrowRequests` | `userId` + `createdAt` desc | My requests |
| `borrowRequests` | `status` + `createdAt` desc | Admin queue |
| `equipments` | `category` + `name` | Filtered catalogue |

They live in git but were **never deployed** (`firebase` CLI was not
installed). Combining `where()` with `orderBy('createdAt')`, or
`orderBy('category')->orderBy('name')`, throws `FailedPreconditionException`.
The Google client then retries until PHP hits `max_execution_time`.

**Current workaround:** listing controllers use at most one `orderBy` (or
none) and sort in PHP via `BorrowRequest::newestFirst()`. That is safe at
this scale.

**Proper fix (optional):**

```powershell
npm install -g firebase-tools
firebase login
firebase deploy --only firestore:rules,firestore:indexes
```

Never run a bare `firebase deploy` (that would try to enable Hosting).

---

## 7. Two Firestore integrations on `develop` (resolved in PR #8)

UI work landed on `develop` through PR #7 (`test-merge-ui`). That PR stored
Firestore logic in **route closures** using `app('firebase.firestore')`
(`kreait/laravel-firebase`): login by email lookup with **no password check**,
no middleware, approve/return without adjusting `availableQuantity`.

`feature/firestore-ui-integration` already had controllers, Firebase ID-token
sessions, `firebase.auth` / `firebase.admin` middleware, and stock
transactions.

Merging the feature branch into `develop` without first merging `develop`
into the feature branch would have conflicted on every Blade view and
`routes/web.php`.

**What we did:** merge `origin/develop` into the feature branch, keep the new
UI, replace the closures with controllers, then PR #8 back into `develop`.

`kreait/laravel-firebase` may still appear in `composer.json` because it came
from PR #7. It is listed under Composer's `dont-discover` so it does not
overwrite `config/firebase.php`. Application code uses
`App\Providers\FirebaseServiceProvider` and `FirestoreService` only.

---

## 8. 403 on admin routes is not a bug

```
GET /admin/dashboard   student  → 403
GET /admin/inventory   student  → 403
GET /admin/dashboard   guest    → 302 /login
GET /admin/dashboard   admin    → 200
```

Students must not see the manager portal. If an administrator gets 403,
their ID token does not contain `role: "admin"` (create the account with
`firebase:make-admin`, then sign out and in).

---

## 9. Passwords cannot be read back

Firebase stores password hashes. Nobody can view a password in the console
or through the Admin SDK. You can only **reset** it.

`firebase:make-admin` with `--password` only applies when the email is **new**.
If the account already exists, the command promotes or demotes the role and
ignores `--password`.

---

## Seeded test accounts

Created for local testing. Change these if they have been shared outside the team.

| Email | Password | Role |
| --- | --- | --- |
| `admincap@cse3caps.test` | `admin0123!` | admin |
| `student@cse3caps.test` | `student0123!` | user |

Firebase requires a full email; `admincap` alone is not valid.

```powershell
D:\php-nts\php.exe artisan firebase:make-admin someone@cse3caps.test --password="secret" --name="Display Name"
# student instead of admin:
D:\php-nts\php.exe artisan firebase:make-admin someone@cse3caps.test --password="secret" --demote
```

---

## How to run locally (handover)

```powershell
cd d:\LATROBE\CAPSTONE\ASG1\CSE3CAPS
git checkout develop
git pull origin develop
D:\php-nts\php.exe artisan serve
# optional, second terminal:
npm run dev
```

Open http://127.0.0.1:8000/login

`vendor`, `node_modules` and `public/build` are expected to already exist after
`composer install` / `npm install` / `npm run build` on a fresh clone. The
service account file `storage/app/firebase/service-account.json` is gitignored;
copy it from the team, never commit it.

---

## Git workflow (do not skip)

```
feature/*  →  develop  →  main
```

1. Branch from an up-to-date `develop`.
2. One task per branch. Names are lowercase with hyphens:
   `feature/equipment-catalog`, `fix/login-redirect`, `docs/sprint-2-report`.
3. If `develop` moved while you worked, **merge `develop` into your feature
   branch first**, resolve conflicts there, then open the PR.
4. PR base is always `develop`, never `main`.
5. `main` is updated only when the sprint deliverable is accepted.

This branch (`feature/update-bug-fix`) is for documenting and fixing the
issues above. Further bug fixes should be small commits here, then a PR
into `develop`.
