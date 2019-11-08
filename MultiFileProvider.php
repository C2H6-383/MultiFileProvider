<?php

namespace {

    /**
     * A small PHP class for combining multiple CSS/JS files into one File dynamically over the url, with modification and 304 headers.
     * @author Ethan Finley <github@ethan-finley.de>
     * @name MultiFileProvider
     * @version 1.1
     */
    class MultiFileProvider {

        /**
         * @var string Contains the Class Version
         */
        public $version = "1.1";

        /**
         *
         * @var string The Mime Typ which is used by the class for the headers
         */
        private $mimeType;

        /**
         *
         * @var string Replace Word for the Website root path
         */
        private $rootWord="DEFAULT_ROOTWORD_afoiudihgfsoidfgklsgfddfg6864646465";

        /**
         *
         * @var string the top adress for this Site (for autoreplace of CSS/JS include paths)
         */
        private $SiteTopAddress;

        /**
         *
         * @var string[] Contains all Allowed MIME Types (only these are tested with the class)
         */
        public static $allowedMimeTypes = [
            "js" => "application/javascript",
            "css" => "text/css"
        ];

        /**
         *
         * @var \MultiFileProvider\FileContainer[] all added Files
         */
        private $allFiles = [];

        /**
         *
         * @var \MultiFileProvider\FileContainer[] the Files wich get requested by the browser
         */
        private $requestedFiles = [];

        /**
         *
         * @var bool if the headers are sent
         */
        private $HeadersSent = false;

        /**
         * Creates the structure for a MultiFileProvider
         * @param string $mimeType the mime Type used later for headers
         * @param string $SiteTopAddress the top adress for this Site (for autoreplace of CSS/JS include paths)
         * @throws Exception If the $mimeType is not in $allowedMimeTypes
         */
        public function __construct($mimeType, $SiteTopAddress="http://stackoverflow.com") {
            if (!in_array($mimeType, MultiFileProvider::$allowedMimeTypes)) {
                throw new Exception("given MimeType not allowed.");
            }


            $this->SiteTopAddress = $SiteTopAddress;
            $this->mimeType = $mimeType;
        }

        /**
         * sets the Root Word for Replace with the Website Path
         * @param string $newWord the new word
         */
        public function set_RootWord($newWord) {
            $this->rootWord = $newWord;
        }

        /**
         * Adds a file for a URL Key word
         * @param string $urlKeyWord the URL Key word for the included files
         * @param string $FileName the File Name for the include file
         */
        public function addFile($urlKeyWord, $FileName) {
            if (!file_exists($FileName)) {
                throw new Exception("File $FileName does not exist in this directory.");
            }

            $newFile = new MultiFileProvider\FileContainer($urlKeyWord, $FileName);

            $this->allFiles[] = $newFile;
        }

        /**
         * Adds Multiple Include Files for one URL Key Word
         * @param string $urlKeyWord the URL Key word for the included files
         * @param string[] $FileNames the File Names for the include files
         */
        public function addFiles($urlKeyWord, $FileNames) {
            foreach ($FileNames as $FileName) {
                $this->addFile($urlKeyWord, $FileName);
            }
        }

        /**
         * extracts the Keywords for the requested files from the URL
         * @return string[] All Keywords in the called URL
         */
        private function getKeyWordsFromURL() {
            if (isset($_SERVER["PATH_INFO"])) {
                $p = explode("/", $_SERVER["PATH_INFO"]);
                array_shift($p);
                $p = array_unique($p);
                $p = array_values($p);
                return $p;
            } else {
                die();
            }
        }

        /**
         * Dumps a Variable into &lt;pre&gt; tags
         * @param mixed $var the variable to dump
         */
        private function betterVarDump($var) {
            echo "<pre>";
            var_dump($var);
            echo "</pre>";
        }

        /**
         * returns the newest File of multiple Files
         * @param \MultiFileProvider\FileContainer[] $Files the Files of which the newest should be returned
         * @return \MultiFileProvider\FileContainer the newest File
         */
        private function getNewestFile($Files) {
            $newestFile = $Files[0];
            foreach ($Files as $File) {
                $File->update_LastModified();
                if ($File->get_LastModified() > $newestFile->get_LastModified()) {
                    $newestFile = $File;
                }
            }

            return $newestFile;
        }

        /**
         * sends the PHP headers for chaching, content-Type and Modification, if not already done
         * @param \MultiFileProvider\FileContainer $newestFile
         */
        private function outputStandardHTTPHeaders($newestFile) {
            if (!$this->HeadersSent) {
                $this->HeadersSent = true;
                header("Content-Type: " . $this->mimeType);
                header("Cache-Control: max-age=0, public, must-revalidate");
                header('Last-Modified: ' . $newestFile->get_LastModified_date(true));
            }
        }

        /**
         * sends the PHP headers for chaching, content-Type and Modification, if not already done
         * @param \MultiFileProvider\FileContainer $newestFile
         */
        private function outputNotModifiedHTTPHeaders($newestFile) {
            if (!$this->HeadersSent) {
                $this->HeadersSent = true;
                header("Cache-Control: max-age=0, public, must-revalidate");
                header('Last-Modified: ' . $newestFile->get_LastModified_date(true));
                header('HTTP/1.0 304 Not Modified');
            }
        }

        /**
         * Outputs the visible Text header for the file
         * @param int $FileCount the Count of the files, later sent
         */
        private function outputFileTextHeader($FileCount) {
            echo "\n/*\n\n Dynamic File, Provided by MultiFileProvider $this->version \n File type: $this->mimeType \n File count: $FileCount \n\n #################################################\n\n*/\n\n";
        }

        /**
         * Outputs the visible Text footer for the file
         */
        private function outputFileTextFooter() {
            echo "\n/*\n\n #################################################\n\n Dynamic File ended. \n MultiFileProvider -> (C) Ethan Ziermann, Germany \n https://github.com/C2H6-383/MultiFileProvider\n\n*/\n\n ";
        }

        /**
         * Outputs the Given File with some File Infos into the document body
         * @param \MultiFileProvider\FileContainer $File the File to output
         */
        private function outputFile($File) {
            $file_Content = file_get_contents($File->get_FileName());
            $file_Content = str_replace($this->rootWord, $this->SiteTopAddress, $file_Content);
            //$file_Content = str_replace("../", $this->SiteTopAddress, $file_Content);
            echo "\n";
            echo "/*\n#################################################\n";
            echo "File name: " . $File->get_FileName() . "  \n";
            echo "Last Edited: " . $File->get_LastModified_date() . "  \n";
            echo "#################################################\n*/\n";
            echo "" . $file_Content;
        }

        /**
         * Creates the Output for the full file
         * @param MultiFileProvider\FileContainer $Files all Files for Output
         * @param MultiFileProvider\FileContainer $newestFile the newest File of all Files
         */
        private function createOutput($Files, $newestFile) {
            $this->outputStandardHTTPHeaders($newestFile);
            $this->outputFileTextHeader(count($Files));
            foreach ($Files as $File) {
                $this->outputFile($File);
            }
            $this->outputFileTextFooter();
        }

        /**
         * creates the File and sends it to the Browser
         */
        public function createMultiFile() {

            $RequestedKeywords = $this->getKeyWordsFromURL();
            foreach ($this->allFiles as $File) {
                if (in_array($File->get_KeyWord(), $RequestedKeywords)) {
                    $this->requestedFiles[] = $File;
                }
            }

            $newestFile = $this->getNewestFile($this->requestedFiles);

            if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
                $newestBrowser = strtotime($_SERVER["HTTP_IF_MODIFIED_SINCE"]);
                if ($newestFile->get_LastModified() > $newestBrowser) {
                    $this->createOutput($this->requestedFiles, $newestFile);
                } else {
                    $this->outputNotModifiedHTTPHeaders($newestFile);
                }
            } else {
                $this->createOutput($this->requestedFiles, $newestFile);
            }
        }

    }

}

namespace MultiFileProvider {

    /**
     * File Container class for MultiFileProvider Class
     * @author Ethan Ziermann <null@unconfigured.com>
     * @see MultiFileProvider
     */
    class FileContainer {

        /**
         *
         * @var string URL Keyword for this File to get Included
         */
        private $keyWord;

        /**
         *
         * @var string file name for the file
         */
        private $FileName;

        /**
         *
         * @var int last modified timestamp
         */
        private $LastModified;

        /**
         * Creates a File Container for the MultiFileProviderClass
         * @param string $keyWord URL Keyword for this File to get Included
         * @param string $FileName file name for the file
         */
        public function __construct($keyWord, $FileName) {
            $this->keyWord = $keyWord;
            $this->FileName = $FileName;
            $this->LastModified = filemtime($FileName);
        }

        /**
         * Updates the LastModified Setting in this Class of this FIle
         */
        public function update_LastModified() {
            $this->LastModified = filemtime($this->FileName);
        }

        /**
         * returns the Filename
         * @return string the File Name
         */
        public function get_FileName() {
            return $this->FileName;
        }

        /**
         * sets the FileName
         * @param string $NewName the new name for the file
         */
        public function set_FileName($NewName) {
            $this->FileName = $NewName;
        }

        /**
         * returns the last Modified timestamp
         * @return int the Last Modfied timestamp
         */
        public function get_LastModified() {
            return $this->LastModified;
        }

        /**
         * returns the URL Keyword for the file
         * @return string the Keyword for the file
         */
        public function get_KeyWord() {
            return $this->keyWord;
        }

        /**
         * gets the last Modification timestamp as date or GMT date
         * @param bool $gmt should it be in GMT (default is false)
         * @return string the date or gmdate
         */
        public function get_LastModified_date($gmt = false) {
            if ($gmt) {
                return gmdate('D, d M Y H:i:s', $this->LastModified) . ' GMT';
            } else {
                return date("d.m.Y H:i:s", $this->LastModified);
            }
        }

    }

}
