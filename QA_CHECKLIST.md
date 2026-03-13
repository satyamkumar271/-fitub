# Fitub QA Checklist (Role-wise)

## 1) Guest / Public
- Open `/`, `/about`, `/contact`, `/faq`, `/privacy-policy`, `/terms-and-conditions`, `/refund-policy`.
- Verify navbar links work on desktop and mobile.
- Verify `Back to Home` appears on non-frontpage guest views.
- Frontpage calculator:
- Submit valid data and confirm BMR/calorie/macro values render.
- Goal buttons (`Maintain`, `Weight Loss`, `Weight Gain`) update macro grams.
- `Find Verified Trainers` and `Find Verified Gyms` buttons open correct pages.

## 2) Registration + OTP
- Register as `customer` with valid email.
- Confirm OTP page opens and OTP email is received.
- Wrong OTP shows error; resend OTP works after cooldown.
- Correct OTP sets account active and allows login.

- Register as `trainer`:
- Required docs: ID proof + certificate proofs.
- OTP verify should move status to review (not active).
- Login blocked until approved.

- Register as `gymowner`:
- Required docs: ID proof + business doc.
- OTP verify should move status to review (not active).
- Login blocked until approved.

## 3) KYC Review (Team/Admin Panel)
- Open `admin/pending-users` and verify tabs: pending/approved/rejected/all.
- Open user detail and verify docs are visible.
- Approve flow:
- Status becomes active.
- Verified badge appears in listings/profile.
- Reject flow:
- Reason required.
- Rejection email sent with re-register link.
- Account removed after rejection flow.

## 4) Listing & Visibility Rules
- Unverified trainer/gym should not appear on:
- Frontpage featured blocks.
- `/trainers` and `/gyms` list pages.
- Verified+active users should appear correctly.

## 5) Inquiry + Chat + Report
- Customer sends inquiry successfully.
- Trainer/gym can view inquiry in leads section.
- Chat opens as expected where allowed.
- Report flow:
- User can submit report.
- Report appears in `admin/reports`.
- Resolve valid/rejected/under_review actions work.

## 6) Lead Unlock + Credits
- Single lead unlock with payment should set lead access.
- If unlock credit exists, lead unlock should consume 1 credit and charge `0`.
- Credit usage should create `UnlockCreditLog` with delta `-1`.
- Valid report compensation with credit grant should add `+1` credit log.
- Verify history in `admin/credits`.

## 7) Payments (Razorpay)
- Test success payment flow for:
- monthly
- yearly
- single_lead
- Test cancel flow marks payment cancelled.
- Test failed verify flow marks payment failed.
- Webhook `payment.captured` / `order.paid` should not duplicate entitlement.

## 8) Support Ticket
- Customer/trainer/gym can create ticket.
- Support reply flow works from both sides.
- Resolve flow updates ticket status.

## 9) Regression
- Login/logout for all roles.
- Dashboard pages open without UI break.
- Sidebar links correct for each role.
- Blog/about/contact/legal pages accessible without auth.

## 10) Production Readiness Quick Checks
- `.env` has valid SMTP and Razorpay values.
- `APP_URL` correct.
- `php artisan storage:link` done.
- `php artisan optimize:clear` done after config updates.
