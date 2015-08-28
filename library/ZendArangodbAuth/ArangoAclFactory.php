<?php

namespace ZendArangodbAuth;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ArangoAclFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator) {
        $oh = $serviceLocator->get('arango-document-handler');
        $acl = new ArangoAcl($oh);
        return $acl;
    }
}
