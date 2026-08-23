<?php

namespace Modules\Academy\Controllers\Web;

use Core\http\ResponseFactory;
use Modules\Academy\Services\AcademyBranchOfferingService;
use RuntimeException;
use Throwable;

class AcademyBranchOfferingController {


    public function __construct(protected AcademyBranchOfferingService $service) {}


    public function index() {
        return ResponseFactory::json(['success' => true, 'data' => $this->service->all((int) auth()->id())]);
    }


    public function storeSchedule() {
        return $this->saveSchedule();
    }


    public function updateSchedule(int $id) {
        return $this->saveSchedule($id);
    }

    public function storeLesson() { return $this->saveLesson(); }

    public function updateLesson(int $id) { return $this->saveLesson($id); }

    public function storeLessonCatalog() {
        try {
            $data = $this->payload('اطلاعات درس جدید معتبر نیست.');
            return ResponseFactory::json(['success' => true, 'data' => $this->service->createLesson((int) auth()->id(), $data)]);
        } catch (Throwable $e) {
            return ResponseFactory::json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    private function saveLesson(int $id = 0) {
        try {
            $data = $this->payload('اطلاعات درس معتبر نیست.');
            return ResponseFactory::json(['success' => true, 'data' => $this->service->saveLesson((int) auth()->id(), $data, $id)]);
        } catch (Throwable $e) {
            return ResponseFactory::json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    private function payload(string $message): array {
        $encoded = (string) request()->input('payload_b64', '');
        $decoded = base64_decode(strtr($encoded, '-_', '+/'), true);
        $data = $decoded === false ? null : json_decode($decoded, true);
        if (!is_array($data)) throw new RuntimeException($message);
        return $data;
    }


    private function saveSchedule(int $id = 0) {
        try {
            $data = $this->payload('اطلاعات برنامه زمانی معتبر نیست.');
            $saved = $this->service->saveSchedule((int) auth()->id(), $data, $id);
            return ResponseFactory::json(['success' => true, 'data' => $saved]);
        } catch (Throwable $e) {
            return ResponseFactory::json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }


    public function delete(string $type, int $id) {
        try {
            $this->service->delete($type, $id, (int) auth()->id());
            return ResponseFactory::json(['success' => true]);
        } catch (Throwable $e) {
            return ResponseFactory::json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }


}
