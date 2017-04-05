<?php

/*
   *  (c) 2016 Aktsiamaailm OÜ - Kõik õigused kaitstud
 *  Litsentsitingimused on saadaval http://www.e-abi.ee/litsentsitingimused
 *  
 *  (c) 2016 Aktsiamaailm OÜ - All rights reserved
 *  Licence terms are available at http://en.e-abi.ee/litsentsitingimused
 *  

 */

/**
 * Description of Csvparser
 *
 * @author Matis
 */
class Eabi_Woocommerce_Postoffice_Model_Csvparser {


    protected static $_encoding = 'UTF-8';
    
    public function setEncoding($encoding) {
        self::$_encoding = $encoding;
        return $this;
    }
    public function getEncoding() {
        return self::$_encoding;
    }
    public function resetEncoding() {
        self::$_encoding = 'UTF-8';
    }
    
    protected $_allowedContentTypes = array(
        'text/plain',
        'plain/text',
        'text/csv',
        'application/csv',
        'application/x-csv',
        'application/vnd.ms-excel',
    );
    
    public function removeUtf8Bom($text) {
        return str_replace("\xef\xbb\xbf", '', $text);
        $bom = pack('H*', 'EFBBBF');
        $text = preg_replace("/^$bom/", '', $text);
        
        return $text;
    }
    
    
    public function getCsvContentTypes() {
        return $this->_allowedContentTypes;
    }
    

    /**
     * <p>Converts CSV file contents to associative PHP array</p>
     * @param string $input UTF-8 encoded contents from CSV file
     * @param bool $withHeaders when true, then first line contents shall be used as array keys for all following lines
     * @param bool|string $keyIndex Allows to index each line by the value of indicated column. Requires $withHeaders set to true
     * @param string $delimiter symbol, which delimits values on each line
     * @param string $enclosure symbol, which allows to enclose the value in a way that delimiters would be ignored within enclosures
     * @param string $escape escape character to escape enclosures
     * @return array
     * @throws Exception when $keyIndex has been set, but CSV file does not contain the index
     */
    public function getData($input, $withHeaders = false, $keyIndex = false, $delimiter = ',', $enclosure = '"', $escape = '\\') {
        if (stripos($this->getEncoding(), 'utf-8') === 0) {
            //utf-8 encoding remove utf-8 bom
            $input = $this->removeUtf8Bom($input);
        }

        //get CSV lines
        $rawData = array_filter(array_map('trim', explode("\n", $input)));
        $rawCsvItems = array();
        //parse the lines into array
        foreach ($rawData as $csvLine) {
            if ($csvLine != '') {
                $rawCsvItems[] = $this->csv2array($csvLine, $delimiter, $enclosure, $escape);
            }
        }
        $headers = false;
        if ($withHeaders) {
            //remove the first item
            $headers = array_shift($rawCsvItems);
        } else {
            if (count($rawCsvItems) > 0) {
                $cnt = count($rawCsvItems[0]);
                $headers = array();
                for ($i = 0; $i < $cnt; $i++) {
                    $headers[] = $i;
                }
            }
        }

        $finalCsvItems = array();

        foreach ($rawCsvItems as $rawCsvItem) {
            $finalCsvItem = array();
            foreach ($rawCsvItem as $key => $element) {
                if (isset($headers[$key])) {
                    $finalCsvItem[$headers[$key]] = $element;
                } else {
                    $finalCsvItem[] = $element;
                }
            }
            if ($keyIndex === false) {
                $finalCsvItems[] = $finalCsvItem;
            } else {
                if (isset($finalCsvItem[$keyIndex])) {
                    //key index as header name
                    $finalCsvItems[$finalCsvItem[$keyIndex]] = $finalCsvItem;
                } else {
                    //key index as numeric index
                    if (!isset($headers[$keyIndex])) {
                        throw new Exception(sprintf("Error when fetching information from CSV file. Key index %s does not exist", $keyIndex));
                    }
                    $finalCsvItems[$headers[$keyIndex]] = $finalCsvItem;
                }
            }
        }

        return $finalCsvItems;
    }
    
    
    
    /**
     * <p>Converts single line of CSV to php indexed array</p>
     * @param string $input one line input
     * @param string $delimiter
     * @param string $enclosure
     * @param string $escape
     * @return array
     * 
     * 
     */
    public function csv2array($input, $delimiter = ',', $enclosure = '"', $escape = '\\') {
//        $fields = explode($enclosure . $delimiter . $enclosure, substr($input, 1, -1));
        $encoding = $this->getEncoding();
        
        $length = mb_strlen($input, $encoding);
        
        
        $word = '';
        $insideEnclosure = false;
        $fields = array();
        for ($i = 0; $i < $length; $i++) {
            $char = mb_substr($input, $i, 1, $encoding);
            $prevChar = ($i > 0)?mb_substr($input, $i - 1, 1, $encoding):false;
            
            $wentInEnclosure = false;
            if (!$insideEnclosure && $enclosure && $char === $enclosure) {
                $insideEnclosure = true;
                $wentInEnclosure = true;
            }
            
            if ($insideEnclosure && !$wentInEnclosure && $char === $enclosure && $prevChar !== $escape) {
                $insideEnclosure = false;
            }
            
            if ($char === $delimiter && !$insideEnclosure) {
                $fields[] = trim(trim($word, $enclosure));
                $word = '';
            } else {
                $word .= $char;
            }
           
        }
        
        if (mb_strlen($word, $encoding) > 0) {
//            echo '<pre>'.htmlspecialchars(print_r($word, true), ENT_COMPAT | ENT_HTML401 | ENT_IGNORE).'</pre>';
            
            $fields[] = trim(trim($word, $enclosure));
            
        }

        foreach ($fields as $key => $value) {
            //make sure that output is always utf-8
            $value = mb_convert_encoding($value, 'UTF-8', $this->getEncoding());
            $fields[$key] = str_replace($escape . $enclosure, $enclosure, $value);
        }
        
        return $fields;
    }
    
    
    
}
