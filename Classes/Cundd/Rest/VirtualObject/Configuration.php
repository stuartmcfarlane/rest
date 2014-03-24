<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 24.03.14
 * Time: 10:43
 */

namespace Cundd\Rest\VirtualObject;

/**
 * Virtual Object Configuration
 *
 * A Virtual Object Configuration is the definition of a REST resource without an associated Extbase Domain Model. This
 * allows the access to database records without the need to defined a Model class.
 *
 * @package Cundd\Rest\VirtualObject
 */
class Configuration implements ConfigurationInterface {
	/**
	 * The array that hold the configuration data
	 *
	 * @see "Resources/Private/Development/Virtual Object Configuration example.json" for the abstract schema
	 * @var array
	 */
	protected $configurationData = array();

	/**
	 * A map of all the source keys and the associated property names
	 *
	 * @var array
	 */
	protected $sourceKeyToPropertyMap = array();

	/**
	 * Whether unknown (un-configured) properties should be skipped during mapping, or throw an exception
	 *
	 * @var boolean
	 */
	protected $skipUnknownProperties = FALSE;


	function __construct($configurationData = array()) {
		$this->configurationData = $configurationData;
	}

	/**
	 * Returns TRUE if the given property name should be mapped, FALSE otherwise.
	 *
	 * @param string $propertyName
	 * @return boolean
	 */
	public function hasProperty($propertyName) {
		return isset($this->configurationData['properties'][$propertyName]);
	}

	/**
	 * Returns TRUE if the given source key is mapped
	 *
	 * Checks if one of the configured property mappings uses the given source key
	 *
	 * @param string $sourceKey
	 * @return boolean
	 */
	public function hasSourceKey($sourceKey) {
		$sourceKeyToPropertyMap = $this->getSourceKeyToPropertyMap();
		return isset($sourceKeyToPropertyMap[$sourceKey]);
	}

	/**
	 * Returns the configuration for the given property name
	 *
	 * @param string $propertyName
	 * @return array
	 */
	public function getConfigurationForProperty($propertyName) {
		return isset($this->configurationData['properties'][$propertyName])
			? $this->configurationData['properties'][$propertyName]
			: array();
	}

	/**
	 * Returns the source property (column) name for the given property name, or NULL if it isn't defined
	 *
	 * @param string $propertyName
	 * @return string
	 */
	public function getSourceKeyForProperty($propertyName) {
		if (!$this->hasProperty($propertyName)) {
			return NULL;
		}
		return isset($this->configurationData['properties'][$propertyName]['column'])
			? $this->configurationData['properties'][$propertyName]['column']
			: NULL;
	}

	/**
	 * Returns the property for the given source property (column)
	 *
	 * @param string $sourceKey
	 * @return string
	 */
	public function getPropertyForSourceKey($sourceKey) {
		$sourceKeyToPropertyMap = $this->getSourceKeyToPropertyMap();
		return isset($sourceKeyToPropertyMap[$sourceKey])
			? $sourceKeyToPropertyMap[$sourceKey]
			: NULL;
	}

	/**
	 * Returns the data type for the given property name
	 *
	 * @param string $propertyName
	 * @return string Returns one of the following: "string", "float", "int", "integer", "bool", "boolean"
	 */
	public function getTypeForProperty($propertyName) {
		if (!$this->hasProperty($propertyName)) {
			return NULL;
		}
		return isset($this->configurationData['properties'][$propertyName]['type'])
			? $this->configurationData['properties'][$propertyName]['type']
			: NULL;
	}

	/**
	 * Returns the source identifier (the database table name)
	 *
	 * @return string
	 */
	public function getSourceIdentifier() {
		return isset($this->configurationData['tableName'])
			? $this->configurationData['tableName']
			: NULL;
	}

	/**
	 * Returns a map of all the source keys and the associated property names
	 *
	 * @return array
	 */
	public function getSourceKeyToPropertyMap() {
		if (!$this->sourceKeyToPropertyMap) {
			foreach ($this->configurationData['properties'] as $propertyName => $propertyMapping) {
				$this->sourceKeyToPropertyMap[$propertyMapping['column']] = $propertyName;
			}
		}
		return $this->sourceKeyToPropertyMap;
	}

	/**
	 * Set whether unknown (un-configured) properties should be skipped during mapping, or throw an exception
	 *
	 * @param boolean $skipUnknownProperties
	 * @return $this
	 */
	public function setSkipUnknownProperties($skipUnknownProperties) {
		$this->skipUnknownProperties = $skipUnknownProperties;
		return $this;
	}

	/**
	 * Return whether unknown (un-configured) properties should be skipped during mapping, or throw an exception
	 *
	 * @return boolean
	 */
	public function shouldSkipUnknownProperties() {
		return $this->skipUnknownProperties;
	}

	/**
	 * Returns the name of the property which uniquely identifies an object
	 *
	 * @return string
	 */
	public function getIdentifier() {
		return isset($this->configurationData['identifier']) ? $this->configurationData['identifier'] : NULL;
	}


} 