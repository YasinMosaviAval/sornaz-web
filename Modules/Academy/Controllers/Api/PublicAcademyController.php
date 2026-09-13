<?php
namespace Modules\Academy\Controllers\Api;

use Core\http\ResponseFactory;
use Modules\Academy\Services\AcademyRegistrationService;

/** Public search uses the same filters and data as the website home form. */
class PublicAcademyController {
    public function __construct(protected AcademyRegistrationService $service) {}

    public function options() {
        $this->locale();
        return ResponseFactory::json($this->service->searchOptions());
    }

    public function index() {
        $this->locale();
        $items = $this->service->all([
            'q' => mb_substr(trim((string)($_GET['q'] ?? '')), 0, 200),
            'instrument' => max(0, (int)($_GET['instrument'] ?? 0)),
            'city' => max(0, (int)($_GET['city'] ?? 0)),
        ]);
        $page = max(1, (int)($_GET['page'] ?? 1));
        // Return only the public card fields, not underlying address/contact records.
        $cards = array_map(static fn(array $item): array => array_intersect_key($item,
            array_flip(['id', 'name', 'summary', 'city', 'instruments', 'avatar', 'cover', 'teachers_count', 'branches'])),
            array_slice($items, ($page - 1) * 12, 12));
        return ResponseFactory::json(['items' => $cards, 'has_more' => $page * 12 < count($items)]);
    }

    public function show(int $id) {
        $this->locale();
        foreach ($this->service->all() as $item) {
            if ((int)$item['id'] !== $id) continue;
            $item['addresses'] = array_map(static fn(array $row): array => ['address'=>(string)($row['address']??'')], $item['addresses']??[]);
            $item['contacts'] = array_map(static fn(array $row): array => ['type'=>(string)($row['type']??''),'value'=>(string)($row['value']??'')], $item['contacts']??[]);
            return ResponseFactory::json($item);
        }
        return ResponseFactory::json(['success'=>false,'message'=>'آموزشگاه پیدا نشد.'],404);
    }

    private function locale(): void {
        app()->setLocale(str_starts_with(strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? ''), 'en') ? 'en' : 'fa');
    }
}
