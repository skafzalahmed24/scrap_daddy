<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Banner;
use App\Models\Order;
use App\Models\ScrapVehicle;
use App\Models\Feedback;
use App\Models\StaticPage;
use App\Models\Faq;
use App\Models\RewardConfiguration;
use App\Models\RewardSetting;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Reward Settings
        RewardSetting::firstOrCreate(
            ['id' => 1],
            ['coin_value_in_rupees' => 0.25]
        );

        // 2. Reward Configurations
        $rewardTiers = [
            ['min_amount' => 100.00, 'max_amount' => 499.00, 'reward_coins' => 50, 'validity_days' => 30, 'status' => 1],
            ['min_amount' => 500.00, 'max_amount' => 1499.00, 'reward_coins' => 150, 'validity_days' => 60, 'status' => 1],
            ['min_amount' => 1500.00, 'max_amount' => 4999.00, 'reward_coins' => 500, 'validity_days' => 90, 'status' => 1],
            ['min_amount' => 5000.00, 'max_amount' => 19999.00, 'reward_coins' => 2000, 'validity_days' => 180, 'status' => 1],
            ['min_amount' => 20000.00, 'max_amount' => 100000.00, 'reward_coins' => 10000, 'validity_days' => 365, 'status' => 1],
        ];
        foreach ($rewardTiers as $tier) {
            RewardConfiguration::updateOrCreate(
                ['min_amount' => $tier['min_amount'], 'max_amount' => $tier['max_amount']],
                $tier
            );
        }

        // 3. Static Pages
        $pages = [
            [
                'slug' => 'help-and-support',
                'title' => 'Help & Support',
                'content' => '<h3>Welcome to Scrap Daddy Support</h3><p>We are dedicated to providing you with seamless doorstep scrap collection services. If you have any inquiries, issues with your pickup, or payment questions, our support team is here to assist you.</p><h4>Contact Details</h4><p>Email: <strong>support@scrapedaddy.com</strong><br>Helpline: <strong>+91 98765 43210</strong><br>Hours: <strong>Mon - Sun: 8:00 AM - 8:00 PM</strong></p>',
            ],
            [
                'slug' => 'privacy-policy',
                'title' => 'Privacy Policy',
                'content' => '<h3>Privacy Policy for Scrap Daddy</h3><p>Your privacy is important to us. This privacy policy explains how Scrap Daddy collects, uses, and safeguards your personal data when you use our website and mobile application.</p>',
            ],
            [
                'slug' => 'terms-and-conditions',
                'title' => 'Terms & Conditions',
                'content' => '<h3>Terms and Conditions</h3><p>By using Scrap Daddy’s website or mobile application, you agree to comply with and be bound by the platform terms and conditions.</p>',
            ],
            [
                'slug' => 'about-us',
                'title' => 'About Us',
                'content' => '<h3>About Scrap Daddy</h3><p>Scrap Daddy is a tech-enabled waste management and doorstep scrap pickup platform. We are on a mission to organize the informal recycling industry, promote environmental sustainability, and ensure maximum value and convenience for households and businesses.</p>',
            ],
        ];
        foreach ($pages as $page) {
            StaticPage::updateOrCreate(['slug' => $page['slug']], $page);
        }

        // 4. FAQs
        $faqs = [
            [
                'question' => 'How does Scrap Daddy doorstep pickup work?',
                'answer' => 'Simply select the scrap items you have, choose your preferred pickup date and time slot, and submit the request. Our verified executive visits your location with a digital weighing machine, calculates the value, pays you instantly, and takes care of the scrap.',
                'status' => 1
            ],
            [
                'question' => 'How is the scrap weighed and priced?',
                'answer' => 'Our pickup executives carry certified, precision digital electronic scales. The rates are updated in real-time according to prevailing recycling market prices, ensuring transparent and fair valuation.',
                'status' => 1
            ],
            [
                'question' => 'When will I receive payment for my scrap?',
                'answer' => 'Payment is transferred instantly to your UPI ID (Google Pay, PhonePe, Paytm), bank account, or paid in cash immediately after weighing on-site.',
                'status' => 1
            ],
            [
                'question' => 'What is the Scrap Vehicle service?',
                'answer' => 'If you have an old, end-of-life two-wheeler, four-wheeler, or commercial vehicle, you can submit a vehicle scrapping request. We assist with legally compliant scrapping, deregistration documentation (RTO certificate), and offer the best scrap value.',
                'status' => 1
            ],
            [
                'question' => 'How do Reward Coins work?',
                'answer' => 'You earn Scrap Daddy reward coins with every completed order. These coins can be redeemed on subsequent pickups to receive cash bonuses or discounts on platform services.',
                'status' => 1
            ],
        ];
        foreach ($faqs as $faq) {
            Faq::firstOrCreate(['question' => $faq['question']], $faq);
        }

        // 5. Banners
        $banners = [
            [
                'uuid' => 'b1000000-0000-0000-0000-000000000001',
                'title' => 'Get Top Value for Your Scrap at Your Doorstep',
                'short_description' => 'Sell your paper, plastics, metals, and appliances with certified digital weighing and instant payout.',
                'uploads' => 'uploads/banners/1781424961_tokMyGxjpA.png',
                'status' => 1,
                'type' => 'web'
            ],
            [
                'uuid' => 'b1000000-0000-0000-0000-000000000002',
                'title' => 'Earn Reward Coins on Every Pickup!',
                'short_description' => 'Get rewarded for responsible recycling. Redeem coins for instant bonuses.',
                'uploads' => 'uploads/banners/1785663471_PlWaquWOQU.png',
                'status' => 1,
                'type' => 'web'
            ],
            [
                'uuid' => 'b1000000-0000-0000-0000-000000000003',
                'title' => 'Scrap Your Old Vehicle Legally & Safely',
                'short_description' => 'Hassle-free vehicle scrappage certificate and government approved recycling.',
                'uploads' => 'requestbanenr.png',
                'status' => 1,
                'type' => 'mobile'
            ],
        ];
        foreach ($banners as $banner) {
            Banner::updateOrCreate(['uuid' => $banner['uuid']], $banner);
        }

        // 6. Categories & Subcategories
        $categoriesData = [
            [
                'uuid' => 'c1000000-0000-0000-0000-000000000001',
                'title' => 'Paper & Cardboard',
                'description' => 'Newspapers, office paper, old textbooks, cartons, magazines, and packaging materials.',
                'image' => 'categories/1781426891_UigZQbzol0.png',
                'status' => 1,
                'subcategories' => [
                    ['uuid' => 's1000000-0000-0000-0000-000000000001', 'name' => 'Newspaper (Raddi)', 'description' => 'Old daily newspapers in English, Hindi, and regional languages. Rate approx ₹14-16/kg.', 'image' => 'subcategories/1781419825_6T5qZvoMG3.png'],
                    ['uuid' => 's1000000-0000-0000-0000-000000000002', 'name' => 'Corrugated Cardboard / Carton', 'description' => 'Clean packaging cartons, e-commerce delivery boxes, cardboard sheets.', 'image' => 'subcategories/1781419847_TSp4N9RxF5.jpeg'],
                    ['uuid' => 's1000000-0000-0000-0000-000000000003', 'name' => 'Old Books & Magazines', 'description' => 'School textbooks, college notebooks, novels, and glossy magazines.', 'image' => 'subcategories/1781430669_RMgna40n13.png'],
                    ['uuid' => 's1000000-0000-0000-0000-000000000004', 'name' => 'Office White Paper', 'description' => 'A4 office documents, letterheads, printer papers, shredded waste paper.', 'image' => 'subcategories/1781430746_kYdY2O1kc3.png'],
                ]
            ],
            [
                'uuid' => 'c1000000-0000-0000-0000-000000000002',
                'title' => 'Plastics & Polymers',
                'description' => 'Plastic bottles, containers, chairs, household plastic items, and packaging wraps.',
                'image' => 'categories/1781428157_gUYrqRL6vL.png',
                'status' => 1,
                'subcategories' => [
                    ['uuid' => 's1000000-0000-0000-0000-000000000005', 'name' => 'Soft Plastic / PET Bottles', 'description' => 'Mineral water bottles, beverage bottles, clean clear plastic containers.', 'image' => 'subcategories/1781430788_r3cNzboEG8.png'],
                    ['uuid' => 's1000000-0000-0000-0000-000000000006', 'name' => 'Hard Plastic / Buckets & Chairs', 'description' => 'Broken plastic chairs, bathroom buckets, storage tubs, crates, HDPE items.', 'image' => 'subcategories/1781430902_yS6qWGhrMG.png'],
                ]
            ],
            [
                'uuid' => 'c1000000-0000-0000-0000-000000000003',
                'title' => 'Metals & Iron',
                'description' => 'Iron scrap, copper wires, aluminium sections, brass, stainless steel, and tin sheets.',
                'image' => 'categories/1781428429_jwQ32pPJvh.png',
                'status' => 1,
                'subcategories' => [
                    ['uuid' => 's1000000-0000-0000-0000-000000000007', 'name' => 'Iron & Heavy Steel', 'description' => 'Pipes, construction rods, grills, angle iron, metal frames, heavy scrap.', 'image' => 'subcategories/1781431012_B2Tdqb0big.png'],
                    ['uuid' => 's1000000-0000-0000-0000-000000000008', 'name' => 'Copper Wire & Pipes', 'description' => 'Pure electrical copper wires, copper tubing, motors, plumbing copper scrap.', 'image' => 'subcategories/1781431083_RRLZUXM1C8.png'],
                    ['uuid' => 's1000000-0000-0000-0000-000000000009', 'name' => 'Aluminium Sections & Cans', 'description' => 'Window frames, utensils, cans, aluminium sheet clippings, ladders.', 'image' => 'subcategories/1781431804_dkuFDW49rg.png'],
                    ['uuid' => 's1000000-0000-0000-0000-000000000010', 'name' => 'Brass & Bronze', 'description' => 'Pooja brassware, antique items, taps, valves, bronze castings.', 'image' => 'subcategories/1781431855_y5fTlWGWBA.png'],
                ]
            ],
            [
                'uuid' => 'c1000000-0000-0000-0000-000000000004',
                'title' => 'E-Waste & Electronics',
                'description' => 'Laptops, mobile phones, CPUs, computer peripherals, cables, and circuit boards.',
                'image' => 'categories/1781429445_I8e0Z270DJ.png',
                'status' => 1,
                'subcategories' => [
                    ['uuid' => 's1000000-0000-0000-0000-000000000011', 'name' => 'Laptops & Computers', 'description' => 'Scrap laptops, desktop towers, monitors, power supplies, keyboards.', 'image' => 'subcategories/1781431902_uBsf8ndplc.png'],
                    ['uuid' => 's1000000-0000-0000-0000-000000000012', 'name' => 'Mobile Phones & Tablets', 'description' => 'Old and damaged smartphones, feature phones, tablets, chargers, batteries.', 'image' => 'subcategories/1781431982_kU3RN2mP5J.png'],
                ]
            ],
            [
                'uuid' => 'c1000000-0000-0000-0000-000000000005',
                'title' => 'Large Appliances',
                'description' => 'Refrigerators, air conditioners, washing machines, microwaves, and water heaters.',
                'image' => 'categories/1781429615_XK6TcjchKe.png',
                'status' => 1,
                'subcategories' => [
                    ['uuid' => 's1000000-0000-0000-0000-000000000013', 'name' => 'Air Conditioner (Split / Window)', 'description' => 'Complete AC units, copper condenser coils, compressor scrap.', 'image' => 'subcategories/1781419825_6T5qZvoMG3.png'],
                    ['uuid' => 's1000000-0000-0000-0000-000000000014', 'name' => 'Refrigerator / Fridge', 'description' => 'Single and double door refrigerators, commercial deep freezers.', 'image' => 'subcategories/1781419847_TSp4N9RxF5.jpeg'],
                    ['uuid' => 's1000000-0000-0000-0000-000000000015', 'name' => 'Washing Machine', 'description' => 'Semi-automatic and fully automatic top/front load washing machines.', 'image' => 'subcategories/1781430669_RMgna40n13.png'],
                ]
            ],
            [
                'uuid' => 'c1000000-0000-0000-0000-000000000006',
                'title' => 'Scrap Vehicles',
                'description' => 'End-of-life two wheelers, four wheelers, auto rickshaws, and automotive parts.',
                'image' => 'categories/1781429874_vqib2zDD4f.png',
                'status' => 1,
                'subcategories' => [
                    ['uuid' => 's1000000-0000-0000-0000-000000000016', 'name' => 'Two Wheeler (Bike / Scooter)', 'description' => 'End of life motorcycles, gearless scooters, mopeds with valid RC copies.', 'image' => 'subcategories/1781430746_kYdY2O1kc3.png'],
                    ['uuid' => 's1000000-0000-0000-0000-000000000017', 'name' => 'Four Wheeler (Car Scrap)', 'description' => 'Accidental, condemned, or 15+ year expired petrol/diesel passenger cars.', 'image' => 'subcategories/1781430788_r3cNzboEG8.png'],
                ]
            ],
            [
                'uuid' => 'c1000000-0000-0000-0000-000000000007',
                'title' => 'Batteries & Glass',
                'description' => 'Inverter batteries, automotive lead-acid batteries, glass bottles, and jars.',
                'image' => 'categories/1781416787_XU3tKW2aF2.jpeg',
                'status' => 1,
                'subcategories' => [
                    ['uuid' => 's1000000-0000-0000-0000-000000000018', 'name' => 'Lead Acid Inverter / Car Battery', 'description' => 'UPS batteries, tubular solar batteries, car & truck batteries. Rate ₹70-85/kg.', 'image' => 'subcategories/1781430902_yS6qWGhrMG.png'],
                ]
            ],
        ];

        foreach ($categoriesData as $cData) {
            $category = Category::updateOrCreate(
                ['uuid' => $cData['uuid']],
                [
                    'title' => $cData['title'],
                    'description' => $cData['description'],
                    'image' => $cData['image'],
                    'status' => $cData['status']
                ]
            );

            foreach ($cData['subcategories'] as $sData) {
                Subcategory::updateOrCreate(
                    ['uuid' => $sData['uuid']],
                    [
                        'category_id' => $category->uuid,
                        'name' => $sData['name'],
                        'description' => $sData['description'],
                        'image' => $sData['image'],
                        'status' => 1
                    ]
                );
            }
        }

        // 7. Users
        $users = [
            [
                'uuid' => 'u1000000-0000-0000-0000-000000000001',
                'full_name' => 'Rahul Sharma',
                'profile_image' => 'profiles/1784906319_Kg4MGtjuKf.jpg',
                'email' => 'rahul.sharma@example.com',
                'phone_number' => '9876543210',
                'password' => Hash::make('password'),
                'pin_code' => '400001',
                'location' => 'A-402, Sea View Towers, Worli, Mumbai',
                'latitude' => 19.01761470,
                'longitude' => 72.81534080,
                'platform_type' => 1,
                'otp' => '123456',
                'is_verified' => 1,
                'status' => 1,
                'reward_coins' => 650,
            ],
            [
                'uuid' => 'u1000000-0000-0000-0000-000000000002',
                'full_name' => 'Priya Patel',
                'profile_image' => null,
                'email' => 'priya.patel@example.com',
                'phone_number' => '9876543211',
                'password' => Hash::make('password'),
                'pin_code' => '380015',
                'location' => 'Flat 12, Sunrise Residency, SG Highway, Ahmedabad',
                'latitude' => 23.02250500,
                'longitude' => 72.57136210,
                'platform_type' => 2,
                'otp' => '123456',
                'is_verified' => 1,
                'status' => 1,
                'reward_coins' => 150,
            ],
            [
                'uuid' => 'u1000000-0000-0000-0000-000000000003',
                'full_name' => 'Amit Kumar',
                'profile_image' => null,
                'email' => 'amit.kumar@example.com',
                'phone_number' => '9876543212',
                'password' => Hash::make('password'),
                'pin_code' => '110001',
                'location' => 'House 56, Sector 14, Connaught Place, New Delhi',
                'latitude' => 28.61393910,
                'longitude' => 77.20902120,
                'platform_type' => 3,
                'otp' => '123456',
                'is_verified' => 1,
                'status' => 1,
                'reward_coins' => 0,
            ],
            [
                'uuid' => 'u1000000-0000-0000-0000-000000000004',
                'full_name' => 'Sneha Reddy',
                'profile_image' => 'profiles/1784906319_Kg4MGtjuKf.jpg',
                'email' => 'sneha.reddy@example.com',
                'phone_number' => '9876543213',
                'password' => Hash::make('password'),
                'pin_code' => '500081',
                'location' => 'Villa 8, Cyber Palm Meadows, Madhapur, Hyderabad',
                'latitude' => 17.44829300,
                'longitude' => 78.39148500,
                'platform_type' => 2,
                'otp' => '123456',
                'is_verified' => 1,
                'status' => 1,
                'reward_coins' => 800,
            ],
            [
                'uuid' => 'u1000000-0000-0000-0000-000000000005',
                'full_name' => 'Vikram Singh',
                'profile_image' => null,
                'email' => 'vikram.singh@example.com',
                'phone_number' => '9876543214',
                'password' => Hash::make('password'),
                'pin_code' => '560001',
                'location' => 'Flat 304, Green Glen Layout, Bellandur, Bangalore',
                'latitude' => 12.97159870,
                'longitude' => 77.59456270,
                'platform_type' => 1,
                'otp' => '123456',
                'is_verified' => 1,
                'status' => 1,
                'reward_coins' => 200,
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(['uuid' => $userData['uuid']], $userData);
        }

        // 8. Orders
        $orders = [
            [
                'id' => 1,
                'user_uuid' => 'u1000000-0000-0000-0000-000000000001',
                'category_uuid' => 'c1000000-0000-0000-0000-000000000001',
                'subcategory_uuid' => 's1000000-0000-0000-0000-000000000001',
                'items' => [
                    ['name' => 'Newspaper (Raddi)', 'quantity' => 30, 'subcategory_uuid' => 's1000000-0000-0000-0000-000000000001'],
                    ['name' => 'Corrugated Cardboard / Carton', 'quantity' => 15, 'subcategory_uuid' => 's1000000-0000-0000-0000-000000000002']
                ],
                'pickup_location' => 'A-402, Sea View Towers, Worli, Mumbai',
                'pickup_date' => '2026-06-22',
                'pickup_time' => '10:00 AM - 12:00 PM',
                'images' => ['subcategories/1781419825_6T5qZvoMG3.png'],
                'notes' => 'Please call 15 minutes before arrival.',
                'status' => 'completed',
                'estimated_pickup_date' => '2026-06-22 10:30:00',
                'total_amount' => 680.00,
                'payment_status' => 'completed',
                'payment_id' => 'PAY_RZP_10001',
                'coins_earned' => 150,
                'coins_redeemed' => 0,
                'available_coins' => 150,
                'coins_expires_at' => now()->addDays(90),
                'discount_applied' => 0.00,
            ],
            [
                'id' => 2,
                'user_uuid' => 'u1000000-0000-0000-0000-000000000001',
                'category_uuid' => 'c1000000-0000-0000-0000-000000000003',
                'subcategory_uuid' => 's1000000-0000-0000-0000-000000000007',
                'items' => [
                    ['name' => 'Iron & Heavy Steel', 'quantity' => 55, 'subcategory_uuid' => 's1000000-0000-0000-0000-000000000007'],
                    ['name' => 'Copper Wire & Pipes', 'quantity' => 5, 'subcategory_uuid' => 's1000000-0000-0000-0000-000000000008']
                ],
                'pickup_location' => 'A-402, Sea View Towers, Worli, Mumbai',
                'pickup_date' => '2026-07-15',
                'pickup_time' => '02:00 PM - 04:00 PM',
                'images' => ['subcategories/1781431012_B2Tdqb0big.png'],
                'notes' => 'Old renovation steel scrap and copper wires.',
                'status' => 'completed',
                'estimated_pickup_date' => '2026-07-15 14:15:00',
                'total_amount' => 3850.00,
                'payment_status' => 'completed',
                'payment_id' => 'PAY_RZP_10002',
                'coins_earned' => 500,
                'coins_redeemed' => 0,
                'available_coins' => 500,
                'coins_expires_at' => now()->addDays(90),
                'discount_applied' => 0.00,
            ],
            [
                'id' => 3,
                'user_uuid' => 'u1000000-0000-0000-0000-000000000002',
                'category_uuid' => 'c1000000-0000-0000-0000-000000000005',
                'subcategory_uuid' => 's1000000-0000-0000-0000-000000000013',
                'items' => [
                    ['name' => 'Air Conditioner (Split / Window)', 'quantity' => 1, 'subcategory_uuid' => 's1000000-0000-0000-0000-000000000013']
                ],
                'pickup_location' => 'Flat 12, Sunrise Residency, SG Highway, Ahmedabad',
                'pickup_date' => '2026-07-28',
                'pickup_time' => '11:00 AM - 01:00 PM',
                'images' => ['subcategories/1781419825_6T5qZvoMG3.png'],
                'notes' => 'Old non-working 1.5 ton split AC outdoor and indoor unit.',
                'status' => 'completed',
                'estimated_pickup_date' => '2026-07-28 11:30:00',
                'total_amount' => 4200.00,
                'payment_status' => 'completed',
                'payment_id' => 'PAY_RZP_10003',
                'coins_earned' => 500,
                'coins_redeemed' => 0,
                'available_coins' => 500,
                'coins_expires_at' => now()->addDays(60),
                'discount_applied' => 0.00,
            ],
            [
                'id' => 4,
                'user_uuid' => 'u1000000-0000-0000-0000-000000000004',
                'category_uuid' => 'c1000000-0000-0000-0000-000000000004',
                'subcategory_uuid' => 's1000000-0000-0000-0000-000000000011',
                'items' => [
                    ['name' => 'Laptops & Computers', 'quantity' => 3, 'subcategory_uuid' => 's1000000-0000-0000-0000-000000000011'],
                    ['name' => 'Mobile Phones & Tablets', 'quantity' => 4, 'subcategory_uuid' => 's1000000-0000-0000-0000-000000000012']
                ],
                'pickup_location' => 'Villa 8, Cyber Palm Meadows, Madhapur, Hyderabad',
                'pickup_date' => '2026-08-12',
                'pickup_time' => '10:00 AM - 12:00 PM',
                'images' => ['subcategories/1781431902_uBsf8ndplc.png'],
                'notes' => 'Office electronics cleanout.',
                'status' => 'completed',
                'estimated_pickup_date' => '2026-08-12 10:45:00',
                'total_amount' => 5500.00,
                'payment_status' => 'completed',
                'payment_id' => 'PAY_RZP_10005',
                'coins_earned' => 2000,
                'coins_redeemed' => 0,
                'available_coins' => 800,
                'coins_expires_at' => now()->addDays(180),
                'discount_applied' => 0.00,
            ],
        ];

        foreach ($orders as $orderData) {
            Order::updateOrCreate(['id' => $orderData['id']], $orderData);
        }

        // 9. Scrap Vehicles
        $scrapVehicles = [
            [
                'id' => 1,
                'user_uuid' => 'u1000000-0000-0000-0000-000000000001',
                'vehicle_type' => 'Two Wheeler',
                'vehicle_number' => 'MH-01-AB-1234',
                'vehicle_brand' => 'Honda',
                'vehicle_model' => 'Activa 3G (2010)',
                'photos' => ['requestbanenr.png'],
                'remark' => 'Engine seized, RC book available for scrap certification.',
                'status' => 'completed',
            ],
            [
                'id' => 2,
                'user_uuid' => 'u1000000-0000-0000-0000-000000000003',
                'vehicle_type' => 'Four Wheeler',
                'vehicle_number' => 'DL-03-CC-5678',
                'vehicle_brand' => 'Maruti Suzuki',
                'vehicle_model' => 'Zen Estilo (2008)',
                'photos' => ['requestbanenr.png'],
                'remark' => '15-year registration expired. Complete vehicle available at Connaught Place.',
                'status' => 'completed',
            ],
            [
                'id' => 3,
                'user_uuid' => 'u1000000-0000-0000-0000-000000000004',
                'vehicle_type' => 'Two Wheeler',
                'vehicle_number' => 'TS-09-XY-9012',
                'vehicle_brand' => 'Bajaj',
                'vehicle_model' => 'Pulsar 150 (2011)',
                'photos' => ['requestbanenr.png'],
                'remark' => 'Accidental frame damage, engine parts intact.',
                'status' => 'pending',
            ]
        ];

        foreach ($scrapVehicles as $vData) {
            ScrapVehicle::updateOrCreate(['id' => $vData['id']], $vData);
        }

        // 10. Feedback
        $feedbacks = [
            [
                'id' => 1,
                'user_uuid' => 'u1000000-0000-0000-0000-000000000001',
                'star_rating' => 5,
                'comment' => 'Super fast and punctual pickup! The pickup boy came with a digital weighing machine and paid immediately via UPI. Highly recommended.',
                'is_approved' => 1
            ],
            [
                'id' => 2,
                'user_uuid' => 'u1000000-0000-0000-0000-000000000002',
                'star_rating' => 5,
                'comment' => 'Disposed of our old AC unit without any hassle. Very polite staff and transparent pricing compared to local scrap dealers.',
                'is_approved' => 1
            ],
            [
                'id' => 3,
                'user_uuid' => 'u1000000-0000-0000-0000-000000000004',
                'star_rating' => 5,
                'comment' => 'Great initiative! Sold all our old office laptops and e-waste. Received certified reward coins too.',
                'is_approved' => 1
            ]
        ];

        foreach ($feedbacks as $fData) {
            Feedback::updateOrCreate(['id' => $fData['id']], $fData);
        }
    }
}
