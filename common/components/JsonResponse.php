<?php

namespace common\components;

class JsonResponse
{

    private $success = false;
    private $message = '';
    private $params = [];

    public function success(bool $success = true): self
    {
        $new = clone $this;
        $new->success = $success;
        return $new;
    }

    public function message(string $message = ''): self
    {
        $new = clone $this;
        $new->message = $message;
        return $new;
    }

    public function params(array $params = []): self
    {
        $new = clone $this;
        $new->params = $params;
        return $new;
    }

    public function asArray(): array
    {
        $params = [
            'success' => $this->success,
        ];
        if (!empty($this->message)) {
            $params['message'] = $this->message;
        }
        return array_merge($this->params, $params);
    }
}
