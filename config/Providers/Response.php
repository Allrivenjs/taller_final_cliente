<?php

namespace Config\Providers;

class Response
{
    /**
     * @var string
     */
    protected $content;

    public function responseContent($content): static
    {
        $this->content = $content;
        return $this;
    }

    public function sendContent(): static
    {
        echo $this->content;
        return $this;
    }

    public function sendJson(): static
    {
        header('Content-Type: application/json');
        echo json_encode($this->content, JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT);
        return $this;
    }

    public function setStatusCode(int $statusCode): static
    {
        http_response_code($statusCode);
        return $this;
    }

    protected function send(): static
    {
        is_array($this->content) ? $this->sendJson() : $this->sendContent();
        static::closeOutputBuffers(0, true); flush();
        return $this;

    }
    public static function closeOutputBuffers(int $targetLevel, bool $flush): void
    {
        $status = ob_get_status(true);
        $level = \count($status);
        $flags = \PHP_OUTPUT_HANDLER_REMOVABLE | ($flush ? \PHP_OUTPUT_HANDLER_FLUSHABLE : \PHP_OUTPUT_HANDLER_CLEANABLE);

        while ($level-- > $targetLevel && ($s = $status[$level]) && (!isset($s['del']) ? !isset($s['flags']) || ($s['flags'] & $flags) === $flags : $s['del'])) {
            if ($flush) {
                ob_end_flush();
            } else {
                ob_end_clean();
            }
        }
    }

}