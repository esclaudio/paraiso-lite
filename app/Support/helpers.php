<?php

if ( ! function_exists('bcrypt')) {
    /**
     * Hash a given value.
     *
     * @param string $value
     * @return string
     */
    function bcrypt(string $value): string
    {
        return password_hash($value, PASSWORD_BCRYPT, ['cost' => 10]);
    }
}

if ( ! function_exists('random_string')) {
    /**
     * Generate a "random" alpha-numeric string.
     *
     * @param integer $length
     * @return string
     */
    function random_string(int $length = 16): string
    {
        $string = '';

        while (($len = strlen($string)) < $length) {
            $size = $length - $len;

            $bytes = random_bytes($size);

            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }

        return $string;
    }
}

if ( ! function_exists('factory')) {
    /**
     * Generate a factory.
     *
     * @param string $model
     * @param integer $times
     * @return \Illuminate\Database\Eloquent\FactoryBuilder
     */
    function factory(string $model, int $times = 0): \Illuminate\Database\Eloquent\FactoryBuilder
    {
        $faker = Faker\Factory::create();
        $factory = (new \Illuminate\Database\Eloquent\Factory($faker))
            ->load('database/factories')
            ->of($model);

        if ($times > 0) {
            return $factory->times($times);
        }

        return $factory;
    }
}

if ( ! function_exists('trans')) {
    /**
     * Translate the given message.
     *
     * @param string $key
     * @return string|null
     */
    function trans(string $key): ?string
    {
        return App\Support\Facades\Translator::trans($key);
    }
}

if ( ! function_exists('get_upload')) {
    /**
     * Upload
     *
     * @param string $name
     * @param \Slim\Http\Request $request
     * @return \Slim\Http\UploadedFile|null
     */
    function get_upload(string $name, \Slim\Http\Request $request): ?\Slim\Http\UploadedFile
    {
        $upload = $request->getUploadedFiles()[$name] ?? null;

        if ($upload && $upload->getError() === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        return $upload;
    }
}

if ( ! function_exists('get_env')) {
    /**
     * Gets the value of an environment variable.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    function get_env(string $key, $default = null)
    {
        $value = $_ENV[$key] ?? null;

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return;
        }

        return $value ?? $default;
    }
}
