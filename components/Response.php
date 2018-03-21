<?php

namespace app\components;

class Response extends \yii\web\Response
{
    protected $swResponse;

    public function setSwResponse($response)
    {
        $this->swResponse = $response;
    }

    public function getSwResponse()
    {
        return $this->swResponse;
    }

    public function sendHeaders()
    {
        $headers = $this->getHeaders();
        if ($headers->count > 0) {
            foreach ($headers as $name => $values) {
                $name = str_replace(' ', '-', ucwords(str_replace('-', ' ', $name)));
                foreach ($values as $value) {
                    $this->swResponse->header($name, $value);
                }
            }
        }
        $this->swResponse->status($this->getStatusCode());
    }

    public function sendContent()
    {
        if ($this->stream === null) {
            if ($this->content) {
                $this->swResponse->end($this->content);
            } else {
                $this->swResponse->end();
            }
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
                flush(); // Free up memory. Otherwise large files will trigger PHP's memory limit.
            }
            fclose($handle);
        } else {
            while (!feof($this->stream)) {
                $this->swResponse->write(fread($this->stream, $chunkSize));
                flush();
            }
            fclose($this->stream);
        }
        $this->swResponse->end();
    }
}