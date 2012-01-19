<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Simple_Xml_Handler
{
	/**
	 * return attribute for a node as an array
	 *
	 * @param simple XML node $sXMLNode
	 */
	static function gttributes($sXMLNode)
	{
		$attributes = array();
		foreach ($sXMLNode->attributes() as $key => $val)
		{
			$attributes[$key] = strval($val);
		}
		return $attributes;
	}
		
	/**
	 * Add an node to the $parent simpleXML document
	 *
	 * @param simpleXML object $parent
	 * @param string $name
	 * @param string $value
	 * @return simpleXML object
	 */
	static function addChild($parent, $name, $value='')
	{		
		$new_child = new SimpleXMLElement("<$name>$value</$name>");
		$node1 = dom_import_simplexml($parent);
		$dom_sxe = dom_import_simplexml($new_child);
		$node2 = $node1->ownerDocument->importNode($dom_sxe, true);
		$node1->appendChild($node2);
		return simplexml_import_dom($node2);
	}
	
	static function importNode($parent, $xmlObject)
	{
		$node1 = dom_import_simplexml($parent);
		$dom_sxe = dom_import_simplexml($xmlObject);
		$node2 = $node1->ownerDocument->importNode($dom_sxe, true);
		$node1->appendChild($node2);
		return simplexml_import_dom($node2);
	}

	/**
	 * Add an attribute to the $parent simpleXML document
	 *
	 * @param simpleXML object $parent
	 * @param string $name
	 * @param string $value
	 * @return simpleXML object
	 */
	static function addAttribute($parent, $name, $value='')
	{
		$node = dom_import_simplexml($parent);
		$node->setAttribute($name,$value);
		return simplexml_import_dom($node);
	}
	
	/**
	 * Wrap CDATA tag around a string
	 *
	 * @param unknown_type $string
	 * @return string
	 */
	static function addCDataTag($string)
	{
		return "<![CDATA[" . $string . "]]>";
	}
	
	/**
	 * Output Simple XML object as XML to screen
	 *
	 * @param unknown_type $sXmlObject
	 */
	static function outputXml($sXmlObject, $http_code)
	{
		$output = $sXmlObject->asXML();
		header('HTTP/1.1: ' . $http_code);
		header('Status: ' . $http_code);
		header('Content-Length: ' . strlen($output));

		exit($output);
	}
	
	
}
