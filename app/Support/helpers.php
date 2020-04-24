<?php


if ( ! function_exists('bcrypt')) {
    /**
     * Hash a given value
     * 
     * @param  string $value
     * 
     * @return string
     */
    function bcrypt(string $value): string
    {
        return password_hash($value, PASSWORD_BCRYPT, ['cost' => 10]);
    }
}

if ( ! function_exists('public_path')) {
    /**
     * Get the path to the public folder
     *
     * @param  string|null  $path
     * 
     * @return string
     */
    function public_path(string $path = null): string
    {
        return PUBLIC_PATH . ($path ? DIRECTORY_SEPARATOR.ltrim($path, DIRECTORY_SEPARATOR) : $path);
    }
}

if ( ! function_exists('storage_path')) {
    /**
     * Get the path to the storage folder
     *
     * @param  string|null  $path
     * 
     * @return string
     */
    function storage_path(string $path = null): string
    {
        return FILES_PATH . ($path ? DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : $path);
    }
}

if ( ! function_exists('storage_make')) {
    /**
     * Create a directory inside the storage folder
     *
     * @param  string  $path
     * 
     * @return string|null
     */
    function storage_make(string $path)
    {
        $path = FILES_PATH . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR);
        $oldmask = umask(0);

        if (file_exists($path) || mkdir($path, 0770, true)) {
            return $path;
        }

        umask($oldmask);
        return null;
    }
}


if ( ! function_exists('storage_put')) {
    /**
     * Puts a file inside the storage folder
     *
     * @param  string $path
     * @param  Slim\Http\UploadedFile $upload
     * 
     * @return string
     */
    function storage_put(string $path, Slim\Http\UploadedFile $upload, string $extension = null)
    {
        $path = FILES_PATH . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR);
        
        if ( ! file_exists($path)) {
            $oldmask = umask(0);
            mkdir($path, 0770, true);
            umask($oldmask);
        }

        if ($extension === null) {
            $extension = pathinfo($upload->getClientFilename(), PATHINFO_EXTENSION);
        }
        
        $filename = md5(uniqid());
        $path = sprintf('%s/%s%s', $path, $filename, $extension);
        
        $upload->moveTo($path);

        return $path;
    }
}

if ( ! function_exists('str_random')) {
    /**
     * Generate a more truly "random" alpha-numeric string.
     *
     * @param  int  $length
     * 
     * @return string
     */
    function str_random($length = 16)
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
     * Factory
     *
     * @param  string  $model
     * @param  int $times
     * 
     * @return \Illuminate\Database\Eloquent\FactoryBuilder
     */
    function factory(string $model, int $times = 0)
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
     * @param  string  $key
     * 
     * @return string|null
     */
    function trans(string $key): string
    {
        return App\Facades\Translator::trans($key);
    }
}

if ( ! function_exists('uploads')) {
    /**
     * Uploads
     *
     * @param  \Slim\Http\Request $request
     * 
     * @return array
     */
    function uploads(\Slim\Http\Request $request): array
    {
        $files = [];

        foreach($request->getUploadedFiles() as $name => $upload) {
            $error = $upload->getError();

            if ($error === UPLOAD_ERR_OK) {
                $files[$name] = $upload;
            } else if($error === UPLOAD_ERR_NO_FILE) {
                $files[$name] = null;
            } else {
                throw new \Exception(sprintf('Upload error nro. %s', $error));
            }
        }

        return $files;
    }
}

if ( ! function_exists('array_input')) {
    /**
     * Converts an array of inputs into an array
     *
     * @param  array $form
     * 
     * @return array
     */
    function array_input(array $inputs): array
    {
        $array = [];

        foreach ($inputs as $field => $values) {
            $i = 0;

            foreach ($values as $value) {
                $array[$i][$field] = $value;
                $i++;
            }
        }

        return $array;
    }
}