<?php


namespace App\Service\Logger;


use App\Service\Logger\LogService;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

class MonologORMHandler extends AbstractProcessingHandler
{
    /** @var LogService */
    private $logService;

    public function __construct(LogService $logService)
    {
        //parent::__construct($level, $bubble);
        $this->logService = $logService;
    }

    protected function write(array $record)
    {
        $log = $this->logService->createByMonologRecord($record);
        $this->logService->save($log);
    }

}
