<?php
/**
 * This source file is available under  GNU General Public License version 3 (GPLv3)
 *
 * Full copyright and license information is available in LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Asioso GmbH (https://www.asioso.com)
 *
 */


namespace DooFinderBundle\File;


use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Class FileHandler
 * @package DooFinderBundle\File
 */
class FileHandler
{
    /**
     * @var string
     */
    private $dataDirectory;

    /**
     * @var Finder
     */
    private $finder;
    /**
     * @var Filesystem
     */
    private $fileSystem;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var array
     */
    private $files = array();

    /**
     * @var \DateTime
     */
    private $now;


    /**
     * AbstractImportHelper constructor.
     * @param $dataDirectory
     * @param LoggerInterface $logger
     */
    public function __construct($dataDirectory, LoggerInterface $logger = null)
    {
        $this->dataDirectory = $dataDirectory;
        $this->finder = new Finder();
        $this->fileSystem = new Filesystem();
        $this->logger = $logger;
        $this->now = new \DateTime();
    }


    /**
     * @param $path
     * @return SplFileInfo
     * @throws FileNotFoundException
     */
    protected function findFile($path): SplFileInfo
    {
        $this->path = $path;

        $this->finder->files()->name($path);
        $this->finder->in($this->dataDirectory);
        if ($this->finder->hasResults()) {
            foreach ($this->finder as $file) {
                /**
                 * @var $file SplFileInfo
                 */
                if ($file->getFilename() == $path) {
                    return $file;
                }
            }
        }
        throw new FileNotFoundException(sprintf("no File for this path: %s", $path));
    }


    /**
     * @param $hashId
     * @param $type
     * @return SplFileInfo
     * @throws \Pimcore\Document\Tag\Exception\NotFoundException
     */
    public function findLatestFileForEngine($hashId, $type): SplFileInfo
    {

        $nameTxt = '/.+_feed_' . $hashId . '_' . $type . '(\.txt|\.gz$)/';

        $this->finder->files()->name($nameTxt)->sortByModifiedTime();
        $returnFile = null;

        foreach ($this->finder->in($this->dataDirectory) as $file) {

            $returnFile = $file;
        }

        if ($returnFile != null) {
            return $returnFile;
        }

        throw new FileNotFoundException();
    }

    /**
     * @param $hashId
     * @param $dataType
     * @param $data
     * @param $headers
     * @param string $delimiter
     * @param string $enclosure
     * @return bool
     */
    public function writeToFile($hashId, $dataType, $data, $headers, $delimiter = ";", $enclosure = '"')
    {

        if (empty($data)) {
            return false;
        }

        $data = $this->matchDataToHeader($data, $headers[$hashId . $dataType]);

        #fputcsv($this->getFileHandler($hashId, $dataType, $headers, $delimiter, $enclosure), $data);
        $data = implode('|', $data);
        file_put_contents($this->getFileHandler($hashId, $dataType, $headers), $data . PHP_EOL, FILE_APPEND | LOCK_EX);
        return true;
    }

    /**
     * @param $hashId
     * @param $dataType
     * @param $headers
     * @param string $delimiter
     * @param string $enclosure
     * @return mixed
     */
    private function getFileHandler($hashId, $dataType, $headers, $delimiter = ";", $enclosure = '"')
    {

        if (!isset($this->files[$hashId . $dataType])) {
            $this->createFile($hashId, $dataType, $headers, $delimiter, $enclosure);
        }
        return $this->files[$hashId . $dataType];
    }

    /**
     * @param $hashid
     * @param $dataType
     * @param $headers
     * @param string $delimiter
     * @param string $enclosure
     */
    private function createFile($hashId, $dataType, $headers, $delimiter = ";", $enclosure = '"')
    {

        $path = $this->now->format('Ymd_His') . '_feed_' . $hashId . '_' . $dataType . ".txt";
        $this->fileSystem->touch($this->dataDirectory . $path);
        $this->fileSystem->chmod($this->dataDirectory . $path, 0774, 0000, false);
        $file = $this->findFile($path);

        $this->files[$hashId . $dataType] = $file->getPathname();
        //write header in there!
        //fputcsv(, $headers[$hashId.$dataType], $delimiter, $enclosure);
        $data = implode('|', $headers[$hashId . $dataType]);

        file_put_contents($this->getFileHandler($hashId, $dataType, $headers), $data . PHP_EOL, FILE_APPEND | LOCK_EX);


    }

    /**
     * @param $data
     * @param $header
     * @return array
     */
    private function matchDataToHeader($data, $header): array
    {
        $feed = array();
        foreach ($header as $headerItem) {
            if (isset($data[$headerItem])) {
                $feed[] = $data[$headerItem];
                continue;
            }
            $feed[] = "";
        }
        return $feed;
    }

    public function compressFiles()
    {
        foreach ($this->files as $key => $file) {
            gzcompressfile($file);
            unlink($file);
        }

    }

}