<?php

namespace Modules\Branch\Controllers\Web;

use Core\Http\Request;
use Modules\Branch\Repositories\BranchRepository;
use Modules\Branch\Services\BranchService;
use Modules\Branch\Services\BranchTypeService;
use Modules\System\Repositories\UserRepository;

class BranchController
{
    protected BranchService $service;

    protected BranchTypeService $branchTypeService;

    public function __construct()
    {
        $this->service = new BranchService(
            new BranchRepository(),
            new UserRepository()
        );
        $this->branchTypeService = app()
            ->container()
            ->make(BranchTypeService::class);
    }

    /*
    |--------------------------------------------------------------------------
    | List
    |--------------------------------------------------------------------------
    */

    public function index(int $academy)
    {
        return view(
            'branch.index',
            [
                'academyId' => $academy,
                'branches'  => $this->service->list($academy),
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    public function create(int $academy)
    {
        return view(
            'branch.create',
            [
                'academyId'   => $academy,
                'branch'      => [],
                'branchTypes' => $this->branchTypeService->options(),
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */

    public function store(int $academy)
    {
        $this->service->create(
            $academy,
            Request::all()
        );
        return redirect(
            "/academy/{$academy}/branches"
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Edit
    |--------------------------------------------------------------------------
    */

    public function edit(
        int $academy,
        int $branch
    ) {
        return view(
            'branch.edit',
            [
                'academyId'   => $academy,
                'branch'      => $this->service->editData($branch),
                'branchTypes' => $this->branchTypeService->options(),
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(
        int $academy,
        int $branch
    ) {
        $this->service->update(
            $branch,
            Request::all()
        );
        return redirect(
            "/academy/{$academy}/branches"
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function delete(
        int $academy,
        int $branch
    ) {
        $this->service->delete($branch);
        return redirect(
            "/academy/{$academy}/branches"
        );
    }



}