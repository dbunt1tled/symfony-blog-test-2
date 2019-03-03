<?php


namespace App\Service\Logger;


use App\Entity\Log;
use App\Repository\LogRepository;

final class LogService
{
    /** @var LogRepository */
    private $logRepository;

    public function __construct(LogRepository $logRepository)
    {
        $this->logRepository = $logRepository;
    }

    /**
     * @param Log $log
     * @return bool
     */
    public function save(Log $log): bool
    {
        return $this->logRepository->save($log);
    }

    public function createByMonologRecord(array $record): Log
    {
        $log = new Log();
        $log->setContext($record['context']);
        $log->setExtra($record['extra']);
        $log->setLevel($record['level']);
        $log->setLevelName($record['level_name']);
        $log->setMessage($record['message']);
        return $log;
    }
}
