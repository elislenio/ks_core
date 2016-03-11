<?php
namespace Ks\CoreBundle\DependencyInjection;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ks_core');

        $rootNode
            ->children()
				->scalarNode('user_management')->end()
				->scalarNode('pwd_management')->end()
				->scalarNode('login_security')->end()
				->integerNode('login_security_threshold')->end()
				->scalarNode('ldap_user_dn')->end()
            ->end()
        ;
		
		/*
		->arrayNode('twitter')
			->children()
				->integerNode('client_id')->end()
				->scalarNode('client_secret')->end()
			->end()
		->end() // twitter
		*/
				
        return $treeBuilder;
    }
}