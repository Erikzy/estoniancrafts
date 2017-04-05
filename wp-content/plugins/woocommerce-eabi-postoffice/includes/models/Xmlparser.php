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
 * Description of Xmlparser
 *
 * @author Matis
 */
class Eabi_Woocommerce_Postoffice_Model_Xmlparser {

    /**
     * To check the allowed nesting depth of the XML tree during xml2json conversion.
     *
     * @var int
     */
    public static $maxRecursionDepthAllowed = 25;

    /**
     * fromXml - Converts XML to JSON
     *
     * Converts a XML formatted string into a JSON formatted string.
     * The value returned will be a string in JSON format.
     *
     * The caller of this function needs to provide only the first parameter,
     * which is an XML formatted String. The second parameter is optional, which
     * lets the user to select if the XML attributes in the input XML string
     * should be included or ignored in xml2json conversion.
     *
     * This function converts the XML formatted string into a PHP array by
     * calling a recursive (protected static) function in this class. Then, it
     * converts that PHP array into JSON by calling the "encode" static funcion.
     *
     * Throws a Exception if the input not a XML formatted string.
     *
     * @access public
     * @param string $xmlStringContents XML String to be converted
     * @param boolean $ignoreXmlAttributes Include or exclude XML attributes in
     * the xml2json conversion process.
     * @return mixed - JSON formatted string on success
     * @throws Exception
     */
    public function fromXml($xmlStringContents, $ignoreXmlAttributes = true, $useNew = false) {
        // Load the XML formatted string into a Simple XML Element object.
        $simpleXmlElementObject = simplexml_load_string($xmlStringContents);

        // If it is not a valid XML content, throw an exception.
        if ($simpleXmlElementObject == null) {
            throw new Exception('Function fromXml was called with an invalid XML formatted string.');
        } // End of if ($simpleXmlElementObject == null)

        $resultArray = null;

        // Call the recursive function to convert the XML into a PHP array.
        if ($useNew) {
            $resultArray = self::_processXml2($simpleXmlElementObject, $ignoreXmlAttributes);
        } else {
            $resultArray = self::_processXml($simpleXmlElementObject, $ignoreXmlAttributes);
        }

        // It is just that simple.
        return $resultArray;
    }

// End of function fromXml.

    /**
     * _processXml - Contains the logic for xml2json
     *
     * The logic in this function is a recursive one.
     *
     * The main caller of this function (i.e. fromXml) needs to provide
     * only the first two parameters i.e. the SimpleXMLElement object and
     * the flag for ignoring or not ignoring XML attributes. The third parameter
     * will be used internally within this function during the recursive calls.
     *
     * This function converts the SimpleXMLElement object into a PHP array by
     * calling a recursive (protected static) function in this class. Once all
     * the XML elements are stored in the PHP array, it is returned to the caller.
     *
     * Throws a Exception if the XML tree is deeper than the allowed limit.
     *
     * @static
     * @access protected
     * @param SimpleXMLElement $simpleXmlElementObject XML element to be converted
     * @param boolean $ignoreXmlAttributes Include or exclude XML attributes in
     * the xml2json conversion process.
     * @param int $recursionDepth Current recursion depth of this function
     * @return mixed - On success, a PHP associative array of traversed XML elements
     * @throws Exception
     */
    protected static function _processXml($simpleXmlElementObject, $ignoreXmlAttributes, $recursionDepth = 0) {
        // Keep an eye on how deeply we are involved in recursion.
        if ($recursionDepth > self::$maxRecursionDepthAllowed) {
            // XML tree is too deep. Exit now by throwing an exception.
            throw new Exception(
            "Function _processXml exceeded the allowed recursion depth of " .
            self::$maxRecursionDepthAllowed);
        } // End of if ($recursionDepth > self::$maxRecursionDepthAllowed)

        if ($recursionDepth == 0) {
            // Store the original SimpleXmlElementObject sent by the caller.
            // We will need it at the very end when we return from here for good.
            $callerProvidedSimpleXmlElementObject = $simpleXmlElementObject;
        } // End of if ($recursionDepth == 0)

        if ($simpleXmlElementObject instanceof SimpleXMLElement) {
            // Get a copy of the simpleXmlElementObject
            $copyOfSimpleXmlElementObject = $simpleXmlElementObject;
            // Get the object variables in the SimpleXmlElement object for us to iterate.
            $simpleXmlElementObject = get_object_vars($simpleXmlElementObject);
        } // End of if (get_class($simpleXmlElementObject) == "SimpleXMLElement")
        // It needs to be an array of object variables.
        if (is_array($simpleXmlElementObject)) {
            // Initialize a result array.
            $resultArray = array();
            // Is the input array size 0? Then, we reached the rare CDATA text if any.
            if (count($simpleXmlElementObject) <= 0) {
                // Let us return the lonely CDATA. It could even be
                // an empty element or just filled with whitespaces.
                return (trim(strval($copyOfSimpleXmlElementObject)));
            } // End of if (count($simpleXmlElementObject) <= 0)
            // Let us walk through the child elements now.
            foreach ($simpleXmlElementObject as $key => $value) {
                // Check if we need to ignore the XML attributes.
                // If yes, you can skip processing the XML attributes.
                // Otherwise, add the XML attributes to the result array.
                if (($ignoreXmlAttributes == true) && (is_string($key)) && ($key == "@attributes")) {
                    continue;
                } // End of if(($ignoreXmlAttributes == true) && ($key == "@attributes"))
                // Let us recursively process the current XML element we just visited.
                // Increase the recursion depth by one.
                $recursionDepth++;
                $resultArray[$key] = self::_processXml($value, $ignoreXmlAttributes, $recursionDepth);

                // Decrease the recursion depth by one.
                $recursionDepth--;
            } // End of foreach($simpleXmlElementObject as $key=>$value) {

            if ($recursionDepth == 0) {
                // That is it. We are heading to the exit now.
                // Set the XML root element name as the root [top-level] key of
                // the associative array that we are going to return to the original
                // caller of this recursive function.
                $tempArray = $resultArray;
                $resultArray = array();
                $resultArray[$callerProvidedSimpleXmlElementObject->getName()] = $tempArray;
            } // End of if ($recursionDepth == 0)

            return($resultArray);
        } else {
            return (trim(strval($simpleXmlElementObject)));
        } // End of if (is_array($simpleXmlElementObject))
    }

// End of function _processXml.

    /**
     * _processXml - Contains the logic for xml2json
     *
     * The logic in this function is a recursive one.
     *
     * The main caller of this function (i.e. fromXml) needs to provide
     * only the first two parameters i.e. the SimpleXMLElement object and
     * the flag for ignoring or not ignoring XML attributes. The third parameter
     * will be used internally within this function during the recursive calls.
     *
     * This function converts the SimpleXMLElement object into a PHP array by
     * calling a recursive (protected static) function in this class. Once all
     * the XML elements are stored in the PHP array, it is returned to the caller.
     *
     * Throws a Exception if the XML tree is deeper than the allowed limit.
     *
     * @param SimpleXMLElement $simpleXmlElementObject
     * @param boolean $ignoreXmlAttributes
     * @param integer $recursionDepth
     * @return array
     */
    protected static function _processXml2($simpleXmlElementObject, $ignoreXmlAttributes, $recursionDepth = 0) {
        // Keep an eye on how deeply we are involved in recursion.
        if ($recursionDepth > self::$maxRecursionDepthAllowed) {
            // XML tree is too deep. Exit now by throwing an exception.
            throw new Exception(
            "Function _processXml exceeded the allowed recursion depth of " .
            self::$maxRecursionDepthAllowed);
        } // End of if ($recursionDepth > self::$maxRecursionDepthAllowed)

        $children = $simpleXmlElementObject->children();
        $name = $simpleXmlElementObject->getName();
        $value = self::_getXmlValue($simpleXmlElementObject);
        $attributes = (array) $simpleXmlElementObject->attributes();

        if (count($children) == 0) {
            if (!empty($attributes) && !$ignoreXmlAttributes) {
                foreach ($attributes['@attributes'] as $k => $v) {
                    $attributes['@attributes'][$k] = self::_getXmlValue($v);
                }
                if (!empty($value)) {
                    $attributes['@text'] = $value;
                }
                return array($name => $attributes);
            } else {
                return array($name => $value);
            }
        } else {
            $childArray = array();
            foreach ($children as $child) {
                $childname = $child->getName();
                $element = self::_processXml2($child, $ignoreXmlAttributes, $recursionDepth + 1);
                if (array_key_exists($childname, $childArray)) {
                    if (empty($subChild[$childname])) {
                        $childArray[$childname] = array($childArray[$childname]);
                        $subChild[$childname] = true;
                    }
                    $childArray[$childname][] = $element[$childname];
                } else {
                    $childArray[$childname] = $element[$childname];
                }
            }
            if (!empty($attributes) && !$ignoreXmlAttributes) {
                foreach ($attributes['@attributes'] as $k => $v) {
                    $attributes['@attributes'][$k] = self::_getXmlValue($v);
                }
                $childArray['@attributes'] = $attributes['@attributes'];
            }
            if (!empty($value)) {
                $childArray['@text'] = $value;
            }
            return array($name => $childArray);
        }
    }

    /**
     * Return the value of an XML attribute text or the text between
     * the XML tags
     *
     *
     * @param SimpleXMLElement $simpleXmlElementObject
     * @return string
     */
    protected static function _getXmlValue($simpleXmlElementObject) {
        return (trim(strval($simpleXmlElementObject)));
    }

    public function toXml($variable, array $attributes = array()) {
        $line = ""; //\r\n";

        $str = '<?xml version="1.0" encoding="utf-8"?>' . $line;
        if (is_array($variable)) {
            $this->toXmlArray($str, '', $variable, -1);
        } else {
            throw new Exception('invalid input, cannot convert to xml');
        }
        if (count($attributes)) {
            $xmlRequest = new DOMDocument('1.0');
            $xmlRequest->loadXML($str);
            $xpath = new DOMXPath($xmlRequest);

            foreach ($attributes as $attribute) {
                $query = $attribute['query'];
                $nodeset = $xpath->query($query, $xmlRequest);

                $exec = $attribute['exec'];
                if (!isset($exec[0])) {
                    //same pattern will be applied to every result
                    foreach ($nodeset as $node) {
                        foreach ($exec as $k => $v) {
                            $node->setAttribute($k, $v);
                        }
                    }
                } else {
                    //each result will get it's own result
                    foreach ($nodeset as $i => $node) {
                        foreach ($exec[$i] as $k => $v) {
                            $node->setAttribute($k, $v);
                        }
                    }
                }
            }
            $str = $xmlRequest->saveXML();
        }


        return $str;
    }

    private function toXmlArray(&$str, $key, array $array, $level) {
        //packages
        $char = ""; //"\t";
        $line = ""; //"\r\n";

        if ($this->isAssoc($array)) {
            if ($key != '')
                $str .= str_repeat($char, $level) . "<" . htmlspecialchars($key) . ">$line";
            //package
            foreach ($array as $xkey => $value) {
                if (is_string($value)) {
                    $str .= str_repeat($char, $level + 1) . "<" . htmlspecialchars($xkey) . ">";
                    $str .= htmlspecialchars($value);
                    $str .= "</" . htmlspecialchars($xkey) . ">$line";
                } else if (is_array($value)) {
                    //array of packages, key is package
                    $this->toXmlArray($str, $xkey, $value, $level + 1);
                }
            }
            if ($key != '')
                $str .= str_repeat($char, $level) . "</" . htmlspecialchars($key) . ">$line";
        } else {
            //not assoc
            foreach ($array as $value) {
                if (is_string($value)) {
                    $str .= str_repeat($char, $level + 0) . "<" . htmlspecialchars($key) . ">";
                    $str .= htmlspecialchars($value);
                    $str .= "</" . htmlspecialchars($key) . ">$line";
                } else if (is_array($value)) {
                    //single package
                    if ($key != '')
                        $str .= str_repeat($char, $level) . "<" . htmlspecialchars($key) . ">$line";
                    $this->toXmlArray($str, '', $value, $level + 0);
                    if ($key != '')
                        $str .= str_repeat($char, $level) . "</" . htmlspecialchars($key) . ">$line";
                }
            }
        }
    }

    private function isAssoc($arr) {
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    /**
     * <p>holds xpath compatible queries</p>
     * <p>Format:</p>
     * <pre>
      $this-&gt;_queryDetails[xpath_selector] = array(
      &nbsp;&nbsp;&nbsp;&nbsp;'query' =&gt; xpath_selector,
      &nbsp;&nbsp;&nbsp;&nbsp;'exec' =&gt; indexed or assoc array of operations,
      );
     * </pre>
     * <p>indexed operations will change XML attributes based on the order of XML tags appearing</p>
     * <p>assoc array operation will change XML attributes on all matching XML tags</p>
     * @var array
     */
    protected $_queryDetails = array();

    /**
     * <p>Converts assoc array to SoapVar and fills up XML attributes, if they are contained in <code>$variable</code></p>
     * <p>Sample input:</p>
     * <pre>
     * 
     * 
     * $variable = array(
      &nbsp;&nbsp;&nbsp;&nbsp;'pick_up_time' =&gt; array(
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'@attributes' =&gt; array(
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'start' =&gt; '2011-08-10T12:15:00',
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'finish' =&gt; '2011-08-10T14:30:00',
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;),
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'worker' =&gt; 'Töötaja nimi',
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'extra' =&gt; array(
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;0 =&gt; array(
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'@attributes' =&gt; array(
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'extra_var' =&gt; '3',
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;),
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;),
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;1 =&gt; array(
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'@attributes' =&gt; array(
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'extra_var' =&gt; '',
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;),
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;),
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;),
      &nbsp;&nbsp;&nbsp;&nbsp;),
      );
     * </pre>
     * <p>Results in following XML:</p>
     * <pre>
      &lt;pick_up_time start=&quot;2011-08-10T12:15:00&quot; finish=&quot;2011-08-10T14:30:00&quot;&gt;
      &nbsp;&nbsp;&nbsp;&nbsp;&lt;worker&gt;Töötaja nimi&lt;/worker&gt;
      &nbsp;&nbsp;&nbsp;&nbsp;&lt;extra extra_var=&quot;3&quot; /&gt;
      &nbsp;&nbsp;&nbsp;&nbsp;&lt;extra extra_var=&quot;&quot; /&gt;
      &lt;/pick_up_time&gt;
     * </pre>
     * @param array $variable
     * @param array $attributes SoapVar does not support XML attributes, you need to overwrite SoapClient class and apply attributes as xpath queries
     * @param string $namespace
     * @return \SoapVar
     */
    public function toSoapVar($variable, array &$attributes = array(), $namespace = null) {
        $this->_queryDetails = array();
        $result = array();
        if (is_array($variable)) {
            $this->_toSoapVar($result, $variable, $namespace, -1, '');
            $attributes = $this->_queryDetails;


            return $result;
        } else {
            return new SoapVar($variable, XSD_STRING, null, $namespace, '');
        }
    }

    /**
     * <p>Recursive function to convert <code>$variable</code> into SoapVar equivalent.</p>
     * @param SoapVar $result result will be stored here
     * @param array $variable assoc array which would be converted to <code>SoapVar</code>
     * @param type $namespace SoapVar xml namespace
     * @param int $level depth level
     * @param string $parentKey parent XML node name
     * @param array $parents first element is root XML node name, last element is direct parents XML node name
     */
    private function _toSoapVar(&$result, $variable, $namespace, $level, $parentKey, $parents = array()) {
        $tmpResult = array();

        $attributesSymbol = '@attributes';

        //handle case, where 'nodename' => 'nodevalue'
        if (is_array($variable) && $this->_isAssoc($variable)) {
            //such a case is only case when attributes can be contained
            $attributes = is_array($variable) && isset($variable[$attributesSymbol]) ? $variable[$attributesSymbol] : array();
            if (is_array($variable) && isset($variable[$attributesSymbol])) {
                unset($variable[$attributesSymbol]);
            }


            if (count($attributes)) {
                //add xpath compatible query details in order to be able to fill up XML attributes afterwards
                $this->_addQuery('//' . implode('/', $parents), $attributes);
            }

            foreach ($variable as $key => $value) {
                if (is_array($value) && $this->_isAssoc($value)) {
                    //regular assoc array recursion, where $key is corresponding parent

                    $tmpParents = $parents;
                    $tmpParents[] = $key;
                    $this->_toSoapVar($tmpResult[], $value, $namespace, $level + 1, $key, $tmpParents);
                } else if (is_array($value) && !$this->_isAssoc($value)) {
                    //indexed array recursion, where each subvalue shares same $key parent
                    $tmpVar = array();
                    $tmpParents = $parents;
                    $tmpParents[] = $key;
                    foreach ($value as $subValue) {
                        $this->_toSoapVar($tmpResult[], $subValue, $namespace, $level + 1, $key, $tmpParents);
                    }
                } else {
                    //no recursion, create XML string SoapVar element
                    if ($value !== null) {
                        $tmpResult[] = new SoapVar($value, XSD_STRING, null, $namespace, $key);
                    }
                }
            }
        } else if (is_array($variable) && !$this->_isAssoc($variable)) {
            //handle indexed array recursion, where $parentKey is shared among the children

            foreach ($variable as $value) {
                if (is_array($value)) {
                    $tmpParents = $parents;
                    $tmpParents[] = $parentKey;


                    $this->_toSoapVar($tmpResult[], $value, $namespace, $level + 0, $parentKey, $tmpParents);
                } else {
                    //no recursion, create XML string SoapVar element
                    if ($value !== null) {
                        $tmpResult[] = new SoapVar($value, XSD_STRING, null, $namespace, $parentKey);
                    }
                }
            }
        } else if (is_string($variable)) {
            $tmpResult[] = new SoapVar($variable, XSD_STRING, null, $namespace, $parentKey);
        }
        //once iterated, put together SoaVar object from the collected $result
        $result = new SoapVar((object) $tmpResult, SOAP_ENC_OBJECT, null, $namespace, $parentKey);
    }

    /**
     * <p>Returns true, if <code>$arr</code> is associative array false otherwise</p>
     * @param array $arr
     * @return bool
     */
    private function _isAssoc($arr) {
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    /**
     * <p>Adds xpath compatible queryes based on <code>$query</code> xpath and array of <code>$operations</code></p>
     * @param string $query
     * @param array $operations
     */
    protected function _addQuery($query, $operations) {
        if (isset($this->_queryDetails[$query])) {
            $item = $this->_queryDetails[$query];

            //create multi item result so each sequential node would get it's own result
            if (!isset($item['exec'][0])) {
                $item['exec'] = array($item['exec']);
            }
            $item['exec'][] = $operations;
        } else {
            $item = array(
                'query' => $query,
                'exec' => $operations,
            );
        }
        $this->_queryDetails[$query] = $item;
    }

}
