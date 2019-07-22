<?php

namespace {

    /**
     * A small PHP class for combining multiple CSS/JS files into one File dynamically over the url, with modification and 304 headers.
     * @author Ethan Ziermann <null@unconfigured.com>
     * @name MultiFileProvider
     * @version 1.0
     */
    class MultiFileProvider {

        /**
         * @var string Contains the Class Version
         */
        public $version = "1.0";

        /**
         *
         * @var string The Mime Typ which is used by the class for the headers
         */
        private $mimeType;

        /**
         *
         * @var string[] Contains all Allowed MIME Types (only these are tested with the class)
         */
        public $allowedMimeTypes = [
            "application/javascript", "text/css"
        ];

        /**
         * Constructs the object
         * @param type $mimeType the mime Type used later for headers
         * @throws Exception If the $mimeType is not in $allowedMimeTypes
         */
        public function __construct($mimeType) {
            if (!in_array($mimeType, $this->allowedMimeTypes)) {
                throw new Exception("given MimeType not allowed.");
            }

            $this->mimeType = $mimeType;
        }

        /**
         * Adds a file for a URL Key word
         * @param string $urlKeyWord the URL Key word for the included files
         * @param string $FileName the File Name for the include file
         */
        public function addFile($urlKeyWord, $FileName) {
            throw new Exception("Not working");
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

    }

}

namespace MultiFileProvider {

    /**
     * File class for MultiFileProvider Class
     * @author Ethan Ziermann <null@unconfigured.com>
     * @see MultiFileProvider
     */
    class MFP_File {

        /**
         *
         * @var string URL Keyword for this File to get Included 
         */
        public $keyWord;
        
        /**
         *
         * @var string file name for the file 
         */
        public $FileName;

        /**
         * 
         * @param string $keyWord URL Keyword for this File to get Included 
         * @param string $FileName file name for the file 
         */        
        public function __construct($keyWord, $FileName) {
            $this->keyWord = $keyWord;
            $this->FileName = $FileName;
        }

    }

}