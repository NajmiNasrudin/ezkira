<?php

namespace App\Core;

abstract class Controller
{
    /**
     * Render a view inside a layout and send response.
     */
    protected function view(
        string $template,
        array $data = [],
        string $layout = 'main',
        string $title = ''
    ): void {
        echo View::render($template, $data, $layout, $title);
    }

    /**
     * Redirect to a URL and stop execution.
     */
    protected function redirect(string $path): never
    {
        $url = str_starts_with($path, 'http') ? $path : BASE_URI . $path;
        header('Location: ' . $url);
        exit;
    }

    /**
     * Redirect back to the referrer, or a fallback.
     */
    protected function back(string $fallback = '/'): never
    {
        $ref = $_SERVER['HTTP_REFERER'] ?? '';
        $this->redirect($ref ?: $fallback);
    }

    /**
     * Send a JSON response.
     */
    protected function json(array $data, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Abort with an HTTP error page.
     */
    protected function abort(int $code): never
    {
        http_response_code($code);
        $file = BASE_PATH . '/views/errors/' . $code . '.php';
        if (file_exists($file)) {
            include $file;
        } else {
            echo "Error $code";
        }
        exit;
    }

    /**
     * Set a flash message.
     */
    protected function flash(string $key, string $message): void
    {
        Session::flash($key, $message);
    }

    /**
     * Get and clear a flash message.
     */
    protected function getFlash(string $key): ?string
    {
        return Session::getFlash($key);
    }

    /**
     * Validate POST fields. Returns errors array (empty = valid).
     * Rules: 'required', 'email', 'min:N', 'max:N', 'confirmed', 'regex:pattern'
     */
    protected function validate(array $data, array $rules): array
    {
        $errors = [];

        foreach ($rules as $field => $ruleStr) {
            $fieldRules = explode('|', $ruleStr);
            $value      = trim($data[$field] ?? '');
            $label      = ucfirst(str_replace('_', ' ', $field));

            foreach ($fieldRules as $rule) {
                if ($rule === 'required' && $value === '') {
                    $errors[$field] = "{$label} is required.";
                    break;
                }
                if ($rule === 'email' && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field] = "Please enter a valid email address.";
                    break;
                }
                if (str_starts_with($rule, 'min:')) {
                    $min = (int)substr($rule, 4);
                    if (strlen($value) < $min) {
                        $errors[$field] = "{$label} must be at least {$min} characters.";
                        break;
                    }
                }
                if (str_starts_with($rule, 'max:')) {
                    $max = (int)substr($rule, 4);
                    if (strlen($value) > $max) {
                        $errors[$field] = "{$label} must not exceed {$max} characters.";
                        break;
                    }
                }
                if ($rule === 'confirmed') {
                    $confirm = trim($data[$field . '_confirmation'] ?? '');
                    if ($value !== $confirm) {
                        $errors[$field] = "{$label} confirmation does not match.";
                        break;
                    }
                }
                if (str_starts_with($rule, 'regex:')) {
                    $pattern = substr($rule, 6);
                    if ($value !== '' && !preg_match($pattern, $value)) {
                        $errors[$field] = "{$label} format is invalid.";
                        break;
                    }
                }
            }
        }

        return $errors;
    }

    /**
     * Check if request method is POST.
     */
    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Sanitize a string for output.
     */
    protected function sanitize(string $value): string
    {
        return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
    }
}
