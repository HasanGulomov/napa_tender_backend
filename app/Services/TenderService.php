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

    // Bu yerda repository-dagi metodlarni chaqiramiz
    public function list($perPage)
    {
        return $this->repo->getPaginated($perPage);
    }
    public function filter($data)
    {
        return $this->repo->filterTenders($data);
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
    public function search($term, $perPage)
    {
        return $this->repo->search($term, $perPage);
    }

    public function toggleFavorite($user, $id)
    {
        $tender = $this->repo->findById($id);
        if (!$tender) return null;

        $status = $user->favorites()->toggle($id);
        return count($status['attached']) > 0;
    }
}
