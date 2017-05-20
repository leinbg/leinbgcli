<?php

namespace Leinbg\LeinbgCli\Utils;

use GuzzleHttp\Client;
use ZipArchive;

class File {

    protected $repoUrl;
    protected $repoName;
    protected $fileName;
    protected $temp = 'temp.zip';

    public function __construct($repoUrl, $repoName, $fileName)
    {
        $this->repoUrl = $repoUrl;
        $this->repoName = $repoName;
        $this->fileName = $fileName;
    }

    public function make()
    {
        $this->download()
             ->unzip()
             ->clean();
    }

    public function download()
    {
        (new Client)->request('GET', $this->repoUrl, ['sink' => $this->getTempFile()]);

        return $this;
    }

    public function unzip()
    {
        $zip = new ZipArchive;
        if ($zip->open($this->getTempFile()) !== true) {
            // @todo: throw new Exception("cannot open zip file");
            return $this;
        }
        $zip->extractTo(getcwd());
        $zip->close();

        return $this;
    }

    public function clean()
    {
        unlink($this->getTempFile());
        rename($this->repoName, $this->fileName);
    }

    public function getTempFile()
    {
        return getcwd() . '/' . $this->temp;
    }
}
