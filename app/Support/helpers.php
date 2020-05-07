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

if ( ! function_exists('filter_uploads')) {
    /**
     * Filter uploads.
     *
     * @param \Slim\Http\Request $request
     * @return array
     */
    function filter_uploads(\Slim\Http\Request $request): array
    {
        $uploads = [];

        foreach($request->getUploadedFiles() as $name => $upload) {
            $error = $upload->getError();

            if ($error === UPLOAD_ERR_OK) {
                $uploads[$name] = $upload;
            } else if($error === UPLOAD_ERR_NO_FILE) {
                $uploads[$name] = null;
            } else {
                throw new \Exception(sprintf('Upload error nro. %s', $error));
            }
        }

        return $uploads;
    }
}

if ( ! function_exists('genv')) {
    /**
     * Gets the value of an environment variable.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    function genv(string $key, $default = null)
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
