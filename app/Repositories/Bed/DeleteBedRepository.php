<?php

namespace App\Repositories\Bed;

use App\Repositories\BaseRepository;

class DeleteBedRepository extends BaseRepository
{
    public function execute($bed){
        if ($bed->status === 'Occupied') {
            return $this->error('Cannot delete an occupied bed.', 400);
        }
        $bed->delete();
        return $this->success('Successfully deleted bed.', null, 200);
    }
}
