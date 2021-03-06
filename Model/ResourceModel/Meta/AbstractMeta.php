<?php
/**
 * @category		Fishpig
 * @package		Fishpig_Wordpress
 * @license		http://fishpig.co.uk/license.txt
 * @author		Ben Tideswell <help@fishpig.co.uk>
 * @info			http://fishpig.co.uk/wordpress-integration.html
 */

namespace FishPig\WordPress\Model\ResourceModel\Meta;

abstract class AbstractMeta extends \FishPig\WordPress\Model\ResourceModel\AbstractResource
{
	/**
	 * Retrieve a meta value from the database
	 * This only works if the model is setup to work a meta table
	 * If not, null will be returned
	 *
	 * @param \FishPig\WordPress\Api\Data\Entity\MetaInterface $object
	 * @param string $metaKey
	 * @param string $selectField
	 * @return null|mixed
	 */
	public function getMetaValue(\FishPig\WordPress\Model\Meta\AbstractMeta $object, $metaKey, $selectField = 'meta_value')
	{
		$select = $this->getConnection()
			->select()
			->from($object->getMetaTable(), $selectField)
			->where($object->getMetaTableObjectField() . '=?', $object->getId())
			->where('meta_key=?', $metaKey)
			->limit(1);

		if (($value = $this->getConnection()->fetchOne($select)) !== false) {
			return trim($value);
		}
			
		return false;
	}

	/**
	 * Save a meta value to the database
	 * This only works if the model is setup to work a meta table
	 *
	 * @param \FishPig\WordPress\Api\Data\Entity\MetaInterface $object
	 * @param string $metaKey
	 * @param string $metaValue
	 */
	public function setMetaValue(\FishPig\WordPress\Api\Data\Entity\MetaInterface $object, $metaKey, $metaValue)
	{
		$metaValue = trim($metaValue);
		$metaData = array(
			$object->getMetaTableObjectField() => $object->getId(),
			'meta_key' => $metaKey,
			'meta_value' => $metaValue,
		);
						
		if (($metaId = $this->getMetaValue($object, $metaKey, $object->getMetaPrimaryKeyField())) !== false) {
			$this->_getWriteAdapter()->update($object->getMetaTable(), $metaData, $object->getMetaPrimaryKeyField() . '=' . $metaId);
		}
		else {
			$this->_getWriteAdapter()->insert($object->getMetaTable(), $metaData);
		}
	}
	
	/**
	 * Get an array of all of the meta values associated with this post
	 *
	 * @param \FishPig\WordPress\Api\Data\Entity\MetaInterface $object
	 * @return false|array
	 */
	public function getAllMetaValues(\FishPig\WordPress\Api\Data\Entity\MetaInterface $object)
	{
		$select = $this->getConnection()
			->select()
			->from($object->getMetaTable(), array('meta_key', 'meta_value'))
			->where($object->getMetaTableObjectField() . '=?', $object->getId());

		if (($values = $this->getConnection()->fetchPairs($select)) !== false) {
			return $values;
		}
		
		return false;
	}
}
