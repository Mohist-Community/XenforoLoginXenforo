<?php

namespace XenforoLoginXenforo\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;
/**
 * COLUMNS
 * @property int user_id
 * @property int time
 * @property string accessToken
 * @property string clientToken
 */
class XenforoLoginToken extends Entity
{
	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_xenforo_login_token';
		$structure->shortName = 'XenforoLogin:XenforoLoginToken';
		$structure->primaryKey = 'user_id';
		$structure->columns = [
			'user_id' => ['type' => self::INT, 'required' => true],
			'time' => ['type' => self::UINT, 'default' => time()],
            'accessToken'=>['type'=>self::STR,'required' => true],
            'clientToken'=>['type'=>self::STR,'required' => true],
		];
		$structure->getters = [];
		$structure->relations = [
			'User' => [
				'entity' => 'XF:User',
				'type' => self::TO_ONE,
				'conditions' => 'user_id',
				'primary' => true
			],
		];

		return $structure;
	}
}