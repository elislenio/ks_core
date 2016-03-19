<?php 
namespace Ks\CoreBundle\Services;

use RecursiveIteratorIterator;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Knp\Menu\MenuFactory;
use Knp\Menu\Iterator\RecursiveItemIterator;
use Doctrine\ORM\EntityManager;

class KnpMenuBuilder
{
	private $em;
	private $router;
	private $factory;
	private $knpmenu;
	
	/**
     * Constructor
     */
    public function __construct(Router $router, EntityManager $em)
    {
		$this->router = $router;
		$this->em = $em;
		$this->factory = new MenuFactory();
    }
	
	private function getChilds($user_id, $menu_id, $parent_id, $parentItem)
	{
		if ($user_id)
		{
			$stmt = $this->em->getConnection()->prepare('select c.*
	from ks_menu_item c
	left join (
		select b.ac_id
		from ks_user_role a
		inner join ks_acl b on a.role_id = b.role_id
		where a.user_id = ?
	) d on c.ac_id = d.ac_id
	where c.menu_id = ?
	and c.parent_id = ?
	and c.visible = 1
	and (
		c.is_branch = 1
		or c.ac_id is null
		or c.ac_id = d.ac_id
	)
	order by c.item_order asc');

			$stmt->bindValue(1, $user_id);
			$stmt->bindValue(2, $menu_id);
			$stmt->bindValue(3, $parent_id);
			$stmt->execute();
			$records = $stmt->fetchAll();
		}
		else
		{
			$qb = $this->em->getConnection()->createQueryBuilder();
			$records = $qb->select('*')
				->from('ks_menu_item', 'a')
				->andWhere('a.menu_id = ?')
				->andWhere('a.parent_id  = ?')
				->setParameter(0, $menu_id)
				->setParameter(1, $parent_id)
				->addOrderBy('a.item_order', 'asc')
				->execute()->fetchAll();
		}
		
		if ($records)
			foreach ($records as $i)
			{
				// Item options
				$options = array();
				$options['label'] = $i['label'];
				if ($i['route']) {
					try {
						$options['uri'] = $this->router->generate($i['route']);
					} catch (RouteNotFoundException $e) {}
				}
				$options['display'] = $i['visible'];
				$options['attributes'] = array('id'=>$i['id']);
				$options['extras'] = array('data'=>$i);
				$menuItem = $this->factory->createItem($i['id'], $options);
				$parentItem->addChild($menuItem);
				
				// recursion
				if ($i['is_branch'])
					$this->getChilds($user_id, $menu_id, $i['id'], $menuItem);
			}
	}
	
	private function clearEmptyBranches()
	{
		$itemIterator = new RecursiveItemIterator($this->knpmenu);
		$iterator = new RecursiveIteratorIterator($itemIterator, RecursiveIteratorIterator::SELF_FIRST);

		foreach ($iterator as $item) 
		{
			$parent = $item->getParent();
			
			if ($item->getExtra('data')['is_branch'] && ! $item->hasChildren())
			{
				$parent->removeChild($item);
				return false;
			}
		}
		
		return true;
	}
	
	public function loadMenu($menu_id, $root_id=false, $user_id=false)
	{
		// Get the root item
		$root = $this->em->getRepository('KsCoreBundle:MenuItem')->getRootItem($menu_id);
		
		$this->knpmenu = $this->factory->createItem('root');
		if ($root_id) $this->knpmenu->setChildrenAttribute('id', $root_id);
		
		$this->getChilds($user_id, $menu_id, $root->getId(), $this->knpmenu);
		
		if ($user_id)
		{
			$cleared = false;
			while (! $cleared) $cleared = $this->clearEmptyBranches();
		}
		
		return $this->knpmenu;
	}
}