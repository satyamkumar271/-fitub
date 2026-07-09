<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Gym;
use App\Models\Inquiry;
use App\Models\InquiryBlock;
use App\Models\InquiryMessage;
use App\Models\InquiryReadState;
use App\Models\InquiryReport;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\SupportTicket;
use App\Models\Trainer;
use App\Models\UnlockCreditLog;
use App\Models\User;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FitubDemoSeeder extends Seeder
{
    public function run(): void
    {
        $faker = FakerFactory::create('en_IN');

        if (Payment::where('razorpay_order_id', 'like', 'demo_order_%')->exists()) {
            $this->command?->warn('Fitub demo data already exists (demo_order_* payments found). Skipping.');
            return;
        }

        DB::transaction(function () use ($faker) {
            $admin = User::firstOrCreate(
                ['email' => 'admin@fitub.test'],
                [
                    'name' => 'Admin',
                    'password' => 'password',
                    'user_type' => 'admin',
                    'status' => 'active',
                    'is_verified' => true,
                    'kyc_status' => 'not_required',
                    'email_verified_at' => now(),
                ]
            );

            $recipients = collect();
            for ($i = 1; $i <= 3; $i++) {
                $trainer = User::firstOrCreate(
                    ['email' => "trainer{$i}@fitub.test"],
                    [
                        'name' => "Trainer {$i}",
                        'password' => 'password',
                        'user_type' => 'trainer',
                        'status' => 'active',
                        'is_verified' => true,
                        'kyc_status' => 'approved',
                        'email_verified_at' => now(),
                    ]
                );

                Trainer::firstOrCreate(
                    ['user_id' => $trainer->id],
                    [
                        'phone_number' => $faker->numerify('9#########'),
                        'city' => $faker->city(),
                        'state' => $faker->state(),
                        'website_url' => 'https://example.com',
                        'specialization' => $faker->randomElement(['Weight Loss', 'Strength', 'Yoga', 'Nutrition']),
                        'experience' => $faker->numberBetween(1, 12),
                        'certifications' => null,
                    ]
                );

                $recipients->push($trainer);
            }

            for ($i = 1; $i <= 3; $i++) {
                $gym = User::firstOrCreate(
                    ['email' => "gym{$i}@fitub.test"],
                    [
                        'name' => "Gym Owner {$i}",
                        'password' => 'password',
                        'user_type' => 'gymowner',
                        'status' => 'active',
                        'is_verified' => true,
                        'kyc_status' => 'approved',
                        'email_verified_at' => now(),
                    ]
                );

                Gym::firstOrCreate(
                    ['user_id' => $gym->id],
                    [
                        'gym_name' => "Fitub Gym {$i}",
                        'gym_phone_number' => $faker->numerify('9#########'),
                        'gym_email' => $gym->email,
                        'gym_website_url' => 'https://example.com',
                        'address_city' => $faker->city(),
                        'address_state' => $faker->state(),
                        'address_pincode' => $faker->postcode(),
                        'total_members' => $faker->numberBetween(50, 5000),
                    ]
                );

                $recipients->push($gym);
            }

            $customers = collect();
            for ($i = 1; $i <= 8; $i++) {
                $customer = User::firstOrCreate(
                    ['email' => "customer{$i}@fitub.test"],
                    [
                        'name' => "Customer {$i}",
                        'password' => 'password',
                        'user_type' => 'customer',
                        'status' => 'active',
                        'is_verified' => false,
                        'kyc_status' => 'not_required',
                        'email_verified_at' => now(),
                    ]
                );

                Customer::firstOrCreate(
                    ['user_id' => $customer->id],
                    [
                        'age' => $faker->numberBetween(18, 55),
                        'weight' => $faker->randomFloat(1, 45, 110),
                        'height' => $faker->randomFloat(1, 145, 195),
                        'goal' => $faker->sentence(8),
                        'city' => $faker->city(),
                        'state' => $faker->state(),
                    ]
                );

                $customers->push($customer);
            }

            // Payments + subscriptions for plan testing (pro/business)
            $plans = [
                ['type' => 'pro', 'days' => 30, 'base' => 1100.85],
                ['type' => 'business', 'days' => 365, 'base' => 4236.44],
            ];

            foreach ($recipients as $idx => $recipient) {
                $plan = $plans[$idx % count($plans)];
                $gstRate = 18.0;
                $gstAmount = round($plan['base'] * ($gstRate / 100), 2);
                $amount = round($plan['base'] + $gstAmount, 2);

                $payment = Payment::create([
                    'user_id' => $recipient->id,
                    'plan_name' => $plan['type'],
                    'amount' => $amount,
                    'base_amount' => $plan['base'],
                    'gst_rate' => $gstRate,
                    'gst_amount' => $gstAmount,
                    'currency' => 'INR',
                    'status' => 'paid',
                    'razorpay_order_id' => 'demo_order_' . Str::uuid(),
                    'razorpay_payment_id' => 'demo_pay_' . Str::uuid(),
                    'razorpay_signature' => 'demo_sig_' . Str::random(16),
                    'context_type' => 'subscription',
                    'context_id' => null,
                    'meta' => ['demo' => true],
                ]);

                $expiresAt = match ($idx % 4) {
                    0 => now()->addDays(2),   // expiring soon
                    1 => now()->addDays(25),
                    2 => now()->subDays(5),   // expired recently
                    default => now()->addDays($plan['days']),
                };

                Subscription::create([
                    'user_id' => $recipient->id,
                    'plan_type' => $plan['type'],
                    'leads_remaining' => null,
                    'expires_at' => $expiresAt,
                ]);
            }

            // Starter credits + logs
            foreach ($recipients->take(4) as $recipient) {
                $gstRate = 18.0;
                $base = 168.64;
                $gstAmount = round($base * ($gstRate / 100), 2);
                $amount = round($base + $gstAmount, 2);

                $payment = Payment::create([
                    'user_id' => $recipient->id,
                    'plan_name' => 'starter',
                    'amount' => $amount,
                    'base_amount' => $base,
                    'gst_rate' => $gstRate,
                    'gst_amount' => $gstAmount,
                    'currency' => 'INR',
                    'status' => 'paid',
                    'razorpay_order_id' => 'demo_order_' . Str::uuid(),
                    'razorpay_payment_id' => 'demo_pay_' . Str::uuid(),
                    'razorpay_signature' => 'demo_sig_' . Str::random(16),
                    'context_type' => 'credits_pack',
                    'context_id' => null,
                    'meta' => ['demo' => true],
                ]);

                $recipient->refresh();
                $recipient->unlock_credits = (int) ($recipient->unlock_credits ?? 0) + 5;
                $recipient->save();

                UnlockCreditLog::create([
                    'user_id' => $recipient->id,
                    'delta' => 5,
                    'balance_after' => (int) $recipient->unlock_credits,
                    'source_type' => 'starter_pack',
                    'source_id' => $payment->id,
                    'note' => 'Demo: Starter pack credits purchased',
                    'created_by' => $admin->id,
                ]);
            }

            // Inquiries (registered + guest), unlocks, chat messages
            $services = ['Online Coaching', 'Personal Training', 'Diet Plan', 'Gym Membership', 'Gym Tour'];
            $createdInquiries = collect();

            foreach ($customers as $customer) {
                for ($j = 0; $j < 2; $j++) {
                    $recipient = $recipients->random();
                    $inquiry = Inquiry::create([
                        'user_id' => $customer->id,
                        'recipient_id' => $recipient->id,
                        'service_needed' => $faker->randomElement($services),
                        'message' => $faker->sentence(12),
                        'status' => $faker->randomElement(['pending', 'forwarded', 'viewed']),
                    ]);

                    InquiryMessage::create([
                        'inquiry_id' => $inquiry->id,
                        'sender_id' => $customer->id,
                        'message' => $inquiry->message,
                    ]);

                    if ($faker->boolean(60)) {
                        InquiryMessage::create([
                            'inquiry_id' => $inquiry->id,
                            'sender_id' => $recipient->id,
                            'message' => $faker->sentence(10),
                        ]);
                    }

                    InquiryReadState::updateOrCreate(
                        ['inquiry_id' => $inquiry->id, 'user_id' => $customer->id],
                        ['last_read_at' => now()->subMinutes($faker->numberBetween(1, 300))]
                    );

                    InquiryReadState::updateOrCreate(
                        ['inquiry_id' => $inquiry->id, 'user_id' => $recipient->id],
                        ['last_read_at' => now()->subMinutes($faker->numberBetween(1, 300))]
                    );

                    $createdInquiries->push($inquiry);
                }
            }

            for ($g = 0; $g < 6; $g++) {
                $recipient = $recipients->random();
                $inquiry = Inquiry::create([
                    'user_id' => null,
                    'recipient_id' => $recipient->id,
                    'guest_name' => $faker->name(),
                    'guest_email' => 'guest' . Str::lower(Str::random(6)) . '@mailinator.com',
                    'guest_phone' => $faker->numerify('9#########'),
                    'service_needed' => $faker->randomElement($services),
                    'message' => $faker->sentence(12),
                    'status' => $faker->randomElement(['pending', 'forwarded', 'viewed']),
                ]);

                $createdInquiries->push($inquiry);
            }

            // Simulate some credit-based unlocks (consume credits + set inquiry viewed)
            $unlockers = $recipients->filter(fn (User $u) => (int) ($u->unlock_credits ?? 0) > 0)->values();
            foreach ($createdInquiries->where('status', '!=', 'viewed')->take(6) as $inquiry) {
                /** @var \App\Models\User|null $unlocker */
                $unlocker = $unlockers->firstWhere('id', $inquiry->recipient_id);
                if (!$unlocker) {
                    continue;
                }

                $unlocker->refresh();
                if ((int) ($unlocker->unlock_credits ?? 0) <= 0) {
                    continue;
                }

                $unlocker->unlock_credits = (int) $unlocker->unlock_credits - 1;
                $unlocker->save();

                $inquiry->status = 'viewed';
                $inquiry->save();

                UnlockCreditLog::create([
                    'user_id' => $unlocker->id,
                    'delta' => -1,
                    'balance_after' => (int) $unlocker->unlock_credits,
                    'source_type' => 'lead_unlock',
                    'source_id' => $inquiry->id,
                    'note' => 'Demo: Lead unlocked using credit',
                    'created_by' => $admin->id,
                ]);
            }

            // Reports + blocks
            $sampleInquiry = $createdInquiries->firstWhere('user_id', '!=', null);
            if ($sampleInquiry) {
                $customer = User::find($sampleInquiry->user_id);
                $recipient = User::find($sampleInquiry->recipient_id);

                if ($customer && $recipient) {
                    InquiryReport::create([
                        'inquiry_id' => $sampleInquiry->id,
                        'reporter_id' => $recipient->id,
                        'reported_user_id' => $customer->id,
                        'reason' => 'spam',
                        'details' => 'Demo report for testing.',
                        'status' => 'open',
                        'compensation_requested' => false,
                    ]);

                    InquiryBlock::firstOrCreate([
                        'inquiry_id' => $sampleInquiry->id,
                        'blocker_id' => $recipient->id,
                        'blocked_user_id' => $customer->id,
                    ], [
                        'reason' => 'Demo block for testing.',
                        'active' => true,
                    ]);
                }
            }

            // Support ticket (basic)
            $ticketUser = $customers->first();
            if ($ticketUser) {
                SupportTicket::create([
                    'user_id' => $ticketUser->id,
                    'subject' => 'Demo: Need help with my inquiry',
                    'message' => 'This is a demo support ticket created by seeder.',
                    'status' => 'open',
                ]);
            }
        });

        $this->command?->info('Fitub demo data seeded. Logins: admin@fitub.test / password, customer1@fitub.test / password, trainer1@fitub.test / password, gym1@fitub.test / password');
    }
}
