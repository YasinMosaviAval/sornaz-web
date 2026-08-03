<?php

namespace Core\console;

class Stub {
    protected string $contents;



    public function __construct(string $file) {
        $this->contents=file_get_contents($file);
    }



    public function replace(array $variables): static {
        foreach($variables as $key=>$value){
            $this->contents=str_replace(
                '{{'.$key.'}}',
                $value,
                $this->contents
            );
        }
        return $this;
    }



    public function render(): string {
        return $this->contents;
    }





}