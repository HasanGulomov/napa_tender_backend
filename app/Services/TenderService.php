<?php

namespace App\Services;

use App\Repositories\TenderRepository;

class TenderService
{
    protected $repo;

    public function __construct(TenderRepository $repo)
    {
        $this->repo = $repo;
    }

    public function list($params, $perPage)
    {
        return $this->repo->getFiltered($params, $perPage);
    }

    public function meta()
    {
        return $this->repo->getMetaData();
    }

    public function get($id)
    {
        return $this->repo->findById($id);
    }

    public function store($data)
    {
        return $this->repo->create($data);
    }



    public function toggleFavorite($user, $id)
    {
        $tender = $this->repo->findById($id);
        if (!$tender) {
            return null;
        }
        if (!$user || !method_exists($user, 'favorites')) {
            return null;
        }
        $status = $user->favorites()->toggle($id);

        return count($status['attached']) > 0;
    }
}
