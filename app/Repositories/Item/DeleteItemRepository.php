<?php

namespace App\Repositories\Item;

use App\Repositories\BaseRepository;

class DeleteItemRepository extends BaseRepository
{
    public function execute($item)
    {
        try {
            $item->delete();
            return $this->success('Item deleted successfully', null, 200);
        } catch (\Exception $e) {
            return $this->error('Failed to delete item', 500, $e->getMessage());
        }

    }
}
