<?php 
namespace Ks\CoreBundle\DependencyInjection;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\Config\FileLocator;

class KsCoreExtension extends ConfigurableExtension implements PrependExtensionInterface
{
	protected function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
		if (isset($mergedConfig['ldap_user_dn']))
			$container->setParameter('ks.core.ldap_user_dn', $mergedConfig['ldap_user_dn']);
		else
			$container->setParameter('ks.core.ldap_user_dn', "CN={username},OU=Users,DC=maxcrc,DC=com");
		
		if (isset($mergedConfig['user_management']))
			$container->setParameter('ks.core.user_management', $mergedConfig['user_management']);
		else
			$container->setParameter('ks.core.user_management', "local");
		
		if (isset($mergedConfig['pwd_management']))
			$container->setParameter('ks.core.pwd_management', $mergedConfig['pwd_management']);
		else
			$container->setParameter('ks.core.pwd_management', "local");
		
		// maybe this here ?
		//$container->setParameter('security.exception_listener.class', 'Ks\CoreBundle\Security\Firewall\ExceptionListener');
		
		$loader = new YamlFileLoader(
			$container,
			new FileLocator(__DIR__.'/../Resources/config')
		);
		
		$loader->load('services.yml');
    }
	
	public function getNamespace()
    {
        return 'http://ks.localhost/schema/dic/core';
    }
	
	public function prepend(ContainerBuilder $container)
    {
    }
}