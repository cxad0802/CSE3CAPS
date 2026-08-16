# CSE3CAPS - Equipment Borrowing System

Capstone project 2026 - La Trobe University (CSE3CAPS).

A web application that lets students browse a catalog of university equipment and submit
borrow requests, while administrators review and approve them. The system is built with
**Laravel (Blade)** on the server side and **Firebase** (Authentication + Cloud Firestore)
as the identity provider and data store.

---

## Table of contents

- [Tech stack](#tech-stack)
- [Team and responsibilities](#team-and-responsibilities)
- [Repository rules](#repository-rules)
  - [Branching model](#branching-model)
  - [Branch naming](#branch-naming)
  - [Commit messages](#commit-messages)
  - [Pull request rules](#pull-request-rules)
  - [Code of conduct for the repository](#code-of-conduct-for-the-repository)
- [Daily workflow (step by step)](#daily-workflow-step-by-step)
- [Local setup](#local-setup)
- [Project structure](#project-structure)
- [Sprint plan](#sprint-plan)

---

## Tech stack

| Layer | Technology |
| --- | --- |
| Backend | Laravel 12 (PHP 8.2+) |
| Frontend | Blade templates, Vite, Tailwind CSS |
| Authentication | Firebase Authentication (Email/Password, Google) |
| Database | Cloud Firestore |
| Backend SDK | `kreait/firebase-php` (ID token verification), `google/cloud-firestore` (data access) |
| Local server | XAMPP / `php artisan serve` |

---

## Team and responsibilities

| Member | Role | Main focus |
| --- | --- | --- |
| Person A | Frontend / Design | Blade templates, wireframes, Firebase JavaScript SDK, UI/UX |
| Person B | Backend / Infrastructure | Laravel setup, Firebase Admin SDK, middleware, controllers, Firestore |
| Scrum Master | Management | Sprint reports, workshop delivery, backlog grooming |

Documentation and presentation deliverables are shared work.

---

## Repository rules

These rules are mandatory for every team member. They exist so that `main` always contains
a working, demonstrable version of the project.

### Branching model

We use a simplified Git Flow with three levels of branches.

```
feature/*  ->  develop  ->  main
```

| Branch | Purpose | Who can push directly |
| --- | --- | --- |
| `main` | Final, release-ready code. Only updated when every sprint deliverable is complete, all tests pass and the application runs without errors. | Nobody. Merge via pull request only. |
| `develop` | Integration branch. All completed features live here and are tested together. | Nobody. Merge via pull request only. |
| `feature/*` | One branch per task. Created from `develop`, merged back into `develop`. | The owner of the task. |

Rules:

1. **Never commit directly to `main` or `develop`.** Always work on a `feature/*` branch.
2. Always create your branch from an up-to-date `develop`.
3. One task equals one branch. Do not mix unrelated changes in a single branch.
4. Keep your branch up to date with `develop` while you work, so that merge conflicts stay small.
5. `main` is only updated at the end of the project (or at a tagged milestone), after the full
   application has been tested and confirmed working.
6. Delete your feature branch after it has been merged.

### Branch naming

Use the prefix that matches the type of work, all lowercase, words separated by hyphens.

| Prefix | Use for | Example |
| --- | --- | --- |
| `feature/` | New functionality | `feature/leaderboard`, `feature/borrow-request-form` |
| `fix/` | Bug fixes | `fix/login-redirect-loop` |
| `docs/` | Documentation only | `docs/sprint-2-report` |
| `chore/` | Tooling, config, dependencies | `chore/upgrade-laravel` |

Do not use personal names such as `kenny-branch` or generic names such as `test1`.

### Commit messages

We follow [Conventional Commits](https://www.conventionalcommits.org/).

```
<type>: <short summary in imperative mood>
```

Allowed types: `feat`, `fix`, `docs`, `style`, `refactor`, `test`, `chore`.

Good examples:

```
feat: add borrow request modal to equipment catalog
fix: verify firebase token before resolving user role
docs: add firestore schema reference
```

Bad examples: `update`, `fixed stuff`, `asdf`, `final version 2`.

Keep commits small and focused; a reviewer should be able to understand each commit on its own.

### Pull request rules

1. When your feature branch is finished, push it and open a pull request **into `develop`**
   (never into `main`).
2. Every pull request requires **at least one approving review from another team member**
   before it can be merged. The author must not merge their own pull request without approval.
3. Fill in the pull request template: what changed, why, how it was tested, and the related
   sprint task.
4. The reviewer checks that the code runs, follows the project conventions, contains no
   leftover debug code, and contains no secrets.
5. Resolve all review comments before merging. If a change is requested, push new commits to
   the same branch; do not open a new pull request.
6. If the pull request has conflicts with `develop`, the author resolves them.

### Code of conduct for the repository

- **Never commit secrets.** Firebase service account JSON files, API keys and `.env` files stay
  out of the repository. Use `.env.example` to document required variables.
- Never force-push to `main` or `develop`.
- Never rewrite history on a branch that somebody else is reviewing.
- Do not commit generated artefacts (`vendor/`, `node_modules/`, build output).
- Communicate blockers early in the team channel; do not sit on a broken branch until the
  workshop.

> Branch protection for `main` and `develop` is configured in
> **GitHub -> Settings -> Branches -> Add branch ruleset**, with "Require a pull request before
> merging" and "Require 1 approval" enabled. See
> [docs/github-branch-protection.md](docs/github-branch-protection.md).

---

## Daily workflow (step by step)

```bash
# 1. Start from an up-to-date develop
git checkout develop
git pull origin develop

# 2. Create your feature branch
git checkout -b feature/equipment-catalog

# 3. Work, then stage and commit
git add .
git commit -m "feat: build equipment catalog layout"

# 4. Push your branch
git push -u origin feature/equipment-catalog

# 5. Open a pull request on GitHub: base = develop, compare = feature/equipment-catalog
#    Request a review from the other team member.

# 6. After the pull request is approved and merged, clean up
git checkout develop
git pull origin develop
git branch -d feature/equipment-catalog
```

To bring the latest `develop` into a branch you are still working on:

```bash
git checkout feature/equipment-catalog
git fetch origin
git merge origin/develop        # resolve any conflicts, then commit
```

---

## Local setup

### Prerequisites

- PHP **8.2 or newer** with the following extensions enabled: `mbstring`, `openssl`, `curl`,
  `zip`, `bcmath`, and **`grpc`** (required by the Firestore client).
- Composer 2.x
- Node.js 20 or newer, with npm
- Access to the project's Firebase console

Windows / XAMPP users: the `grpc` extension is not bundled. Follow
[docs/php-grpc-windows.md](docs/php-grpc-windows.md) to install it.

### Installation

```bash
git clone https://github.com/cxad0802/CSE3CAPS.git
cd CSE3CAPS

composer install
npm install

cp .env.example .env
php artisan key:generate
```

### Firebase configuration

1. Follow [docs/firebase-setup.md](docs/firebase-setup.md) to download the service account
   JSON key.
2. Save it as `storage/app/firebase/service-account.json` (this path is git-ignored).
3. Fill in the `FIREBASE_*` variables in `.env`.
4. Verify the connection:

```bash
php artisan firestore:init
```

### Running the application

Run each command in its own terminal:

```bash
php artisan serve      # http://127.0.0.1:8000
npm run dev            # Vite dev server for assets
```

---

## Project structure

```
CSE3CAPS/
├── app/
│   ├── Console/Commands/     # Artisan commands (e.g. firestore:init)
│   ├── Http/Controllers/     # Request handling
│   ├── Http/Middleware/      # Firebase token verification, role checks
│   ├── Providers/            # FirebaseServiceProvider
│   └── Services/             # FirestoreService and other integrations
├── config/firebase.php       # Firebase / Firestore configuration
├── docs/                     # Setup guides, schema, diagrams, sprint reports
├── resources/views/          # Blade templates
├── routes/web.php            # Web routes
└── storage/app/firebase/     # Service account key (ignored by git)
```

---

## Sprint plan

### Sprint 2 - Setup and basic UI

| Task | Owner |
| --- | --- |
| Initialise the Laravel project and install the Firebase Admin PHP SDK | Person B |
| Integrate the Firebase JavaScript SDK into `app.blade.php` | Person A |
| UI wireframes for the login page and equipment catalog | Person A |
| Blade templates for main navigation and catalog layout | Person A |
| Firestore collections (`users`, `equipments`, `borrowRequests`) and document structures | Person B |
| Use case diagrams and "Report Sprint 1" | Shared |

### Sprint 3 - Authentication and core features

| Task | Owner |
| --- | --- |
| Blade forms for login, registration and the borrow request modal | Person A |
| Firebase Authentication on the frontend, passing ID tokens to Laravel | Person B |
| Laravel middleware for token verification and role-based access (admin vs. user) | Person B |
| Controllers that process borrow requests and write to Firestore | Person B |
| Mid-semester presentation slide deck and local demo environment | Shared |
| Deliver "Report Sprint 2" in the workshop | Scrum Master |
