// Signs the user in against Firebase Authentication in the browser, then posts
// the resulting ID token to Laravel so the server-rendered pages get a session.
// The password is sent to Firebase directly and never reaches our server.

const form = document.querySelector('[data-firebase-login]');

if (form) {
    const email = form.querySelector('[name="email"]');
    const password = form.querySelector('[name="password"]');
    const idToken = form.querySelector('[name="id_token"]');
    const errorBox = form.querySelector('[data-login-error]');
    const submit = form.querySelector('[type="submit"]');

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        errorBox.textContent = '';
        submit.disabled = true;

        try {
            // Imported on demand: the Firebase SDK is only needed on this screen.
            const [{ signInWithEmailAndPassword }, { auth }] = await Promise.all([
                import('firebase/auth'),
                import('../firebase'),
            ]);

            const credential = await signInWithEmailAndPassword(
                auth,
                email.value.trim(),
                password.value,
            );

            idToken.value = await credential.user.getIdToken();

            // submit() does not re-fire this listener, so the form posts normally.
            form.submit();
        } catch (error) {
            errorBox.textContent = describe(error);
            submit.disabled = false;
        }
    });
}

/**
 * Firebase error codes are not suitable for end users, and deliberately do not
 * distinguish a wrong password from an unknown account.
 */
function describe(error) {
    switch (error?.code) {
        case 'auth/invalid-email':
        case 'auth/invalid-credential':
        case 'auth/wrong-password':
        case 'auth/user-not-found':
            return 'That email address and password do not match an account.';
        case 'auth/user-disabled':
            return 'This account has been disabled. Contact IT support.';
        case 'auth/too-many-requests':
            return 'Too many attempts. Wait a few minutes before trying again.';
        case 'auth/network-request-failed':
            return 'Network error. Check your connection and try again.';
        case 'auth/operation-not-allowed':
            return 'Email sign-in is not enabled for this Firebase project.';
        default:
            return 'Sign-in failed. Please try again.';
    }
}
