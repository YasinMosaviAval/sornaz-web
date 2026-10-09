<?php
namespace Modules\Analytics\Controllers\Web;

use Core\database\DB;
use Core\http\ResponseFactory;
use Modules\Analytics\Services\AdminGalleryService;
use Modules\Analytics\Services\AdminSettingService;
use Modules\Analytics\Services\PersonalPanelDataService;
use RuntimeException;
use Throwable;

final class PersonalPanelDataController
{
    public function __construct(private PersonalPanelDataService $data, private AdminGalleryService $gallery, private AdminSettingService $settings)
    {
    }

    public function lessons() { return $this->reply(fn (int $actor) => $this->data->lessons($actor)); }
    public function schedules() { return $this->reply(fn (int $actor) => $this->data->schedules($actor)); }
    public function finance() { return $this->reply(fn (int $actor) => $this->data->finance($actor)); }

    public function gallery()
    {
        return $this->reply(function (int $actor): array {
            $data = $this->gallery->data($actor);
            $data['owners'] = array_values(array_filter($data['owners'], static fn (array $owner): bool => (int) $owner['userId'] === $actor));
            $data['items'] = array_values(array_filter($data['items'], static fn (array $item): bool => (int) $item['ownerId'] === $actor));
            $data['hideOwnerFilters'] = true;
            return $data;
        });
    }

    public function createGallery()
    {
        return $this->reply(fn (int $actor) => $this->gallery->store($actor, ['ownerId' => $actor] + $_POST, $_FILES['file'] ?? []));
    }

    public function updateGallery(int $id)
    {
        return $this->reply(function (int $actor) use ($id) {
            $this->ownMedia($actor, $id);
            return $this->gallery->update($actor, $id, $_POST, $_FILES['file'] ?? []);
        });
    }

    public function deleteGallery(int $id)
    {
        return $this->reply(function (int $actor) use ($id) {
            $this->ownMedia($actor, $id);
            $this->gallery->delete($actor, $id);
            return null;
        });
    }

    public function settings()
    {
        return $this->reply(function (int $actor): array {
            $personal = [];
            foreach (DB::table('z_user_settings')->where('user_id', $actor)->whereNull('deleted_at')->get() as $row) {
                if (str_starts_with((string) $row['key'], 'panel_')) {
                    $personal[substr((string) $row['key'], 6)] = $row['value'];
                }
            }
            return ['defaults' => $this->settings->get(), 'personal' => $personal, 'canEditSite' => $actor === 1];
        });
    }

    public function saveSettings()
    {
        return $this->reply(function (int $actor): array {
            $data = $this->payload();
            $options = [
                'language' => ['fa', 'en'], 'themeMode' => ['light', 'dark'],
                'colorTheme' => ['indigo', 'emerald', 'rose', 'amber'],
                'primaryFont' => ['vazir', 'sahel', 'iran_yekan', 'iran_sansx', 'kalameh', 'peyda'],
            ];
            foreach (['fontScale' => [-2, 2], 'fontWeight' => [0, 5], 'cornerRadius' => [0, 16]] as $key => $bounds) {
                if (!array_key_exists($key, $data)) continue;
                if (filter_var($data[$key], FILTER_VALIDATE_INT) === false || (int) $data[$key] < $bounds[0] || (int) $data[$key] > $bounds[1]) {
                    throw new RuntimeException('مقدار تنظیمات معتبر نیست.', 422);
                }
                $options[$key] = null;
            }
            if (array_diff(array_keys($data), array_keys($options))) throw new RuntimeException('گزینهٔ تنظیمات معتبر نیست.', 422);
            foreach ($data as $key => $value) {
                if (is_array($options[$key]) && !in_array($value, $options[$key], true)) throw new RuntimeException('مقدار تنظیمات معتبر نیست.', 422);
            }
            transaction(function () use ($actor, $data): void {
                foreach ($data as $key => $value) {
                    $settingKey = 'panel_' . $key;
                    $existing = DB::table('z_user_settings')->where('user_id', $actor)->where('`key`', $settingKey)->whereNull('deleted_at')->first();
                    $values = ['value' => (string) $value, 'type' => is_int($value) ? 'integer' : 'string', 'visibility' => 'private', 'updated_by' => $actor];
                    if ($existing) DB::table('z_user_settings')->where('user_setting_id', (int) $existing['user_setting_id'])->update($values);
                    else DB::table('z_user_settings')->insert(['user_id' => $actor, '`key`' => $settingKey, 'created_by' => $actor] + $values);
                }
            });
            return $data;
        });
    }

    private function ownMedia(int $actor, int $id): void
    {
        if ($id < 1 || !DB::table('media_files')->where('media_file_id', $id)->where('user_id', $actor)->whereNull('deleted_at')->first()) {
            throw new RuntimeException('رسانه یافت نشد.', 403);
        }
    }

    private function payload(): array
    {
        $raw = (string) request()->input('payload_b64', '');
        $decoded = base64_decode(strtr($raw, '-_', '+/'), true);
        $data = $decoded === false ? null : json_decode($decoded, true);
        if (!is_array($data)) throw new RuntimeException('اطلاعات ارسال‌شده معتبر نیست.', 422);
        return $data;
    }

    private function reply(callable $action)
    {
        try {
            return ResponseFactory::json(['success' => true, 'data' => $action((int) auth()->id())]);
        } catch (Throwable $error) {
            $expected = $error instanceof RuntimeException && !($error instanceof \PDOException);
            return ResponseFactory::json(['success' => false, 'message' => $expected ? $error->getMessage() : 'انجام عملیات در حال حاضر ممکن نیست.'], $expected ? (in_array($error->getCode(), [403, 422], true) ? $error->getCode() : 422) : 500);
        }
    }
}
