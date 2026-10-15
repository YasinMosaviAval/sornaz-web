<?php
namespace Modules\Academy\Services;

final class LessonInUseException extends \RuntimeException
{
    public function __construct(public readonly array $dependencies)
    {
        parent::__construct('حذف این درس به دلیل استفاده در دوره یا قرارداد مدرس مجاز نیست.');
    }
}
