<?php
namespace CoinbaseCommerce\Resources\Operations;

trait SaveMethodTrait
{
    public function save($headers = [])
    {
        $id = $this->getPrimaryKeyValue();

        if (\is_scalar($id) && !method_exists($this, 'update')) {
            throw new \Exception('Update is not allowed');
        }

        return $id ? $this->update() : $this->insert();
    }
}
