<?php


namespace App\Service\Logger;


use Symfony\Component\HttpFoundation\RequestStack;

final class RequestProcessor
{
    /** @var RequestStack */
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @param array $record
     * @return array
     */
    public function processRecord(array $record): array
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!isset($record['extra'])) {
            return $record;
        }

        $record['extra']['client_ip'] = $request->getClientIp();
        $record['extra']['client_port'] = $request->getPort();
        $record['extra']['uri'] = $request->getUri();
        $record['extra']['method'] = $request->getMethod();
        $record['extra']['request'] = $request->request->all();

        return $record;
    }
}
