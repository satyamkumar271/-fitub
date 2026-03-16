## Fitub – Complete Workflow Guide (Users, Trainers, Gyms)

Yeh file high‑level documentation hai ki **customer, trainer, gym owner** aur **admin** Fitub ko kaise use karte hain, kya fayde hain, aur important features (dashboard, credits, chat, KYC, payments, support, blocks, cancellations) kaise kaam karte hain.

---

## 1. User Types aur Roles

- **Customer (normal user)**  
  - Gyms / trainers ko search karta hai.  
  - Inquiries bhejta hai, chat se baat karta hai.  
  - Koi paid plan nahi, sirf service le raha hai.

- **Trainer**  
  - Apni profile / specialization set karta hai.  
  - Leads (inquiries) receive karta hai, chat karta hai, plans buy karke unlimited leads unlock kar sakta hai.  
  - Payments dashboard, credit history, support, blocked users, reports sab use karta hai.

- **Gym Owner**  
  - Gym profile, address, services manage karta hai.  
  - Leads / visit booking handle karta hai.  
  - Trainer ki tarah hi plans, payments, chat, credit history use karta hai.

- **Admin**  
  - Sab users ka data, KYC, payments, reports, blocked users, registrations manage karta hai.

---

## 2. Registration & Login Flow

### 2.1. Registration (Customer / Trainer / Gym Owner)

1. User `Register` page pe jaata hai.  
2. **Name, Email, Password, User Type** choose karta hai (`customer`, `trainer`, `gymowner`).  
3. System ek user record banata hai:
   - `status = email_unverified`  
   - `kyc_status`:
     - Customer ke liye: `not_required`  
     - Trainer / Gym: `profile_incomplete`
4. Email pe **OTP** bheja jaata hai (EmailOtp table).
5. User OTP form me code daalta hai:
   - Agar sahi hai:
     - Customer:
       - `status = active`  
       - `kyc_status = not_required`  
       - Auto-login ho jata hai.
     - Trainer / Gym:
       - `status = profile_incomplete`  
       - `kyc_status = profile_incomplete`  
       - Login karke profile complete karni hoti hai.

### 2.2. Login

- Login pe kuch status checks:
  - `email_unverified` → login block, pehle OTP verify.  
  - `pending` → “account under review” message.  
  - `cancelled` → “account cancelled by admin” message (login block).

---

## 3. Profile & KYC Flow

### 3.1. Customer

- Dashboard pe:
  - Basic details (age, phone, weight, height, goal, city, state) fill kar sakta hai.  
  - Profile photo upload kar sakta hai.  
  - KYC required nahi hai, `status = active` hi rehta hai.

### 3.2. Trainer

- Dashboard → Profile form:
  - Required fields (jab profile incomplete ho): phone, city, state, specialization, experience, id proof, certificates.  
  - Certificates multiple files as gallery store hote hain.  
  - Submit karne par:
    - `status = pending`  
    - `kyc_status = pending`  
    - Admin panel me KYC review queue me chala jaata hai.

### 3.3. Gym Owner

- Dashboard → Profile form:
  - Required fields: gym name, phone, email, website (optional), address, pincode, gym age, total members, id proof, business doc.  
  - Submit karne par trainer jaisa hi:
    - `status = pending`  
    - `kyc_status = pending`  
    - Admin KYC review karega.

### 3.4. Admin KYC Review

- Admin sidebar → `KYC Reviews` / `Pending Approvals`:
  - Tabs: `pending`, `approved`, `rejected`, `all`.  
  - Profile check karke:
    - **Approve**:
      - `status = active`, `is_verified = true`, `kyc_status = approved`.  
      - User ko email se approval notification jaata hai.
    - **Reject (KYC)**:
      - Reason store hota hai, user ko email jata hai.  
      - Current code me reject hone par user soft‑delete bhi ho sakta hai.

---

## 4. Leads, Credits & Chat – Trainer / Gym Owner

### 4.1. Leads ka source

- Customer jab koi service request / inquiry form fill karta hai (gym ya trainer ko), to `Inquiry` create hota hai:
  - `user_id` = customer  
  - `recipient_id` = trainer/gym  
  - `service_needed`, `title`, `message` etc.

### 4.2. Dashboard & Leads Page

- Trainer/gym dashboard:
  - Recent leads dikhte hain.  
  - `Leads & Inquiries` page pe:
    - Sirf woh leads jinke status `forwarded` ya `viewed` hain.  
    - **Blocked** ya **reported** leads yahan se automatically hide ho jaate hain.

### 4.3. Lead Unlock & Subscriptions

- Plans (`/billing/plans`):
  - **Monthly** (30 days, unlimited leads).  
  - **Yearly** (365 days, unlimited leads).  
  - **Single Lead Unlock** (sirf ek lead ke liye).
- Flow:
  - Agar trainer/gym ke paas **active subscription** hai (monthly/yearly, `expires_at > now`):
    - Leads simply visible / unlock ho jaati hain (`viewed`).
  - Agar subscription nahi hai:
    - Specific lead unlock ke liye **single lead purchase** ya **unlock credit** use hota hai.
- **Unlock Credits**:
  - Refund / compensation cases me admin trainer ko `unlock_credits` de sakta hai.  
  - Lead unlock karte waqt agar credit available hai:
    - 1 credit use hota hai, lead unlock (free), payment record amount 0 ke saath create hota hai.  
    - `UnlockCreditLog` me history stored hai, `Credit History` page admin side se visible hai.

### 4.4. Chat Flow

- Lead detail se trainer/gym **chat open** karta hai:
  - `inquiries/{inquiry}/chat` route.
  - Sirf `user_id` (customer) aur `recipient_id` (trainer/gym) hi is chat ko access kar sakte hain.
- Chat screen features:
  - Message list (oldest→newest).  
  - Message send (agar:
    - lead unlocked hai,
    - aur chat blocked nahi hai).
  - Top actions:
    - **Report** (abuse/spam/fake lead/other).  
    - **Block** (modal with mandatory reason).

### 4.5. Block & Report ka effect

- **Block (Inquiry level)**:
  - `InquiryBlock` record create/update hota hai:
    - `inquiry_id`, `blocker_id`, `blocked_user_id`, `reason`, `active = true`.
  - Consequences:
    - Us inquiry ki chat me dono users ke liye `isBlocked = true` → “Chat is blocked for this inquiry.”  
    - Customer side:
      - `My Inquiries` list se wo lead/chat hide ho jaata hai.  
    - Trainer/gym side:
      - `Leads & Inquiries` list se bhi wo lead/chat hide ho jaata hai.  
    - New **All Chats** page se bhi wo conversation hata diya jaata hai.

- **Report**:
  - `InquiryReport` record create hota hai (reason + optional details).  
  - `fake_lead` report ke case me system auto‑block bhi create kar sakta hai.  
  - Admin panel me `Reports` section me yeh sab tickets aate hain.

### 4.6. “All Chats” Page (Instagram‑style)

- Route: `/conversations` (`inquiries.conversations`).  
- Roles: **customer, trainer, gym** sab ke liye.
- List me:
  - Har conversation = ek `Inquiry` jisme current user ya to customer hai ya recipient.  
  - **Blocked** ya **actively reported** conversations is list se bhi remove kiye jaate hain.
- Card layout:
  - Avatar (initial letter), other user ka naam, inquiry ID.  
  - Last message ka preview (`You: message...`).  
  - Right side: last activity time + status badge (`viewed` / forwarded etc.).  
  - Click → same chat screen open.

---

## 5. Customer Side Flow (Simplified)

1. Register + email verify.  
2. Dashboard / home se gyms ya trainers search.  
3. Profile page open karke inquiry send (lead create hota hai).  
4. `My Inquiries` page:
   - Khud ke saare active leads.  
   - Jis conversation ko block / report kiya gaya, wo yahan se hide ho jaata hai.  
5. Chat me:
   - Trainer/gym se messaging.  
   - Report / block options (safety ke liye).  
6. `All Chats` page:
   - Saari active conversations ek hi jaga se open ho sakti hain.

---

## 6. Payments & Subscriptions (Trainer / Gym Owner)

1. Trainer/gym login → `Leads & Inquiries` dekhta hai.  
2. Agar lead locked hai:
   - System unko `/billing/plans` pe le jaata hai.  
3. Plans me se choose:
   - Monthly / yearly / single lead.  
4. **Cashfree Checkout**:
   - Backend `orders` API se order aur `payment_session_id` banata hai.  
   - Frontend checkout SDK open hoti hai (sandbox/live).  
   - Payment success ke baad:
     - Order status `PAID` check hota hai.  
     - `payments` table update hota hai (`status = paid`).  
     - `subscriptions` update:
       - Monthly/yearly: expiry date extend hoti hai.  
       - Single lead: ek chhoti subscription + inquiry status `viewed`.  
5. `My Payments` page:
   - Saare payments ka list (amount, status, plan, created date).  
6. Admin `Payments` page:
   - Total revenue, success/failure counts, table of all payments.

---

## 7. Support Tickets

### 7.1. User / Trainer / Gym – Support Form

- Sidebar → `Support Team`:
  - Naya, detailed form:
    - **Issue Type**: Billing, Login, KYC, Leads, Technical, Feedback, Other.  
    - **Priority**: Low, Normal, High, Urgent.  
    - Subject + Message.  
    - Optional: Related page/feature, contact phone, attachment (screenshot/PDF).
- Submit par:
  - `SupportTicket` + first `SupportTicketMessage` create hote hain.
  - User ko apna ticket detail view milta hai (chat style).

### 7.2. Admin Support Team

- Admin sidebar → `Support Team`:
  - Tabs: `Open`, `In Progress`, `Resolved`, `All`.  
  - Har ticket pe:
    - Issue type & priority badges.  
    - Status badge, user, created time.  
  - Detail page:
    - Ticket info (issue type, priority, related page, contact phone, attachment link).  
    - Status change (open → in_progress → resolved).  
    - Admin aur user ke beech message thread.

---

## 8. Blocks, Warnings & Registration Cancellation (Admin)

### 8.1. Blocked Users (Inquiry level)

- Admin panel → `Blocked Users`:
  - `InquiryBlock` records (active blocks) list.  
  - Detail page me:
    - Block details, reporter, blocked user, related inquiry.  
    - Actions:
      - **Send Warning**: email + warning log.  
      - **Cancel Registration**:
        - User ke `status = cancelled`.  
        - `registration_cancelled_at`, `registration_cancellation_reason` store.  
        - User ko “Registration Cancelled” email.

### 8.2. Registration Issues Page

- Admin sidebar → `Registration Issues` (humne add kiya):  
  - Dikhata hai:
    - `status = cancelled` users.  
    - Jinko kabhi warning mili ho (inquiry_block_warnings).  
  - Columns: user type, status, warnings count, cancellation reason, last updated.  
  - Action:
    - **View** (user detail).  
    - **Activate** (cancelled user ke liye):
      - `status = active`, cancellation fields null.

---

## 9. Security & Safety Features Summary

- Email OTP verification.  
- KYC review with admin approval for trainers/gyms.  
- Chat reporting (abuse/spam/fake lead).  
- Lead‑level blocking (don’t show blocked chat in lists / All Chats).  
- Admin‐side warnings and registration cancellation with reason.  
- Support tickets with priority & attachments.

---

## 10. Typical Journeys (Short)

- **Customer**:
  1. Register + OTP verify → profile update (optional)  
  2. Search trainer/gym → send inquiry → chat → visit/join  
  3. Agar koi issue: report/block + support ticket.

- **Trainer / Gym Owner**:
  1. Register → email verify → profile complete → KYC approved.  
  2. Leads receive → plan/credits se unlock → chat → customer close.  
  3. Payments & subscriptions manage, support tickets raise, bad users ko block/report.

- **Admin**:
  1. KYC approvals + user management.  
  2. Leads reports, blocks, warnings, cancellations.  
  3. Payments, credit history, support tickets, blogs, etc. manage.

