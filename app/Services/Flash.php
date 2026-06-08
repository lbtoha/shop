<?php

namespace App\Services;

class Flash
{
    public function __call($name, $arguments)
    {
        if (in_array($name, ['success', 'error', 'warning', 'info'])) {
            session()->flash($name, $arguments[0]);

            return $this;
        }

        throw new \BadMethodCallException("Method {$name} does not exist.");
    }

    public function now($type, $message)
    {
        session()->now($type, $message);

        return $this;
    }

    public function overlay($message, $title = 'Notice')
    {
        session()->flash('overlay', [
            'message' => $message,
            'title' => $title,
        ]);

        return $this;
    }
}
