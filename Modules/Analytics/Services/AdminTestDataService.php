<?php

namespace Modules\Analytics\Services;

use Core\database\DB;
use RuntimeException;

class AdminTestDataService {
    private const USERNAME_PREFIX = 'test_academy_manager_';
    private const TOTAL = 50;

    public function seedAcademyManagers(): array {
        return transaction(function () {
            $passwordHash = password_hash('123456789', PASSWORD_DEFAULT);
            $created = 0;
            $updated = 0;

            foreach ($this->people() as $index => $person) {
                $number = $index + 1;
                $username = self::USERNAME_PREFIX . sprintf('%02d', $number);
                $existing = DB::table('users')->where('username', $username)->first();
                $createdAt = $existing['created_at'] ?? date('Y-m-d H:i:s', strtotime('-' . (540 - $index * 9) . ' days'));
                $values = $this->userValues($person, $index, $createdAt, $passwordHash);

                if ($existing) {
                    $userId = (int)$existing['user_id'];
                    DB::table('users')->where('user_id', $userId)->update($values);
                    $updated++;
                } else {
                    $userId = DB::table('users')->insertGetId(['username' => $username] + $values);
                    if (!$userId) throw new RuntimeException('ایجاد کاربر آزمایشی شماره ' . $number . ' ناموفق بود.');
                    $created++;
                }

                DB::table('users')->where('user_id', $userId)->update([
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]);
                $this->setFullNameTranslation($userId, $person['name'], $createdAt);
                $this->syncAddresses($userId, $index, $createdAt);
                $this->syncContacts($userId, $index, $username, $createdAt);
            }

            return [
                'created' => $created,
                'updated' => $updated,
                'total' => self::TOTAL,
                'message' => "تست مدیران آموزشگاه تکمیل شد: {$created} کاربر ایجاد و {$updated} کاربر آزمایشی همگام‌سازی شد.",
            ];
        });
    }

    public function statistics(): array {
        $users = DB::table('users')->whereRaw("username LIKE '" . self::USERNAME_PREFIX . "%'")->get();
        $userIds = array_map(fn(array $user) => (int)$user['user_id'], $users);
        $stats = [
            'total' => count($users),
            'addresses' => $userIds ? DB::table('user_addresses')->whereIn('user_id', $userIds)->count() : 0,
            'contacts' => $userIds ? DB::table('user_contacts')->whereIn('user_id', $userIds)->count() : 0,
            'pending' => 0, 'approved' => 0, 'other' => 0,
        ];
        foreach ($users as $user) {
            $status = $user['status'] ?? '';
            if ($status === 'pending') $stats['pending']++;
            elseif ($status === 'approved') $stats['approved']++;
            else $stats['other']++;
        }
        return $stats;
    }

    public function deleteAcademyManagers(): array {
        return transaction(function () {
            $users = DB::table('users')->whereRaw("username LIKE '" . self::USERNAME_PREFIX . "%'")->get();
            $userIds = array_map(fn(array $user) => (int)$user['user_id'], $users);
            if (!$userIds) {
                return ['deleted' => 0, 'message' => 'هیچ مدیر آموزشگاه آزمایشی برای حذف وجود ندارد.'];
            }

            $addresses = DB::table('user_addresses')->whereIn('user_id', $userIds)->get();
            $addressIds = array_map(fn(array $address) => (int)$address['address_id'], $addresses);
            if ($addressIds) {
                DB::table('translations')->where('table_name', 'user_addresses')->whereIn('table_id', $addressIds)->delete();
                DB::table('user_addresses')->whereIn('address_id', $addressIds)->delete();
            }
            $contacts = DB::table('user_contacts')->whereIn('user_id', $userIds)->get();
            $contactIds = array_map(fn(array $contact) => (int)$contact['user_contact_id'], $contacts);
            if ($contactIds) {
                DB::table('translations')->where('table_name', 'user_contacts')->whereIn('table_id', $contactIds)->delete();
                DB::table('user_contacts')->whereIn('user_contact_id', $contactIds)->delete();
            }
            DB::table('translations')->where('table_name', 'users')->whereIn('table_id', $userIds)->delete();
            DB::table('users')->whereIn('user_id', $userIds)->delete();

            return [
                'deleted' => count($userIds),
                'message' => count($userIds) . ' مدیر آموزشگاه آزمایشی و ترجمه‌های مرتبط با موفقیت حذف شدند.',
            ];
        });
    }

    private function syncContacts(int $userId, int $userIndex, string $username, string $registeredAt): void {
        $templates = $this->contactTemplates($userIndex, $username);
        $contactCount = ($userIndex % 10) + 1;
        $seenModes = [];
        $registrationTimestamp = strtotime($registeredAt);

        for ($contactIndex = 0; $contactIndex < $contactCount; $contactIndex++) {
            $contact = $templates[$contactIndex];
            $mode = $contact['mode'];
            $isMain = !isset($seenModes[$mode]);
            $seenModes[$mode] = true;
            $contactCreatedAt = date('Y-m-d H:i:s', $registrationTimestamp + ($contactIndex + 1) * 6 * 3600 + ($userIndex % 5) * 3600);
            $approvedAt = date('Y-m-d H:i:s', strtotime($contactCreatedAt) + (($contactIndex % 12) + 1) * 3600);
            $lastCalledAt = date('Y-m-d H:i:s', strtotime($approvedAt) + (($contactIndex % 14) + 1) * 86400);
            $updatedAt = date('Y-m-d H:i:s', strtotime($lastCalledAt) + (($contactIndex % 6) + 1) * 3600);
            $contactId = $this->findContactByTranslatedValue($userId, $contact['value']);
            $values = [
                'mode' => $mode,
                'platform' => $contact['platform'],
                'priority' => $isMain ? 'primary' : $contact['priority'],
                'is_main' => $isMain ? 1 : 0,
                'status' => $contact['status'],
                'last_called_at' => $lastCalledAt,
                'created_at' => $contactCreatedAt,
                'created_by' => $userId,
                'updated_at' => $updatedAt,
                'updated_by' => $userId,
                'approved_at' => $approvedAt,
                'approved_by' => $userId,
                'deleted_at' => null,
                'deleted_by' => null,
            ];

            if ($contactId) {
                DB::table('user_contacts')->where('user_contact_id', $contactId)->update($values);
            } else {
                $contactId = DB::table('user_contacts')->insertGetId(['user_id' => $userId] + $values);
                if (!$contactId) throw new RuntimeException('ایجاد راه ارتباطی آزمایشی کاربر ناموفق بود.');
            }
            $this->setContactTranslation($contactId, $userId, 'value', $contact['value'], $contactCreatedAt, $updatedAt, $approvedAt);
            $this->setContactTranslation($contactId, $userId, 'note', $contact['note'], $contactCreatedAt, $updatedAt, $approvedAt);
        }
    }

    private function findContactByTranslatedValue(int $userId, string $value): int {
        $translations = DB::table('translations')->where('table_name', 'user_contacts')->where('locale', 'fa')
            ->where('field', 'value')->where('value', $value)->get();
        foreach ($translations as $translation) {
            $contact = DB::table('user_contacts')->where('user_contact_id', (int)$translation['table_id'])->where('user_id', $userId)->first();
            if ($contact) return (int)$contact['user_contact_id'];
        }
        return 0;
    }

    private function setContactTranslation(int $contactId, int $userId, string $field, string $value, string $createdAt, string $updatedAt, string $approvedAt): void {
        $translation = DB::table('translations')->where('table_name', 'user_contacts')->where('table_id', $contactId)
            ->where('locale', 'fa')->where('field', $field)->first();
        $values = [
            'code' => null, 'value' => $value, 'version' => 1,
            'created_at' => $createdAt, 'created_by' => $userId,
            'updated_at' => $updatedAt, 'updated_by' => $userId,
            'approved_at' => $approvedAt, 'approved_by' => $userId,
            'deleted_at' => null, 'deleted_by' => null,
        ];
        if ($translation) {
            DB::table('translations')->where('translation_id', (int)$translation['translation_id'])->update($values);
            return;
        }
        $translationId = DB::table('translations')->insertGetId([
            'table_name' => 'user_contacts', 'table_id' => $contactId, 'locale' => 'fa', 'field' => $field,
        ] + $values);
        if (!$translationId) throw new RuntimeException('ثبت ترجمه راه ارتباطی آزمایشی ناموفق بود.');
    }

    private function contactTemplates(int $userIndex, string $username): array {
        $number = $userIndex + 1;
        return [
            ['mode'=>$userIndex % 2 === 0 ? 'phone' : 'email','platform'=>'other','value'=>$userIndex % 2 === 0 ? sprintf('0991%07d', 1000000 + $number) : sprintf('sornaz.academy.manager%02d@gmail.com', $number),'priority'=>'primary','status'=>'active','note'=>'راه ارتباطی اصلی کاربر؛ کد تأیید یک‌بارمصرف با موفقیت ثبت شده است.'],
            ['mode'=>$userIndex % 2 === 0 ? 'email' : 'phone','platform'=>'other','value'=>$userIndex % 2 === 0 ? sprintf('sornaz.manager%02d@gmail.com', $number) : sprintf('0912%07d', 5000000 + $number),'priority'=>'support','status'=>'active','note'=>'راه ارتباطی پشتیبان برای زمان‌هایی که مورد اصلی در دسترس نیست.'],
            ['mode'=>'social','platform'=>'instagram','value'=>'https://instagram.com/' . $username,'priority'=>'secondary','status'=>'active','note'=>'صفحه عمومی اینستاگرام؛ پیام‌های کاری در ساعات اداری بررسی می‌شوند.'],
            ['mode'=>'social','platform'=>'telegram','value'=>'https://t.me/' . $username,'priority'=>'support','status'=>'active','note'=>'شناسه تلگرام برای پشتیبانی و ارسال فایل‌های آموزشی.'],
            ['mode'=>'phone','platform'=>'other','value'=>sprintf('0935%07d', 6000000 + $number),'priority'=>'emergency','status'=>'active','note'=>'شماره تماس اضطراری؛ فقط برای موارد فوری استفاده شود.'],
            ['mode'=>'social','platform'=>'website','value'=>'https://' . $username . '.sornaz.test','priority'=>'secondary','status'=>'active','note'=>'وب‌سایت آزمایشی معرفی مدیر و برنامه‌های آموزشگاه.'],
            ['mode'=>'email','platform'=>'other','value'=>sprintf('%s.office@gmail.com', $username),'priority'=>'ledger','status'=>'active','note'=>'ایمیل امور اداری و دریافت اسناد و صورت‌حساب‌ها.'],
            ['mode'=>'social','platform'=>'whats-app','value'=>'https://wa.me/98' . substr(sprintf('0919%07d', 7000000 + $number), 1),'priority'=>'support','status'=>'active','note'=>'واتساپ کاری؛ تماس صوتی فقط با هماهنگی قبلی انجام شود.'],
            ['mode'=>'social','platform'=>'linkedin','value'=>'https://linkedin.com/in/' . $username,'priority'=>'other','status'=>'inactive','note'=>'پروفایل حرفه‌ای قدیمی است و ممکن است با تأخیر به‌روزرسانی شود.'],
            ['mode'=>'social','platform'=>'google-meet','value'=>'https://meet.google.com/snz-' . sprintf('%04d', $number) . '-mgr','priority'=>'other','status'=>'deactive','note'=>'لینک جلسه آنلاین رزرو؛ در حال حاضر فقط با تعیین وقت قبلی فعال می‌شود.'],
        ];
    }

    private function syncAddresses(int $userId, int $userIndex, string $registeredAt): void {
        $locations = $this->locations();
        $addressCount = ($userIndex % 3) + 1;
        $registrationTimestamp = strtotime($registeredAt);

        for ($addressIndex = 0; $addressIndex < $addressCount; $addressIndex++) {
            $location = $locations[($userIndex * 3 + $addressIndex) % count($locations)];
            $province = DB::table('world_iran_provinces')->where('province_name', $location['province'])->first();
            if (!$province) throw new RuntimeException('استان آدرس آزمایشی یافت نشد: ' . $location['province']);
            $county = DB::table('world_iran_counties')
                ->where('county_name', $location['county'])
                ->where('province_id', (int)$province['province_id'])
                ->first();
            if (!$county) throw new RuntimeException('شهرستان آدرس آزمایشی با استان انتخاب‌شده تطابق ندارد: ' . $location['county']);

            $addressCreatedAt = date('Y-m-d H:i:s', $registrationTimestamp + ($addressIndex + 1) * 86400 + ($userIndex % 12) * 3600);
            $addressUpdatedAt = date('Y-m-d H:i:s', strtotime($addressCreatedAt) + (($addressIndex + 1) * 5) * 3600);
            $existing = DB::table('user_addresses')->where('user_id', $userId)->where('postal_code', $location['postal_code'])->first();
            $values = [
                'country_id' => 0,
                'province_id' => (int)$province['province_id'],
                'county_id' => (int)$county['county_id'],
                'is_main' => $addressIndex === 0 ? 1 : 0,
                'latitude' => $location['latitude'],
                'longitude' => $location['longitude'],
                'postal_code' => $location['postal_code'],
                'created_at' => $addressCreatedAt,
                'created_by' => $userId,
                'updated_at' => $addressUpdatedAt,
                'updated_by' => $userId,
                'deleted_at' => null,
                'deleted_by' => null,
            ];

            if ($existing) {
                $addressId = (int)$existing['address_id'];
                DB::table('user_addresses')->where('address_id', $addressId)->update($values);
            } else {
                $addressId = DB::table('user_addresses')->insertGetId(['user_id' => $userId] + $values);
                if (!$addressId) throw new RuntimeException('ایجاد آدرس آزمایشی کاربر ناموفق بود.');
            }

            $this->setAddressTranslation($addressId, $userId, 'address', $location['address'], $addressCreatedAt, $addressUpdatedAt);
            $this->setAddressTranslation($addressId, $userId, 'note', $location['note'], $addressCreatedAt, $addressUpdatedAt);
        }
    }

    private function setAddressTranslation(int $addressId, int $userId, string $field, string $value, string $createdAt, string $updatedAt): void {
        $translation = DB::table('translations')->where('table_name', 'user_addresses')->where('table_id', $addressId)
            ->where('locale', 'fa')->where('field', $field)->first();
        $values = [
            'code' => null, 'value' => $value, 'version' => 1,
            'created_at' => $createdAt, 'created_by' => $userId,
            'updated_at' => $updatedAt, 'updated_by' => $userId,
            'deleted_at' => null, 'deleted_by' => null,
        ];
        if ($translation) {
            DB::table('translations')->where('translation_id', (int)$translation['translation_id'])->update($values);
            return;
        }
        $translationId = DB::table('translations')->insertGetId([
            'table_name' => 'user_addresses', 'table_id' => $addressId, 'locale' => 'fa', 'field' => $field,
        ] + $values);
        if (!$translationId) throw new RuntimeException('ثبت ترجمه آدرس آزمایشی ناموفق بود.');
    }

    private function locations(): array {
        return [
            ['province'=>'تهران','county'=>'تهران','address'=>'تهران، بزرگراه شیخ فضل‌الله نوری، ورودی بزرگراه شهید همت، برج میلاد','latitude'=>35.7448416,'longitude'=>51.3753212,'postal_code'=>'1449614531','note'=>'نشانی اصلی آزمایشی؛ مراجعه حضوری بهتر است پیش از ساعت ۱۸ هماهنگ شود.'],
            ['province'=>'فارس','county'=>'شیراز','address'=>'شیراز، بلوار گلستان، حدفاصل چهارراه ادبیات و چهارراه حافظیه، آرامگاه حافظ','latitude'=>29.6259365,'longitude'=>52.5585667,'postal_code'=>'7136419151','note'=>'نشانی دوم آزمایشی در محدوده گردشگری؛ در روزهای تعطیل احتمال شلوغی وجود دارد.'],
            ['province'=>'اصفهان','county'=>'اصفهان','address'=>'اصفهان، میدان امام حسین، خیابان سپه، میدان نقش جهان','latitude'=>32.6573073,'longitude'=>51.6775612,'postal_code'=>'8146414848','note'=>'محل در محدوده تاریخی است و دسترسی خودرو در بعضی ساعت‌ها محدود می‌شود.'],
            ['province'=>'آذربایجان شرقی','county'=>'تبریز','address'=>'تبریز، محله ششگلان، خیابان ثقةالاسلام، جنب خیابان عارف، مقبره‌الشعرا','latitude'=>38.0820297,'longitude'=>46.2919108,'postal_code'=>'5138663411','note'=>'نشانی اجاره‌ای آزمایشی؛ ممکن است بین ساعت ۱۳ تا ۱۵ پاسخ‌گویی حضوری انجام نشود.'],
            ['province'=>'خراسان رضوی','county'=>'مشهد','address'=>'مشهد، خیابان امام رضا، میدان بیت‌المقدس، ورودی باب‌الرضا حرم مطهر رضوی','latitude'=>36.2879029,'longitude'=>59.6157291,'postal_code'=>'9137913316','note'=>'به علت محدودیت ترافیکی مرکز شهر، استفاده از حمل‌ونقل عمومی پیشنهاد می‌شود.'],
            ['province'=>'گیلان','county'=>'رشت','address'=>'رشت، میدان شهرداری، مجموعه تاریخی شهرداری رشت','latitude'=>37.2759338,'longitude'=>49.5883064,'postal_code'=>'4136934364','note'=>'این نشانی برای تحویل مرسوله در ساعات اداری مناسب‌تر است.'],
            ['province'=>'یزد','county'=>'یزد','address'=>'یزد، خیابان امام خمینی، میدان امیرچخماق، مجموعه تاریخی امیرچخماق','latitude'=>31.8972362,'longitude'=>54.3686977,'postal_code'=>'8916736918','note'=>'نشانی در بافت تاریخی قرار دارد؛ پیش از مراجعه تلفنی هماهنگ شود.'],
            ['province'=>'کرمان','county'=>'کرمان','address'=>'کرمان، میدان ارگ، بازار گنجعلی‌خان، مجموعه گنجعلی‌خان','latitude'=>30.2924087,'longitude'=>57.0671107,'postal_code'=>'7616914111','note'=>'دسترسی مستقیم خودرو تا ورودی بازار ممکن نیست و بخشی از مسیر پیاده است.'],
            ['province'=>'همدان','county'=>'همدان','address'=>'همدان، میدان بوعلی سینا، آرامگاه بوعلی سینا','latitude'=>34.7988575,'longitude'=>48.5146239,'postal_code'=>'6516738695','note'=>'خانه آزمایشی نزدیک میدان است؛ ممکن است عصرها کسی در محل حضور نداشته باشد.'],
            ['province'=>'خوزستان','county'=>'اهواز','address'=>'اهواز، بلوار ساحلی شرقی، حدفاصل خیابان سلمان فارسی و میدان شهدا، پل سفید','latitude'=>31.3282914,'longitude'=>48.6706183,'postal_code'=>'6135714387','note'=>'برای ملاقات حضوری، ساعات خنک‌تر روز انتخاب شود و هماهنگی قبلی انجام گیرد.'],
        ];
    }

    private function userValues(array $person, int $index, string $createdAt, string $passwordHash): array {
        $createdTimestamp = strtotime($createdAt);
        $status = $this->statusFor($index);
        $approvedBySiteAdmin = $status === 'approved' && $index >= 20 && $index < 32;
        $phoneVerifiedAt = date('Y-m-d H:i:s', $createdTimestamp + (($index % 48) + 1) * 3600);
        $lastLoginAt = date('Y-m-d H:i:s', min(time(), $createdTimestamp + (($index % 90) + 4) * 86400));
        $updatedAt = date('Y-m-d H:i:s', min(time(), $createdTimestamp + (($index % 72) + 1) * 3600));
        $approvedAt = $approvedBySiteAdmin
            ? date('Y-m-d H:i:s', $createdTimestamp + (5 * 3600) + (($index * 19) % 235) * 3600)
            : null;

        return [
            'email' => sprintf('sornaz.academy.manager%02d@gmail.com', $index + 1),
            'phone' => sprintf('0991%07d', 1000000 + $index + 1),
            'phone_verified_at' => $phoneVerifiedAt,
            'last_login_at' => $lastLoginAt,
            'last_login_ip' => sprintf('185.51.%d.%d', 20 + ($index % 20), 10 + $index),
            'password' => $passwordHash,
            'national_code' => $this->nationalCode(730000001 + $index),
            'gender' => $person['gender'],
            'type' => 'human',
            'status' => $status,
            'locale' => 'fa',
            'timezone' => 'Asia/Tehran',
            'avatar_file_id' => null,
            'visibility' => $index < 30 ? 'public' : ($index < 40 ? 'private' : 'unlisted'),
            'birthday' => $this->birthdayFor($index),
            'register_time' => $createdAt,
            'register_method' => ['phone', 'email', 'google'][$index % 3],
            'created_at' => $createdAt,
            'created_by' => null,
            'updated_at' => $updatedAt,
            'updated_by' => null,
            'approved_at' => $approvedAt,
            'approved_by' => $approvedBySiteAdmin ? 1 : null,
            'deleted_at' => null,
            'deleted_by' => null,
        ];
    }

    private function setFullNameTranslation(int $userId, string $fullName, string $createdAt): void {
        $translation = DB::table('translations')
            ->where('table_name', 'users')->where('table_id', $userId)
            ->where('locale', 'fa')->where('field', 'full_name')->first();
        $values = [
            'code' => null,
            'value' => $fullName,
            'version' => 1,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => $userId,
            'deleted_at' => null,
            'deleted_by' => null,
        ];
        if ($translation) {
            DB::table('translations')->where('translation_id', (int)$translation['translation_id'])->update($values + [
                'created_by' => $userId,
            ]);
            return;
        }
        $translationId = DB::table('translations')->insertGetId([
            'table_name' => 'users', 'table_id' => $userId, 'locale' => 'fa', 'field' => 'full_name',
            'created_at' => $createdAt, 'created_by' => $userId,
        ] + $values);
        if (!$translationId) throw new RuntimeException('ثبت ترجمه نام کاربر آزمایشی ناموفق بود.');
    }

    private function statusFor(int $index): string {
        if ($index < 20) return 'pending';
        if ($index < 40) return 'approved';
        if ($index < 44) return 'rejected';
        if ($index < 47) return 'inactive';
        return 'banned';
    }

    private function birthdayFor(int $index): string {
        $ages = [25, 27, 29, 61, 65, 70, 75, 80, 63, 68];
        $age = $index < 40 ? 30 + (($index * 7) % 31) : $ages[$index - 40];
        return date('Y-m-d', strtotime('-' . $age . ' years -' . (($index * 17) % 330) . ' days'));
    }

    private function nationalCode(int $base): string {
        $digits = str_split(sprintf('%09d', $base));
        $sum = 0;
        foreach ($digits as $index => $digit) $sum += (int)$digit * (10 - $index);
        $remainder = $sum % 11;
        $checkDigit = $remainder < 2 ? $remainder : 11 - $remainder;
        return implode('', $digits) . $checkDigit;
    }

    private function people(): array {
        $maleFirstNames = ['علی', 'رضا', 'امیر', 'حسین', 'مهدی', 'محمد', 'سعید', 'آرش', 'پویان', 'کیوان', 'فرهاد', 'بهرام', 'نوید', 'بابک', 'کامران', 'کوروش', 'داریوش', 'سامان', 'رامین', 'شهاب', 'میلاد', 'محسن', 'مسعود', 'یاسر', 'جواد'];
        $femaleFirstNames = ['مریم', 'سارا', 'نگار', 'الهام', 'نرگس', 'لیلا', 'مهسا', 'نازنین', 'شبنم', 'پرستو', 'سپیده', 'ترانه', 'آزاده', 'سمیرا', 'بهاره', 'غزل', 'حدیث', 'رویا', 'مینا', 'شیوا', 'الهه', 'زهرا', 'فاطمه', 'ریحانه', 'هانیه'];
        $lastNames = ['محمدی', 'احمدی', 'رضایی', 'کریمی', 'حسینی', 'مرادی', 'قاسمی', 'اکبری', 'صادقی', 'نوری'];
        $people = [];
        for ($i = 0; $i < 25; $i++) {
            $people[] = ['name' => $maleFirstNames[$i] . ' ' . $lastNames[$i % 10], 'gender' => 'male'];
            $people[] = ['name' => $femaleFirstNames[$i] . ' ' . $lastNames[($i + 5) % 10], 'gender' => 'female'];
        }
        return $people;
    }
}
