<?php

namespace Modules\Academy\Requests;

class AcademyIndexRequest {


    // protected array $input;
    protected array $data;


    public function __construct(array $data = []) {
        $this->data = $data;
    }

    public function page(): int {
        return max(1, (int)($this->data['page'] ?? 1));
    }

    public function perPage(): int
    {
        return max(1, min(100, (int)($this->data['per_page'] ?? 20)));
    }

    public function search(): ?string
    {
        return trim($this->data['search'] ?? '') ?: null;
    }

    public function status(): mixed
    {
        return $this->data['status'] ?? null;
    }

    public function orderBy(): string
    {
        return $this->data['order_by'] ?? 'user_id';
    }

    public function direction(): string
    {
        return strtoupper($this->data['direction'] ?? 'DESC');
    }

    // public function page(): int {return max(1, (int)($this->input['page'] ?? 1));}
    // public function perPage(): int {
    //     $perPage = (int)($this->input['per_page'] ?? 20);
    //     if ($perPage < 1) {
    //         $perPage = 20;
    //     }
    //     if ($perPage > 100) {
    //         $perPage = 100;
    //     }
    //     return $perPage;
    // }
    // public function search(): ?string {
    //     $value = trim($this->input['search'] ?? '');
    //     return $value === '' ? null : $value;
    // }
    // public function status(): mixed {return $this->input['status'] ?? null;}
    public function sort(): string {return $this->data['sort'] ?? 'user_id';}
    // public function direction(): string {return strtolower($this->input['direction'] ?? 'desc') === 'asc' ? 'asc' : 'desc';}
    public function validate(array $data): array {
        $errors=[];

        if(empty($data['username'])){
            $errors['username']='Username is required';
        }

        if(empty($data['status'])){
            $data['status']=1;
        }

        if(!isset($data['locale'])){
            $data['locale']='fa';
        }

        if($errors){
            throw new \Exception(
                json_encode($errors)
            );
        }

        return $data;
    }

}