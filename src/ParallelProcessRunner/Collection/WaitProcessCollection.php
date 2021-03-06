<?php

namespace Tonic\ParallelProcessRunner\Collection;

use Symfony\Component\Process\Process;
use Tonic\ParallelProcessRunner\Exception\ProcessesMustBeInReadyStatusException;

/**
 * Class WaitProcessCollection.
 *
 * @author kandelyabre <kandelyabre@gmail.com>
 */
class WaitProcessCollection extends ProcessCollection
{
    /**
     * {@inheritdoc}
     *
     * @param Process|Process[]|ProcessCollection|array $process
     *
     * @return array|int
     *
     * @throws ProcessesMustBeInReadyStatusException
     */
    public function add($process)
    {
        switch (true) {
            case is_array($process):
                $result = array_map(function ($process) {
                    return $this->add($process);
                }, $process);
                break;
            case $process instanceof ProcessCollection:
                $result = $this->add($process->toArray());
                break;
            case $process instanceof Process:
                if ($process->getStatus() != Process::STATUS_READY) {
                    throw new ProcessesMustBeInReadyStatusException($process);
                }
                // no break
            default:
                $result = parent::add($process);
        }

        return $result;
    }
}
