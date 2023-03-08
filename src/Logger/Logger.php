<?php

namespace Frankie\Core\Logger;

use Ds\Vector;
use Stringable;

class Logger implements LoggerInterface
{
    /** @var LogHandlerInterface[] */
    protected Vector $debugContainer;
    /** @var LogHandlerInterface[] */
    protected Vector $infoContainer;
    /** @var LogHandlerInterface[] */
    protected Vector $noticeContainer;
    /** @var LogHandlerInterface[] */
    protected Vector $warningContainer;
    /** @var LogHandlerInterface[] */
    protected Vector $errorContainer;
    /** @var LogHandlerInterface[] */
    protected Vector $criticalContainer;
    /** @var LogHandlerInterface[] */
    protected Vector $alertContainer;
    /** @var LogHandlerInterface[] */
    protected Vector $emergencyContainer;
    protected int $level;

    public function __construct(LogLevel $level)
    {
        $this->level = $level->value;
        $this->clearAll();
    }

    public function clearAll(): self
    {
        $this->debugContainer = new Vector();
        $this->infoContainer = new Vector();
        $this->noticeContainer = new Vector();
        $this->warningContainer = new Vector();
        $this->errorContainer = new Vector();
        $this->criticalContainer = new Vector();
        $this->alertContainer = new Vector();
        $this->emergencyContainer = new Vector();

        return $this;
    }

    public function addToAll(LogHandlerInterface $logger): self
    {
        $this->debugContainer->push($logger);
        $this->infoContainer->push($logger);
        $this->noticeContainer->push($logger);
        $this->warningContainer->push($logger);
        $this->errorContainer->push($logger);
        $this->criticalContainer->push($logger);
        $this->alertContainer->push($logger);
        $this->emergencyContainer->push($logger);

        return $this;
    }

    public function addToDebug(LogHandlerInterface $logger): self
    {
        $this->debugContainer->push($logger);

        return $this;
    }

    public function addToInfo(LogHandlerInterface $logger): self
    {
        $this->infoContainer->push($logger);

        return $this;
    }

    public function addToNotice(LogHandlerInterface $logger): self
    {
        $this->noticeContainer->push($logger);

        return $this;
    }

    public function addToWarning(LogHandlerInterface $logger): self
    {
        $this->warningContainer->push($logger);

        return $this;
    }

    public function addToError(LogHandlerInterface $logger): self
    {
        $this->errorContainer->push($logger);

        return $this;
    }

    public function addToCritical(LogHandlerInterface $logger): self
    {
        $this->criticalContainer->push($logger);

        return $this;
    }

    public function addToAlert(LogHandlerInterface $logger): self
    {
        $this->alertContainer->push($logger);

        return $this;
    }

    public function addToEmergency(LogHandlerInterface $logger): self
    {
        $this->emergencyContainer->push($logger);

        return $this;
    }

    public function clearDebug(): self
    {
        $this->debugContainer = new Vector();

        return $this;
    }

    public function clearInfo(): self
    {
        $this->infoContainer = new Vector();

        return $this;
    }

    public function clearNotice(): self
    {
        $this->noticeContainer = new Vector();

        return $this;
    }

    public function clearWarning(): self
    {
        $this->warningContainer = new Vector();

        return $this;
    }

    public function clearError(): self
    {
        $this->errorContainer = new Vector();

        return $this;
    }

    public function clearCritical(): self
    {
        $this->criticalContainer = new Vector();

        return $this;
    }

    public function clearAlert(): self
    {
        $this->alertContainer = new Vector();

        return $this;
    }

    public function clearEmergency(): self
    {
        $this->emergencyContainer = new Vector();

        return $this;
    }

    public function getDebugLoggers(): Vector
    {
        return $this->debugContainer->copy();
    }

    public function getNoticeLoggers(): Vector
    {
        return $this->noticeContainer->copy();
    }

    public function getInfoLoggers(): Vector
    {
        return $this->infoContainer->copy();
    }

    public function getWarningLoggers(): Vector
    {
        return $this->warningContainer->copy();
    }

    public function getErrorLoggers(): Vector
    {
        return $this->errorContainer->copy();
    }

    public function getCriticalLoggers(): Vector
    {
        return $this->criticalContainer->copy();
    }

    public function getAlertLoggers(): Vector
    {
        return $this->alertContainer->copy();
    }

    public function getEmergencyLoggers(): Vector
    {
        return $this->emergencyContainer->copy();
    }

    public function debug(Stringable|string $message, array $context = []): void
    {
        if ($this->level < LogLevel::DEBUG->value) {
            return;
        }
        foreach ($this->debugContainer as $handler) {
            $handler->debug($message, $context);
        }
    }

    public function info(Stringable|string $message, array $context = []): void
    {
        if ($this->level < LogLevel::INFO->value) {
            return;
        }
        foreach ($this->infoContainer as $handler) {
            $handler->info($message, $context);
        }
    }

    public function notice(Stringable|string $message, array $context = []): void
    {
        if ($this->level < LogLevel::NOTICE->value) {
            return;
        }
        foreach ($this->noticeContainer as $handler) {
            $handler->notice($message, $context);
        }
    }

    public function warning(Stringable|string $message, array $context = []): void
    {
        if ($this->level < LogLevel::WARNING->value) {
            return;
        }
        foreach ($this->warningContainer as $handler) {
            $handler->warning($message, $context);
        }
    }

    public function error(Stringable|string $message, array $context = []): void
    {
        if ($this->level < LogLevel::ERROR->value) {
            return;
        }
        foreach ($this->errorContainer as $handler) {
            $handler->error($message, $context);
        }
    }

    public function critical(Stringable|string $message, array $context = []): void
    {
        if ($this->level < LogLevel::CRITICAL->value) {
            return;
        }
        foreach ($this->criticalContainer as $handler) {
            $handler->critical($message, $context);
        }
    }

    public function alert(Stringable|string $message, array $context = []): void
    {
        if ($this->level < LogLevel::ALERT->value) {
            return;
        }
        foreach ($this->alertContainer as $handler) {
            $handler->alert($message, $context);
        }
    }

    public function emergency(Stringable|string $message, array $context = []): void
    {
        if ($this->level < LogLevel::EMERGENCY->value) {
            return;
        }
        foreach ($this->emergencyContainer as $handler) {
            $handler->emergency($message, $context);
        }
    }
}
