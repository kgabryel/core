<?php

namespace Frankie\Core\Logger;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger as Monolog;
use Stringable;

class FileLogHandler implements LogHandlerInterface
{
    private ?string $dateFormat;
    private ?string $outputFormat;
    private bool $allowInlineBreaks;
    private bool $ignoreEmptyContextAndExtra;
    private Monolog $logger;
    private string $channel;
    private PathProviderInterface $pathProvider;

    public function __construct(PathProviderInterface $pathProvider)
    {
        $this->channel = 'Frankie';
        $this->dateFormat = null;
        $this->outputFormat = null;
        $this->allowInlineBreaks = false;
        $this->ignoreEmptyContextAndExtra = true;
        $this->pathProvider = $pathProvider;
        $this->modifyMonolog();
    }

    private function modifyMonolog(): void
    {
        $this->logger = new Monolog($this->channel);
        $handler = new StreamHandler($this->pathProvider->getPath());
        $handler->setFormatter(
            new LineFormatter(
                $this->outputFormat,
                $this->dateFormat,
                $this->allowInlineBreaks,
                $this->ignoreEmptyContextAndExtra
            )
        );
        $this->logger->pushHandler($handler);
    }

    public function changeChannel(string $channel): self
    {
        $this->channel = $channel;
        $this->modifyMonolog();

        return $this;
    }

    public function changeDateFormat(?string $dateFormat): self
    {
        $this->dateFormat = $dateFormat;
        $this->modifyMonolog();

        return $this;
    }

    public function changeOutputFormat(?string $outputFormat): self
    {
        $this->outputFormat = $outputFormat;
        $this->modifyMonolog();

        return $this;
    }

    public function enableInlineBreaks(): self
    {
        $this->allowInlineBreaks = true;
        $this->modifyMonolog();

        return $this;
    }

    public function disableInlineBreaks(): self
    {
        $this->allowInlineBreaks = false;
        $this->modifyMonolog();

        return $this;
    }

    public function enableIgnoreEmptyContextAndExtra(): self
    {
        $this->ignoreEmptyContextAndExtra = true;
        $this->modifyMonolog();

        return $this;
    }

    public function disableIgnoreEmptyContextAndExtra(): self
    {
        $this->ignoreEmptyContextAndExtra = false;
        $this->modifyMonolog();

        return $this;
    }

    public function debug(Stringable|string $message, array $context): void
    {
        $this->logger->debug($message, $context);
    }

    public function info(Stringable|string $message, array $context): void
    {
        $this->logger->info($message, $context);
    }

    public function notice(Stringable|string $message, array $context): void
    {
        $this->logger->notice($message, $context);
    }

    public function warning(Stringable|string $message, array $context): void
    {
        $this->logger->warning($message, $context);
    }

    public function error(Stringable|string $message, array $context): void
    {
        $this->logger->error($message, $context);
    }

    public function critical(Stringable|string $message, array $context): void
    {
        $this->logger->critical($message, $context);
    }

    public function alert(Stringable|string $message, array $context): void
    {
        $this->logger->alert($message, $context);
    }

    public function emergency(Stringable|string $message, array $context): void
    {
        $this->logger->emergency($message, $context);
    }

    public function changePath(PathProviderInterface $pathProvider): self
    {
        $this->pathProvider = $pathProvider;
        $this->modifyMonolog();

        return $this;
    }
}
