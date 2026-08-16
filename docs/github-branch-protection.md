# GitHub branch protection setup

These settings must be applied once by the repository owner. They enforce the workflow
described in the [README](../README.md): no direct pushes to `main` or `develop`, and every
pull request needs one approving review.

## Steps

1. Open the repository on GitHub: `https://github.com/cxad0802/CSE3CAPS`.
2. Go to **Settings** -> **Branches**.
3. Under **Branch protection rules** (or **Rulesets** on newer accounts), click
   **Add branch protection rule**.
4. Create the rule for `main`:
   - **Branch name pattern**: `main`
   - Enable **Require a pull request before merging**
     - Set **Required number of approvals before merging** to `1`
     - Enable **Dismiss stale pull request approvals when new commits are pushed**
   - Enable **Require conversation resolution before merging**
   - Enable **Do not allow bypassing the above settings**
   - Leave **Allow force pushes** and **Allow deletions** disabled
   - Click **Create**.
5. Repeat step 4 with the **Branch name pattern** `develop`.
6. Go to **Settings** -> **General** -> **Default branch** and change the default branch to
   `develop`, so new pull requests target `develop` automatically.
7. In **Settings** -> **General** -> **Pull Requests**, enable
   **Automatically delete head branches** so merged feature branches are cleaned up.

## Verification

After applying the rules, this command must fail:

```bash
git checkout develop
git commit --allow-empty -m "chore: test protection"
git push origin develop
# expected: remote rejected ... protected branch hook declined
```

If the push succeeds, the rule was not applied to the correct branch pattern.
