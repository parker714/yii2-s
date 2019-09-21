<?php

namespace parker714\yii2s\components;

/**
 * Class Response
 *
 * @package parker714\yii2s\components
 */
class Response extends \yii\web\Response
{
    /**
     * @var \swoole_http_response
     */
    public $swResponse;

    /**
     * rewrite sendContent
     */
    public function sendContent()
    {
        if ($this->stream === null) {
            $this->swResponse->end($this->content);

            return;
        }
        $chunkSize = 2 * 1024 * 1024; // 2MB per chunk swoole limit
        if (is_array($this->stream)) {
            list ($handle, $begin, $end) = $this->stream;
            fseek($handle, $begin);
            while (!feof($handle) && ($pos = ftell($handle)) <= $end) {
                if ($pos + $chunkSize > $end) {
                    $chunkSize = $end - $pos + 1;
                }
                $this->swResponse->write(fread($handle, $chunkSize));
                //flush(); // Free up memory. Otherwise large files will trigger PHP's memory limit.
            }
            fclose($handle);
        } else {
            while (!feof($this->stream)) {
                $this->swResponse->write(fread($this->stream, $chunkSize));
                //flush();
            }
            fclose($this->stream);
        }
        $this->swResponse->end();
    }

    /**
     * rewrite sendHeaders
     */
    public function sendHeaders()
    {
        $headers = $this->getHeaders();
        if ($headers->count > 0) {
            foreach ($headers as $name => $values) {
                foreach ($values as $value) {
                    $this->swResponse->header($name, $value);
                }
            }
        }
        $this->swResponse->status($this->getStatusCode());
    }
}