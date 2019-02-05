<?php namespace Jake142\LaravelPods;

class PhpunitXML
{
    /**
     * Set a pod
     *
     */
    public function disablePod($pod)
    {
        $domPhpunitXML   = $this->readPhpunitXml();
        $phpunitXMLXPath = new \DOMXPath($domPhpunitXML);
        $testsuiteNode   = $phpunitXMLXPath->query("/phpunit/testsuites/testsuite[@name='".$pod."']")->item(0);
        if (!is_null($testsuiteNode)) {
            $testsuiteNode->parentNode->removeChild($testsuiteNode);
            $this->writePhpunitXml($domPhpunitXML);
        }
    }

    /**
     * Add a service
     *
     */
    public function enablePod($pod)
    {

        $domPhpunitXML   = $this->readPhpunitXml();
        $phpunitXMLXPath = new \DOMXPath($domPhpunitXML);
        $testsuitesNode  = $phpunitXMLXPath->query('/phpunit/testsuites')->item(0);
        if (!is_null($testsuitesNode)) {
            $testsuiteNode = $domPhpunitXML->createElement('testsuite');
            $testsuiteNode->setAttribute('name', $pod);
            $directoryNode = $domPhpunitXML->createElement('directory');
            $directoryNode->setAttribute('suffix', 'Test.php');
            $directoryNodeData = $domPhpunitXML->createTextNode('./pod/'.$pod.'/tests');
            $directoryNode->appendChild($directoryNodeData);
            $testsuiteNode->appendChild($directoryNode);
            $testsuitesNode->appendChild($testsuiteNode);
            $this->writePhpunitXml($domPhpunitXML);
        }
    }

    /**
     * Read the phpunit.xml
     *
     * @return \DOMDocument
     */
    public function readPhpunitXml()
    {
        $domPhpunitXML                     = new \DOMDocument();
        $domPhpunitXML->preserveWhiteSpace = true;
        $domPhpunitXML->formatOutput       = true;
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
}
