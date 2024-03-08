<?php
namespace App\Models\Traits;

trait HasHiddenTimestamps
{
  public function __construct(array $data = [])
  {
    parent::__construct($data);
    $this->hidden = array_merge($this->hidden, ['created_at', 'updated_at']);
  }
}