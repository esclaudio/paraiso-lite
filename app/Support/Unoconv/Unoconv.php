<?php

namespace App\Support\Unoconv;

use Psr\Log\LoggerInterface;

class Unoconv
{
    protected $bin;
    protected $logger;
    protected $output;
    protected $command;
    protected $allowedTypes = [
        'pdf' => [
            'text/plain',
            'application/msword',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.oasis.opendocument.text',
            'application/vnd.oasis.opendocument.spreadsheet',
        ]
    ];

    public function __construct(string $bin = null, LoggerInterface $logger = null)
    {
        $this->bin = $bin ?: 'unoconv';
        $this->logger = $logger;
    }

    /**
     * Get the last output
     * @return string [description]
     */
    public function getLastOutput() : string
    {
        return $this->output;
    }

    /**
     * Get the last executed command
     * @return string [description]
     */
    public function getLastCommand() : string
    {
        return $this->command;
    }

    /**
     * Convert
     * @param  string $format Format
     * @param  string $path   Path
     * @param  string $output Output
     * @return bool
     */
    public function convertTo(string $format, string $path, string $output) : bool
    {
        if ($this->canConvertTo($format, $path)) {
            $this->command = "{$this->bin} -f {$format} -o {$output} {$path}";

            system($this->command, $this->output);

            if ($this->logger && $this->output) {
                $this->logger->error($this->command, [
                    'output' => $this->output
                ]);
            }

            return $this->output == '0';
        }

        return false;
    }

    /**
     * Convert to pdf
     * @param  string $path   Path
     * @param  string $output Output
     * @return bool
     */
    public function convertToPdf(string $path, string $output) : bool
    {
        return $this->convertTo('pdf', $path, $output);
    }

    /**
     * Check if can convert
     * @param  string $format Format
     * @param  string $path   Path
     * @return bool
     */
    public function canConvertTo(string $format, string $path) : bool
    {
        // BUG: If document is saved from LibreOffice, the mimetype is application/octet-stream
        //      https://bugs.documentfoundation.org/show_bug.cgi?id=101317

        if (array_key_exists($format, $this->allowedTypes)) {
            $mimetype = mime_content_type($path);
            return in_array($mimetype, $this->allowedTypes[$format]);
        }
        
        return false;
    }
 }
