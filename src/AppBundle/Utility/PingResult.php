<?php

namespace AppBundle\Utility;

use Exception;
use GuzzleHttp\Exception\XmlParseException;
use GuzzleHttp\Message\Response;
use SimpleXMLElement;

/**
 * Capture all the information in an HTTP ping and store it for later.
 */
class PingResult {
	
	/**
	 * @var SimpleXMLElement 
	 */
	private $xml;
	
	/**
	 * HTTP Status Code
	 * @var int
	 */
	private $status;
	
	/**
	 * @var string
	 */
	private $error;
	
    /**
     * Construct a Ping response from an HTTP response.
     * 
     * @param Response $response
     */
	public function __construct(Response $response) {
		$this->status = $response->getStatusCode();
		$this->error = null;
		$this->xml = null;
		try {
			$this->xml = $response->xml();
		} catch (Exception $ex) {            
			$this->error = $ex->getMessage();
		} catch (XmlParseException $ex) {
			$this->error = $ex->getMessage();
		}
	}
	
    /**
     * Get an XML value.
     * 
     * @param string $q XPath query.
     * @return string|null.
     */
	private function simpleQuery($q) {
		if($this->xml === null) {
			return null;
		}
		$element = $this->xml->xpath($q);
		if($element) {
			return (string)$element[0];
		}
		return null;
	}
	
    /**
     * Return true if the response contained valid XML.
     * 
     * @return boolean
     */
	public function hasXml() {
		return $this->xml !== null;
	}
	
    /**
     * Return true if the response was an error.
     * 
     * @return boolean
     */
	public function hasError() {
		return $this->error !== null;
	}
	
    /**
     * Return the HTTP status code.
     * 
     * @return int
     */
	public function getHttpStatus() {
		return $this->status;
	}
	
    /**
     * Return the HTTP error if there was one.
     * 
     * @return string|null
     */
	public function getError() {
		return $this->error;
	}
	
    /**
     * Get the OJS release version.
     * 
     * @return string
     */
	public function getOjsRelease() {
		return $this->simpleQuery('/plnplugin/ojsInfo/release');
	}
	
    /**
     * Get the plugin release version.
     * 
     * @return string
     */
	public function getPluginReleaseVersion() {
		return $this->simpleQuery('/plnplugin/pluginInfo/release');
	}
	
    /**
     * Get the release date for the plugin as a string.
     * 
     * @return string
     */
	public function getPluginReleaseDate() {
		return $this->simpleQuery('/plnplugin/pluginInfo/releaseDate');
	}
	
    /**
     * Return 'Yes' if the plugin reports itself as current.
     * 
     * @return string
     */
	public function isPluginCurrent() {
		if($this->simpleQuery('/plnplugin/pluginInfo/current')) {
			return 'Yes';
		}
		return 'No';
	}
	
    /**
     * Return 'Yes' if the plugin reports the terms are accepted.
     * 
     * @return string
     */
	public function areTermsAccepted() {
		return $this->simpleQuery('/plnplugin/pluginInfo/terms/@termsAccepted');
	}
	
    /**
     * Return the journal title.
     * 
     * @return string
     */
	public function getJournalTitle() {
		return $this->simpleQuery('/plnplugin/journalInfo/title');
	}
	
    /**
     * Return the article count.
     * 
     * @return int
     */
	public function getArticleCount() {
		return $this->simpleQuery('/plnplugin/journalInfo/articles/@count');
	}
	
    /**
     * Return a list of the article titles in the ping.
     * 
     * @return array
     */
	public function getArticleTitles() {
		$articles = array();
		if($this->xml === null) {
			return $articles;
		}
		foreach ($this->xml->xpath('/plnplugin/journalInfo/articles/article') as $element) {
			$articles[] = array(
				'date' => $element['pubDate'],
				'title' => (string) $element,
			);
		}
		return $articles;
	}
}