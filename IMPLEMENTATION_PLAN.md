# Review Submission and Approval Completion Plan

## Audit result

The July milestone supplied the public form, database insert, login, and basic moderation controls. The remaining work was production hardening and completion of the moderation lifecycle.

## Implemented in this branch

- Preserve valid form values and show validation errors inline after redirect.
- Record explicit publication consent and add basic bot/rate protection.
- Clean up uploaded files when validation or database insertion fails.
- Calculate the public review count and average from approved reviews.
- Add rejected review state, approval/moderation timestamps, and an audit log.
- Make moderation updates transactional and constrain photo deletion to generated upload paths.
- Disable admin login when no password is configured; throttle failed logins.
- Harden session cookies and standardize POST redirects.
- Provide a one-time migration for existing installations.

## Review and release checklist

- Run the migration against a backup/staging copy of the current database.
- Set a strong `ADMIN_PASSWORD` in the hosting environment.
- Confirm `uploads/` is writable and PHP Fileinfo/Mbstring/PDO MySQL are enabled.
- Test: valid submission, invalid submission value preservation, photo validation, approve, reject, unpublish, feature, unfeature, and delete.
- Confirm only approved reviews affect the public count/average and appear publicly.
- After approval, merge this feature branch through the normal protected-branch process and deploy separately.
