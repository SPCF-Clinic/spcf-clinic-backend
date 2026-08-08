<?php

namespace App\Repositories\Bed;

use App\Repositories\BaseRepository;

class DeleteBedRepository extends BaseRepository
{
    public function execute($bed){
        $bed->delete();
        return $this->success('Successfully deleted bed.', null, 200);
    }
}
