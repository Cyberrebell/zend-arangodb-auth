<?php

namespace ZendArangodbAuth;

use Zend\Permissions\Acl\AclInterface;
use ArangoODM\ObjectHandler;
use ArangoODM\Document;

class ArangoAcl implements AclInterface
{
    protected $objectHandler;
    
    /**
     * @param ObjectHandler $oh
     */
	public function __construct(ObjectHandler $oh)
	{
		$this->objectHandler = $oh;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Zend\Permissions\Acl\AclInterface::hasResource()
	 */
	public function hasResource($resource)
	{
	    $resource = $this->objectHandler->findBy(new Document('Resource', ['name' => $resource]));
	    
	    return count($resource) == 1;
	}
	
	/**
	 * Warning! $identity must be the user _id
	 * 
	 * (non-PHPdoc)
	 * @see \Zend\Permissions\Acl\AclInterface::isAllowed()
	 */
	public function isAllowed($identity = null, $resource = null, $privilege = null)
	{
	    return (bool) $this->objectHandler->query(
	        "FOR u_r IN User_Role
               FILTER u_r._from == '" . $identity . "'
               FOR ur IN Role
                 FILTER ur._id == u_r._to
                   FOR inh_r IN GRAPH_TRAVERSAL('Role_Role', ur._id, 'outbound')
                     FOR r IN inh_r
                       FOR re IN Resource
                         FILTER re.name == '" . $resource . "'
                         FOR r_re IN Role_Resource
                           FILTER r_re._to == re._id && r_re._from == r.vertex._id
                           RETURN true",
	        false
        );
	}
}
