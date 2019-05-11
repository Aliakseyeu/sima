<?php

namespace App\Objects\Response;

class Response implements IResponsable
{
    
	protected $error = false;
	protected $messages = [];
	protected $success = [];
	protected $errors = [];
	
	public function __construct($message = ''){
		$this->message = $message;
	}
	
	public function setError(bool $error = true): void{
		$this->error = $error;
	}

    public function isError(): bool
    {
        return $this->error;
    }

	public function addMessage(string $message): void{
		$this->messages[] = $message;
	}

    public function getMessages(): array
    {
        return $this->messages;
    }

	public function addSuccess(string $message): void{
		$this->success[] = $message;
	}

	public function addError(string $message): void{
		$this->errors[] = $message;
	}

    public function getSuccess(): array
    {
        return $this->success;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function toJson(){
        return json_encode($this);
    }
	
}
