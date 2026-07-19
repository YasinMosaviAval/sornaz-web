<?php

namespace Modules\Branch\Services;

use Core\Translation\TranslationService;
use Modules\Branch\Repositories\BranchTypeRepository;

class BranchTypeService {


    public function __construct(protected BranchTypeRepository $repository){
    }


    public function options(): array {
        $items = $this->repository->options();
        foreach ($items as &$item){
            $item['title'] = TranslationService::manager()->get(
                'academy_branch_types',
                $item['academy_branch_type_id'],
                'title'
            );
        }
        return $items;
    }




}