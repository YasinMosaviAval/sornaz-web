<?php

namespace Modules\Academy\Controllers\Web;

use Core\http\ResponseFactory;
use Modules\Academy\Services\AcademyCourseService;
use RuntimeException;
use Throwable;

class AcademyCourseController
{
    public function __construct(private AcademyCourseService $s)
    {
    }

    public function index()
    {
        return $this->run(fn () => ['success' => true, 'data' => $this->s->bootstrap((int) auth()->id())]);
    }

    public function store()
    {
        return $this->save();
    }

    public function update(int $id)
    {
        return $this->save($id);
    }

    public function destroy(int $id)
    {
        return $this->run(
            function () use ($id) {
                $this->s->deleteCourse((int) auth()->id(), $id);
                return ['success' => true];
            }
        );
    }

    public function cycleStatus(int $id)
    {
        return $this->run(fn () => ['success' => true, 'data' => $this->s->cycleStatus((int) auth()->id(), $id)]);
    }

    public function realtimeVersion()
    {
        return $this->run(fn () => ['success' => true, 'data' => $this->s->realtimeVersion((int) auth()->id())]);
    }

    public function studentOptions()
    {
        return $this->run(fn () => ['success' => true, 'data' => $this->s->studentOptions((int) auth()->id())]);
    }

    public function storeStudent()
    {
        return $this->run(fn () => ['success' => true, 'data' => $this->s->storeStudent((int) auth()->id(), $this->payload())]);
    }

    public function memberUserLookup()
    {
        try {
            return ResponseFactory::json(['success' => true, 'data' => $this->s->memberUserLookup((int) auth()->id(), $this->payload())]);
        } catch (\PDOException $error) {
            return ResponseFactory::json(['success' => false, 'message' => 'جستجوی کاربر ناموفق بود.'], 500);
        } catch (RuntimeException $error) {
            return ResponseFactory::json(['success' => false, 'message' => $error->getMessage()], $error->getCode() === 403 ? 403 : 422);
        } catch (Throwable $error) {
            return ResponseFactory::json(['success' => false, 'message' => 'جستجوی کاربر ناموفق بود.'], 500);
        }
    }

    public function studentSchedulePreview(int $id)
    {
        return $this->run(fn () => ['success' => true, 'data' => (new \Modules\Academy\Services\AcademyTermService())->previewStudentSchedule((int) auth()->id(), $id, $this->payload())]);
    }

    public function storeLevel()
    {
        return $this->level();
    }

    public function updateLevel(int $id)
    {
        return $this->level($id);
    }

    public function deleteLevel(int $id)
    {
        return $this->run(
            function () use ($id) {
                $this->s->deleteLevel((int) auth()->id(), $id);
                return ['success' => true];
            }
        );
    }

    private function save(int $id = 0)
    {
        return $this->run(fn () => ['success' => true, 'data' => $this->s->saveCourse((int) auth()->id(), $this->payload(), $id)]);
    }

    private function level(int $id = 0)
    {
        return $this->run(fn () => ['success' => true, 'data' => $this->s->saveLevel((int) auth()->id(), $this->payload(), $id)]);
    }

    private function payload(): array
    {
        $raw = base64_decode(strtr((string) request()->input('payload_b64', ''), '-_', '+/'), true);
        $d = $raw === false ? null : json_decode($raw, true);
        if (!is_array($d)) {
            throw new RuntimeException('اطلاعات ارسالی معتبر نیست.');
        }
        return $d;
    }

    private function run(callable $f)
    {
        try {
            return ResponseFactory::json($f());
        } catch (Throwable $e) {
            return ResponseFactory::json(['success' => false, 'message' => $e instanceof \PDOException ? 'ذخیره یا دریافت اطلاعات انجام نشد. دوباره تلاش کنید.' : $e->getMessage()], in_array($e->getCode(), [403, 404, 409], true) ? $e->getCode() : 422);
        }
    }
}
