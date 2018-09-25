<?php namespace Jake142\Service;

class PhpunitXML
{
    /**
     * Read the phpunit.xml
     *
     * @return \DOMDocument
     */
    public function readPhpunitXml()
    {
        $domPhpunitXML = new \DOMDocument();
        $domPhpunitXML->preserveWhiteSpace = true;
        $domPhpunitXML->formatOutput = true;
        $domPhpunitXML->load(base_path('phpunit.xml'));
        return $domPhpunitXML;
    }
    /**
     * Write the phpunit.xml
     *
     * @var \DOMDocument
     */
    public function writePhpunitXml($domPhpunitXML)
    {
        $domPhpunitXML->save(base_path('phpunit.xml'));
    }
    /**
     * Add a service
     *
     */
    public function addService($id)
    {

        $domPhpunitXML = $this->readPhpunitXml();
        $phpunitXMLXPath = new \DOMXPath ( $domPhpunitXML );
        $testsuitesNode = $phpunitXMLXPath->query('/phpunit/testsuites')->item(0);
        if(!is_null($testsuitesNode)) {
            $testsuiteNode = $domPhpunitXML->createElement ('testsuite');
            $testsuiteNode->setAttribute ( 'name' , $id );
            $directoryNode = $domPhpunitXML->createElement ('directory');
            $directoryNode->setAttribute ( 'suffix' , 'Test.php');
            $directoryNodeData = $domPhpunitXML->createTextNode('./app/Services/'.$id.'/tests');
            $directoryNode->appendChild($directoryNodeData);
            $testsuiteNode->appendChild($directoryNode);
            $testsuitesNode->appendChild($testsuiteNode);
            $this->writePhpunitXml($domPhpunitXML);
        }
    }
    /**
     * Set a service
     *
     */
    public function removeService($id)
    {
        $domPhpunitXML = $this->readPhpunitXml();
        $phpunitXMLXPath = new \DOMXPath ( $domPhpunitXML );
        $testsuiteNode = $phpunitXMLXPath->query("/phpunit/testsuites/testsuite[@name='".$id."']")->item(0);
        if(!is_null($testsuiteNode)) {
            $testsuiteNode->parentNode->removeChild($testsuiteNode);
            $this->writePhpunitXml($domPhpunitXML);
        }
    }

}