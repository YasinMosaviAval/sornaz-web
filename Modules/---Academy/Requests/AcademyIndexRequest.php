<?php

namespace Modules\Academy\Requests;

use Exception;

class AcademyIndexRequest {

    protected array $data;

    public function __construct(array $data = []) { $this->data = $data; }
    public function page(): int { return max(1, (int)($this->data['page'] ?? 1)); }
    public function perPage(): int { return max(1, min(100, (int)($this->data['per_page'] ?? 20))); }
    public function search(): ?string { return trim($this->data['search'] ?? '') ?: null; }
    public function status(): mixed { return $this->data['status'] ?? null; }
    public function orderBy(): string { return $this->data['order_by'] ?? 'user_id'; }
    public function direction(): string { return strtoupper($this->data['direction'] ?? 'DESC'); }
    public function sort(): string {return $this->data['sort'] ?? 'user_id';}
    
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
            throw new Exception(json_encode($errors));
        }
        return $data;
    }

}