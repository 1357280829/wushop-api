<?php

namespace App\Models\Traits\Cart;

trait ChangeNumber
{
    public function incrementNumber($amount = 1)
    {
        if ($this->is_invalid) {
            return false;
        }

        return $this->update([
            'number' => $this->getChangingNumber($this->number + $amount),
        ]);
    }

    public function decrementNumber($amount = 1)
    {
        $number = $this->number - $amount;

        return $this->update([
            'number' => $number > 1 ? $number : 1,
        ]);
    }

    //  获取 待变化的数量
    public function getChangingNumber($number)
    {
        $number = $number > 1 ? $number : 1;

        $limit = 200;

        $stock = $this->product->is_enable_sku ? $this->productSku->stock : $this->product->stock;

        return $stock < $limit ? ($number < $stock ? $number : $stock) : ($number < $limit ? $number : $limit);
    }
}