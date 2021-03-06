<?php

namespace App\Containers\Ad\Tasks;

use App\Containers\Ad\Data\Repositories\AdRepository;
use App\Containers\Ad\Services\AdService;
use App\Ship\Exceptions\CreateResourceFailedException;
use App\Ship\Parents\Tasks\Task;
use Exception;
use Illuminate\Support\Facades\Log;

class CreateAdTask extends Task
{

  protected $repository;
  /**
   * @var AdService
   */
  private $service;

  public function __construct(AdRepository $repository, AdService $service)
  {
    $this->repository = $repository;
    $this->service = $service;
  }

  public function run(object $data)
  {
    try {
      $ad = $this->service->createAd($data);
      $this->service->savePhoto($data, $ad->id);
    } catch (Exception $exception) {
      Log::error('Createing ads filed: ' . $exception->getMessage());
      return  false;
    }
    return true;
  }
}
